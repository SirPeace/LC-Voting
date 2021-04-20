<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use App\Models\Status;
use App\Models\Vote;
use Livewire\Component;
use Livewire\WithPagination;

class IdeasIndex extends Component
{
    // use WithPagination;

    public function render()
    {
        $statuses = Status::pluck('id', 'name');

        $ideas = Idea::with('user', 'category', 'status') // eager-load relationships (n+1)
            ->when(
                request()->status,
                function ($query) use ($statuses) {
                    return $query->where('status_id', $statuses[request()->status]);
                }
            )
            ->addSelect([ // check if user voted for idea (n+1)
                'voted_by_user' => Vote::select('id')
                    ->where('user_id', auth()->id())
                    ->whereColumn('idea_id', 'ideas.id')
            ])
            ->withCount('votes') // get votes count (n+1)
            ->latest('id')
            ->simplePaginate(Idea::PAGINATION_COUNT);

        return view('livewire.ideas-index', compact('ideas'));
    }
}
