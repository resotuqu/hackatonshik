<div>

    <div class="text-sm breadcrumbs mb-4">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}">Админ</a></li>
            <li class="opacity-70">Пользователи</li>
        </ul>
    </div>

    <h1 class="text-2xl font-bold mb-4">Управление пользователями</h1>

    <div class="flex flex-wrap gap-2 mb-4">
        <select class="select select-bordered select-sm" wire:model.live="roleFilter">
            <option value="">Все роли</option>
            @foreach($roles as $role)
                <option value="{{ $role->value }}">{{ $role->label() }}</option>
            @endforeach
        </select>
        <input class="input input-bordered input-sm" wire:model.live.debounce.400ms="search" placeholder="Поиск по ФИО, email, никнейму" />
    </div>

    <x-mary-card class="card card-border bg-base-100">
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ФИО</th>
                        <th>Email</th>
                        <th>Роль</th>
                        <th>Статус</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->fio }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($editingUserId === $user->id)
                                    <select class="select select-bordered select-xs" wire:model="editRole">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->value }}">{{ $role->label() }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    {{ $user->role->label() }}
                                @endif
                            </td>
                            <td>
                                @if($user->isSuspended())
                                    <span class="badge badge-error">Заблокирован</span>
                                @else
                                    <span class="badge badge-success">Активен</span>
                                @endif
                            </td>
                            <td class="text-right space-x-1">
                                @if($editingUserId === $user->id)
                                    <x-mary-button wire:click="saveRole" class="btn-xs btn-primary" label="Сохранить" />
                                @else
                                    <x-mary-button wire:click="startEditRole({{ $user->id }})" class="btn-xs btn-outline" label="Роль" />
                                @endif
                                <x-mary-button
                                    wire:click="toggleSuspension({{ $user->id }})"
                                    wire:confirm="{{ $user->isSuspended() ? 'Снять блокировку?' : 'Заблокировать пользователя?' }}"
                                    class="btn-xs {{ $user->isSuspended() ? 'btn-success' : 'btn-warning' }}"
                                    label="{{ $user->isSuspended() ? 'Разблокировать' : 'Заблокировать' }}"
                                />
                                <x-mary-button
                                    wire:click="showActivity({{ $user->id }})"
                                    class="btn-xs btn-ghost"
                                    label="История"
                                />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $users->links() }}</div>
    </x-mary-card>

    @if ($activityUser)
        <x-mary-card class="card card-border bg-base-100 mt-4">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h2 class="text-lg font-semibold">История: {{ $activityUser->fio }}</h2>
                <x-mary-button wire:click="hideActivity" class="btn-ghost btn-sm" label="Скрыть" />
            </div>
            @can('viewActivityHistory', $activityUser)
                <x-activity-timeline :subject="$activityUser" :limit="50" />
            @endcan
        </x-mary-card>
    @endif
</div>
