<?php

namespace App\Http\Livewire;

use App\Jobs\NotifyVoters;
use App\Models\Idea;
use Illuminate\Http\Response;
use Livewire\Component;

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
        if (auth()->guest() || !auth()->user()?->isAdmin()) {
            return Response::HTTP_FORBIDDEN;
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
