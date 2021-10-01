<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use App\Models\Comment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class Comments extends Component
{
    use WithPagination;

    public $idea;

    protected $listeners = [
        'commentCreated',
        'commentUpdated',
        'commentDeleted',
        'statusUpdate'
    ];

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
    }

    public function commentCreated()
    {
        $this->idea->refresh();

        $this->gotoPage($this->getPaginatedComments()->lastPage());
    }

    public function commentUpdated()
    {
        $this->idea->refresh();

        $this->gotoPage($this->getPaginatedComments()->lastPage());
    }

    public function commentDeleted()
    {
        $this->idea->refresh();
    }

    public function statusUpdate()
    {
        $this->idea->refresh();

        $this->gotoPage($this->getPaginatedComments()->lastPage());
    }

    public function render()
    {
        return view('livewire.comments', [
            'comments' => $this->getPaginatedComments()
        ]);
    }

    protected function getPaginatedComments(): LengthAwarePaginator
    {
        return Comment::query()
            ->with('user') // n+1
            ->with('idea') // n+1
            ->where('idea_id', $this->idea->id)
            ->paginate();
    }
}
