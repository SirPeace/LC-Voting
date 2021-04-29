<?php

namespace App\Http\Livewire;

use App\Mail\IdeaStatusChange;
use App\Models\Idea;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
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
            $this->notifyAllVoters();
        }
    }

    public function render()
    {
        return view('livewire.set-status');
    }

    private function notifyAllVoters(): void
    {
        $this->idea->votes()
            ->select('name', 'email')
            ->chunk(100, function ($voters) {
                foreach ($voters as $voter) {
                    Mail::to($voter->email)
                        ->queue(new IdeaStatusChange($this->idea));
                }
            });
    }
}
