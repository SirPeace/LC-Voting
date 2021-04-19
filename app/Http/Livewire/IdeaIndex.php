<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use App\Models\User;
use Livewire\Component;

class IdeaIndex extends Component
{
    public Idea $idea;
    public ?User $user = null;
    public int $votesCount;
    public bool $isVoted;

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
        $this->user = auth()->user();
        $this->votesCount = $idea->votes_count;
        $this->isVoted = (bool) $this->idea->voted_by_user;
    }

    public function vote()
    {
        if (!$this->user) {
            return redirect(route('login'));
        }

        $this->idea->vote($this->user);
        $this->isVoted = true;
        $this->votesCount += 1;
    }

    public function unvote()
    {
        $this->idea->unvote($this->user);
        $this->isVoted = false;
        $this->votesCount -= 1;
    }

    public function render()
    {
        return view('livewire.idea-index');
    }
}
