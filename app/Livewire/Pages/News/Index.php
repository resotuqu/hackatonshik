<?php

namespace App\Livewire\Pages\News;

use App\Models\NewsPost;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app', ['title' => 'Новости'])]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $category = 'all';

    #[Url]
    public string $from = '';

    #[Url]
    public string $to = '';

    #[Url]
    public string $tag = '';

    #[Computed]
    public function posts()
    {
        return NewsPost::query()
            ->published()
            ->when($this->search !== '', fn ($query) => $query->where('title', 'like', '%'.$this->search.'%'))
            ->when($this->category !== 'all', fn ($query) => $query->where('category', $this->category))
            ->when($this->from !== '', fn ($query) => $query->whereDate('published_at', '>=', $this->from))
            ->when($this->to !== '', fn ($query) => $query->whereDate('published_at', '<=', $this->to))
            ->when($this->tag !== '', fn ($query) => $query->whereJsonContains('tags', $this->tag))
            ->latest('published_at')
            ->paginate(6);
    }

    #[Computed]
    public function categories()
    {
        return NewsPost::query()
            ->published()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->all();
    }

    #[Computed]
    public function tags()
    {
        return NewsPost::query()
            ->published()
            ->pluck('tags')
            ->filter()
            ->flatten()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'category', 'from', 'to', 'tag'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        return view('pages.news.index');
    }
}
