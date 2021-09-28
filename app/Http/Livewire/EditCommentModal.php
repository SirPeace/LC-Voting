<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use Illuminate\Http\Response;
use Livewire\Component;

class EditCommentModal extends Component
{
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

    public function updateComment(): void
    {
        if (
            auth()->guest() ||
            auth()->user()->cannot('update', $this->comment)
        ) {
            return abort(Response::HTTP_FORBIDDEN);
        }

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
