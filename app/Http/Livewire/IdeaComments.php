<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class IdeaComments extends Component
{
    /**
     * @var \Illuminate\Database\Eloquent\Collection<\App\Models\IdeaComment>
     */
    public Collection $comments;

    public function mount(Idea $idea)
    {
        $this->comments = $idea->comments;
    }

    public function render()
    {
        return view('livewire.idea-comments');
    }
}
