<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Illuminate\Http\Response;
use Livewire\Component;

class MarkIdeaAsSpamModal extends Component
{
    public Idea $idea;

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
    }

    public function markAsSpam()
    {
        if (auth()->guest()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->idea->markAsSpam(auth()->user());

        $this->emit('ideaWasMarkedAsSpam', 'Idea was marked as spam');
    }

    public function render()
    {
        return view('livewire.mark-idea-as-spam-modal');
    }
}
