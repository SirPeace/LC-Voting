<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use Livewire\Component;
use App\Traits\ShouldAuthorize;

class DeleteCommentModal extends Component
{
    use ShouldAuthorize;

    public $comment;

    protected $listeners = ['setDeleteComment'];

    public function setDeleteComment(int $commentId): void
    {
        $this->comment = Comment::findOrFail($commentId);

        $this->emit('deleteCommentModalInit');
    }

    public function deleteComment()
    {
        $this->authorize(
            fn () => optional(auth()->user())->can('delete', $this->comment)
        );

        $this->comment->delete();

        $this->emit('commentDeleted', 'Comment was successfully deleted');
    }

    public function render()
    {
        return view('livewire.delete-comment-modal');
    }
}
