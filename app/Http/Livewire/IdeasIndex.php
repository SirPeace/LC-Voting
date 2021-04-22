<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Idea;
use App\Models\Status;
use App\Models\Vote;
use Livewire\Component;
use Livewire\WithPagination;

class IdeasIndex extends Component
{
    use WithPagination;

    public string $status = '';
    public string $category = '';

    protected $queryString = [
        'status' => ['except' => ''],
        'category' => ['except' => ''],
    ];

    protected $listeners = ['updateQueryStringStatus'];

    public function updateQueryStringStatus(string $value)
    {
        $this->resetPage();
        $this->status = $value;
    }

    public function render()
    {
        $categories = Category::all();

        $ideas = Idea::with('user', 'category', 'status') // eager-load relationships (n+1)
            ->when(
                $this->status,
                function ($query) {
                    $statuses = Status::pluck('id', 'name');
                    return $query->where('status_id', $statuses[$this->status]);
                }
            )
            ->when(
                $this->category,
                function ($query) use ($categories) {
                    $categories = $categories->pluck('id', 'name');
                    return $query->where('category_id', $categories[$this->category]);
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

        return view('livewire.ideas-index', compact('ideas', 'categories'));
    }
}
