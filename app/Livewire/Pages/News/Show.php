<?php

namespace App\Livewire\Pages\News;

use App\Models\NewsPost;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public NewsPost $post;

    public function mount(NewsPost $post): void
    {
        abort_unless($post->is_published, 404);
        $this->post = $post;
    }

    #[Layout('layouts::app')]
    public function render()
    {
        return view('pages.news.show');
    }
}
