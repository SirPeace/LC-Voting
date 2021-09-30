<?php

namespace App\Http\Livewire;

use App\Models\Comment as CommentModel;
use Livewire\Component;

class Comment extends Component
{
    public $comment;
    public $spamMarksCount;

    protected $listeners = [
        'commentUpdated',
        'commentWasMarkedAsSpam',
        'commentWasMarkedAsNotSpam',
    ];

    public function commentUpdated(): void
    {
        $this->comment->refresh();
    }

    public function commentWasMarkedAsSpam(): void
    {
        $this->comment->refresh();
    }

    public function commentWasMarkedAsNotSpam(): void
    {
        $this->spamMarksCount = 0;
    }

    public function mount(CommentModel $comment)
    {
        $this->comment = $comment;
        $this->spamMarksCount = $comment->spamMarks()->count();
    }

    public function render()
    {
        return view('livewire.comment');
    }
}
