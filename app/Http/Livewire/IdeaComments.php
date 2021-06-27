<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use App\Models\IdeaComment;
use Livewire\Component;
use Livewire\WithPagination;

class IdeaComments extends Component
{
    use WithPagination;

    public Idea $idea;

    protected $listeners = ['ideaCommentCreated'];

    public function ideaCommentCreated()
    {
        $this->idea->refresh();

        $lastPaginationPageUrl = $this->idea
            ->comments()
            ->paginate()
            ->lastPage();

        $this->gotoPage($lastPaginationPageUrl);
    }

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
    }

    public function render()
    {
        $comments = IdeaComment::with('user')
            ->where('idea_id', $this->idea->id)
            ->paginate()
            ->withQueryString();

        return view('livewire.idea-comments', compact('comments'));
    }
}
