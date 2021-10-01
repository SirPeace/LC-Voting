<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use App\Models\Comment;
use Livewire\Component;
use App\Jobs\NotifyVoters;
use App\Traits\ShouldAuthorize;

class SetStatus extends Component
{
    use ShouldAuthorize;

    public $idea;
    public $statusID;
    public $comment;
    public $notifyAllVoters = false;

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
        $this->statusID = $idea->status_id;
    }

    public function setStatusID()
    {
        $this->authorize(
            fn () => optional(auth()->user())->isAdmin()
        );

        $this->idea->status_id = $this->statusID;
        $this->idea->save();

        Comment::create([
            'idea_id' => $this->idea->id,
            'user_id' => auth()->id(),
            'body' => $this->comment ?: 'No comment.',
            'status_id' => $this->statusID
        ]);

        $this->emit('statusUpdate');

        if ($this->notifyAllVoters) {
            NotifyVoters::dispatch($this->idea);
        }
    }

    public function render()
    {
        return view('livewire.set-status');
    }
}
