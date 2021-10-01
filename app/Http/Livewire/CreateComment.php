<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Comment;
use Illuminate\Http\Response;

class CreateComment extends Component
{
    public $idea;
    public $comment = '';

    protected $rules = [
        'comment' => 'required|string|min:10',
    ];

    public function addComment()
    {
        if (auth()->guest()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->validate();

        Comment::create([
            'idea_id' => $this->idea->id,
            'user_id' => auth()->id(),
            'body' => $this->comment,
        ]);

        $this->emit('commentCreated', 'Comment was successfuly created.');
    }

    public function render()
    {
        return view('livewire.create-comment');
    }
}
