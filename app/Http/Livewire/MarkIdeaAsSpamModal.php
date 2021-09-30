<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Illuminate\Http\Response;
use Livewire\Component;

class MarkIdeaAsSpamModal extends Component
{
    public $idea;

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
    }

    public function markAsSpam()
    {
        if (auth()->guest()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        try {
            $this->idea->spamMarks()->attach(auth()->id());
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->emit('ideaWasMarkedAsSpam', 'Idea is already marked as spam');
        }

        $this->emit('commentWasMarkedAsSpam', 'Idea was marked as spam');
    }

    public function render()
    {
        return view('livewire.mark-idea-as-spam-modal');
    }
}
