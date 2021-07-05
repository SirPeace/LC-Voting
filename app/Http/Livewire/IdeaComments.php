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
    public Idea $idea;

    protected $listeners = ['ideaCommentCreated'];

    public function ideaCommentCreated()
    {
        $this->idea->refresh();
        $this->comments = $this->idea->comments;
    }

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
        $this->comments = $idea->comments;
    }

    public function render()
    {
        return view('livewire.idea-comments');
    }
}
