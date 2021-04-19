<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use App\Models\Vote;
use Livewire\Component;
use Livewire\WithPagination;

class IdeasIndex extends Component
{
    use WithPagination;

    public function render()
    {
        $ideas = Idea::with('user', 'category', 'status') // eager-load relationships (n+1)
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
