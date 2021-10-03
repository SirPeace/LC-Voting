<?php

namespace App\Http\Livewire;

use App\Traits\ShouldAuthorize;
use Livewire\Component;

class DeleteIdeaModal extends Component
{
    use ShouldAuthorize;

    public $idea;

    public function deleteIdea()
    {
        $this->authorize(
            fn () => optional(auth()->user())->can('delete', $this->idea)
        );

        $this->idea->delete();

        session()->flash('ideaDelete', 'Idea was successfully deleted');

        return redirect()->route('idea.index');
    }

    public function render()
    {
        return view('livewire.delete-idea-modal');
    }
}
