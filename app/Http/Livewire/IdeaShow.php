<?php

namespace App\Http\Livewire;

use App\Http\Livewire\Traits\WithAuthRedirects;
use App\Models\Idea;
use Livewire\Component;

class IdeaShow extends Component
{
    use WithAuthRedirects;

    public $idea;
    public $votesCount;
    public $isVoted;
    public $spamMarksCount;

    protected $listeners = [
        'ideaStatusUpdate',
        'ideaUpdate',
        'ideaWasMarkedAsSpam',
        'ideaWasMarkedAsNotSpam',
        'commentCreated'
    ];

    public function ideaStatusUpdate()
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
        $this->spamMarksCount = 0;
    }

    public function commentCreated()
    {
        $this->idea->refresh();
    }

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
        $this->votesCount = $this->idea->votes()->count();
        $this->spamMarksCount = $this->idea->spamMarks()->count();

        $this->isVoted = $this->idea->voters->contains(auth()->user());
    }

    public function vote()
    {
        if (auth()->guest()) {
            return $this->redirectToLogin();
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
            $this->idea->voters()->attach(auth()->user());
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
            $this->idea->voters()->detach(auth()->user());
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            // User did not vote for this idea
            return false;
        }
    }
}
