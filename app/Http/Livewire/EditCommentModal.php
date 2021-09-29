<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use Livewire\Component;
use App\Traits\ShouldAuthorize;

class EditCommentModal extends Component
{
    use ShouldAuthorize;

    public $comment;
    public $body;

    protected $rules = ['body' => 'string|min:10'];
    protected $listeners = ['setEditComment'];

    public function setEditComment(int $commentId): void
    {
        $this->comment = Comment::findOrFail($commentId);
        $this->body = $this->comment->body;

        $this->emit('editCommentModalInit');
    }

    public function updateComment()
    {
        $this->authorize(
            fn () => optional(auth()->user())->can('update', $this->comment)
        );

        $this->validate();

        $updateStatus = $this->comment->update([
            'body' => $this->body
        ]);

        if ($updateStatus) {
            $this->emit('commentUpdated', 'The comment was successfully updated');
        }
    }

    public function render()
    {
        return view('livewire.edit-comment-modal');
    }
}
