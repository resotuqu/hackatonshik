<?php

namespace App\Livewire\Pages\Admin;

use App\Livewire\Concerns\AuthorizesAdminAccess;
use App\Models\NewsPost;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Layout('layouts::app', ['title' => 'Новости — админ'])]
class News extends Component
{
    use AuthorizesAdminAccess, Toast, WithPagination;

    public string $title = '';

    public string $slug = '';

    public string $excerpt = '';

    public string $body = '';

    public ?string $published_at = null;

    public bool $is_published = false;

    public ?int $editingId = null;

    public bool $showForm = false;

    public function mount(): void
    {
        Gate::authorize('access-admin');
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->published_at = now()->format('Y-m-d\TH:i');
    }

    public function edit(int $postId): void
    {
        $post = NewsPost::query()->findOrFail($postId);
        $this->editingId = $post->id;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->excerpt = (string) ($post->excerpt ?? '');
        $this->body = (string) $post->body;
        $this->published_at = $post->published_at !== null
            ? Carbon::parse($post->published_at)->format('Y-m-d\TH:i')
            : null;
        $this->is_published = (bool) $post->is_published;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->authorizeAdminAccess();

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['boolean'],
        ]);

        if ($this->editingId) {
            $post = NewsPost::query()->findOrFail($this->editingId);
            $post->update($validated + ['category' => $post->category ?? 'Обновления']);
            $this->success('Новость обновлена.');
        } else {
            NewsPost::query()->create($validated + ['category' => 'Обновления']);
            $this->success('Новость создана.');
        }

        $this->resetForm();
    }

    public function delete(int $postId): void
    {
        $this->authorizeAdminAccess();
        NewsPost::query()->whereKey($postId)->delete();
        $this->success('Новость удалена.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['title', 'slug', 'excerpt', 'body', 'published_at', 'is_published', 'editingId', 'showForm']);
        $this->is_published = false;
    }

    public function render()
    {
        return view('pages.admin.news', [
            'posts' => NewsPost::query()->latest('updated_at')->paginate(10),
        ]);
    }
}
