<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use Livewire\Component;
use Illuminate\Http\Response;

class MarkCommentAsSpamModal extends Component
{
    public $comment;

    protected $listeners = ['setMarkAsSpamComment'];

    public function setMarkAsSpamComment(int $commentId): void
    {
        $this->comment = Comment::findOrFail($commentId);

        $this->emit('markAsSpamCommentModalInit');
    }

    public function markAsSpam()
    {
        if (auth()->guest()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        try {
            $this->comment->spamMarks()->attach(auth()->id());
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->emit('commentWasMarkedAsSpam', 'Comment is already marked as spam');
        }

        $this->emit('commentWasMarkedAsSpam', 'Comment was marked as spam');
    }

    public function render()
    {
        return view('livewire.mark-comment-as-spam-modal');
    }
}
