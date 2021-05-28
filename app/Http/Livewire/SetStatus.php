<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use App\Models\User;
use Livewire\Component;
use App\Jobs\NotifyVoters;
use Illuminate\Http\Response;

class SetStatus extends Component
{
    public Idea $idea;
    public int $statusID;
    public bool $notifyAllVoters = true;

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
        $this->statusID = $idea->status_id;
    }

    public function setStatusID()
    {
        // If user is not admin abondon request
        if (auth()->guest() || !optional(auth()->user())->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->idea->status_id = $this->statusID;
        $this->idea->save();

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
