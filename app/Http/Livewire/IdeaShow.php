<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use App\Models\User;
use App\Voter;
use Livewire\Component;

class IdeaShow extends Component
{
    public Idea $idea;
    public ?User $user = null;
    public int $votesCount;
    public bool $isVoted;

    protected $listeners = ['statusUpdate'];

    public function statusUpdate()
    {
        $this->idea->refresh();
    }

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
        $this->user = auth()->user();
        $this->votesCount = intval($this->idea->votes()->count());

        $this->isVoted = $this->getVoter()->isVotedBy($this->user);
    }

    public function vote()
    {
        if (!$this->user) {
            return redirect(route('login'));
        }

        $this->getVoter()->vote($this->user);
        $this->isVoted = true;
        $this->votesCount += 1;
    }

    public function unvote()
    {
        $this->getVoter()->unvote($this->user);
        $this->isVoted = false;
        $this->votesCount -= 1;
    }

    public function render()
    {
        return view('livewire.idea-show');
    }

    private function getVoter(): Voter
    {
        return new Voter($this->idea);
    }
}
