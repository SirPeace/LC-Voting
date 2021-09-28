<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use App\Models\User;
use Livewire\Component;

class IdeaShow extends Component
{
    public $idea;
    public $user = null;
    public $votesCount;
    public $isVoted;

    protected $listeners = [
        'statusUpdate',
        'ideaUpdate',
        'ideaWasMarkedAsSpam',
        'ideaWasMarkedAsNotSpam',
        'ideaCommentCreated'
    ];

    public function statusUpdate()
    {
        $this->idea->refresh();
    }

    public function ideaUpdate()
    {
        $this->idea->refresh();
    }

    public function ideaWasMarkedAsSpam()
    {
        $this->idea->refresh();
    }

    public function ideaWasMarkedAsNotSpam()
    {
        $this->idea->refresh();
    }

    public function ideaCommentCreated()
    {
        $this->idea->refresh();
    }

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
        $this->user = auth()->user();
        $this->votesCount = intval($this->idea->votes()->count());

        $this->isVoted = $this->idea->voters->contains($this->user);
    }

    public function vote()
    {
        if (!$this->user) {
            return redirect(route('login'));
        }

        if ($this->voteIdea()) {
            $this->isVoted = true;
            $this->votesCount += 1;
        }
    }

    public function unvote()
    {
        if ($this->unvoteIdea()) {
            $this->isVoted = false;
            $this->votesCount -= 1;
        }
    }

    public function render()
    {
        return view('livewire.idea-show');
    }

    /**
     * Make this user to vote for this idea
     *
     * @return void
     */
    private function voteIdea(): bool
    {
        try {
            $this->idea->voters()->attach($this->user);
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            // User already voted for this idea
            return false;
        }
    }

    /**
     * Make this user to unvote for this idea
     *
     * @return void
     */
    private function unvoteIdea(): bool
    {
        try {
            $this->idea->voters()->detach($this->user);
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            // User did not vote for this idea
            return false;
        }
    }
}
