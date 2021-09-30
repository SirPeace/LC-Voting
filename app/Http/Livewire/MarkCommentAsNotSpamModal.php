<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Traits\ShouldAuthorize;
use Livewire\Component;
use Illuminate\Http\Response;

class MarkCommentAsNotSpamModal extends Component
{
    use ShouldAuthorize;

    public $comment;

    protected $listeners = ['setMarkAsNotSpamComment'];

    public function setMarkAsNotSpamComment(int $commentId): void
    {
        $this->comment = Comment::findOrFail($commentId);

        $this->emit('markAsNotSpamCommentModalInit');
    }

    public function markAsNotSpam()
    {
        $this->authorize(
            fn () => optional(auth()->user())->isAdmin()
        );

        $this->comment->spamMarks()->detach();

        $this->emit('commentWasMarkedAsNotSpam', 'Comment spam marks were reset');
    }

    public function render()
    {
        return view('livewire.mark-comment-as-not-spam-modal');
    }
}
