<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Comment extends Component
{
    public $comment;

    protected $listeners = [
        'commentUpdated'
    ];

    public function commentUpdated(): void
    {
        $this->comment->refresh();
    }

    public function render()
    {
        return view('livewire.comment');
    }
}
