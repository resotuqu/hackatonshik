<?php

namespace App\Livewire\Pages\Admin;

use App\Enums\UserRole;
use App\Livewire\Concerns\AuthorizesAdminAccess;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Layout('layouts::app', ['title' => 'Пользователи — админ'])]
class Users extends Component
{
    use AuthorizesAdminAccess, Toast, WithPagination;

    #[Url]
    public string $roleFilter = '';

    #[Url]
    public string $search = '';

    public ?int $editingUserId = null;

    public ?int $activityUserId = null;

    public string $editRole = '';

    public function mount(): void
    {
        Gate::authorize('access-admin');
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function startEditRole(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        $this->editingUserId = $user->id;
        $this->editRole = $user->role->value;
    }

    public function saveRole(): void
    {
        $this->authorizeAdminAccess();

        $this->validate([
            'editRole' => ['required', 'in:admin,moderator,partner,judge,user'],
        ]);

        $user = User::query()->findOrFail($this->editingUserId);
        $user->forceFill(['role' => UserRole::from($this->editRole)])->save();

        $this->editingUserId = null;
        $this->success('Роль обновлена.');
    }

    public function toggleSuspension(int $userId): void
    {
        $this->authorizeAdminAccess();

        $user = User::query()->findOrFail($userId);

        if ($user->isAdmin() && auth()->id() === $user->id) {
            $this->error('Нельзя заблокировать свой аккаунт.');

            return;
        }

        $user->forceFill([
            'suspended_at' => $user->isSuspended() ? null : now(),
        ])->save();

        activity('user')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperty(
                'suspended_at',
                $user->suspended_at !== null ? Carbon::parse($user->suspended_at)->toIso8601String() : null
            )
            ->log('suspension_changed');

        $this->success($user->isSuspended() ? 'Пользователь заблокирован.' : 'Блокировка снята.');
    }

    public function showActivity(int $userId): void
    {
        $this->authorizeAdminAccess();

        $this->activityUserId = $userId;
    }

    public function hideActivity(): void
    {
        $this->activityUserId = null;
    }

    public function render()
    {
        $users = User::query()
            ->when($this->roleFilter !== '', fn ($q) => $q->where('role', $this->roleFilter))
            ->when($this->search !== '', function ($q): void {
                $term = '%'.$this->search.'%';
                $q->where(function ($inner) use ($term): void {
                    $inner
                        ->where('fio', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('nickname', 'like', $term);
                });
            })
            ->orderByDesc('id')
            ->paginate(15);

        return view('pages.admin.users', [
            'users' => $users,
            'roles' => UserRole::cases(),
            'activityUser' => $this->activityUserId
                ? User::query()->find($this->activityUserId)
                : null,
        ]);
    }
}
