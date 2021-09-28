<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Illuminate\Http\Response;
use Livewire\Component;

class MarkIdeaAsNotSpamModal extends Component
{
    public $idea;

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
    }

    public function markAsNotSpam()
    {
        if (auth()->guest() || !optional(auth()->user())->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->idea->spamMarks()->detach();

        $this->emit('ideaWasMarkedAsNotSpam', 'Spam marks were removed');
    }

    public function render()
    {
        return view('livewire.mark-idea-as-not-spam-modal');
    }
}
