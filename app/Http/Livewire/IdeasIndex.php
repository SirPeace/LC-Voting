<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;
use App\Repositories\IdeaRepository;

class IdeasIndex extends Component
{
    use WithPagination;

    public $status = '';
    public $category = '';
    public $filter = '';
    public $search = '';

    protected $queryString = [
        'status' => ['except' => ''],
        'category' => ['except' => ''],
        'filter' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    protected $listeners = ['updateQueryStringStatus'];

    public function updateQueryStringStatus(string $value)
    {
        $this->status = $value;
        $this->resetPage();
    }

    public function updating(string $name, $value)
    {
        $this->resetPage();
    }

    public function render()
    {
        $ideas = IdeaRepository::getIdeasForIndex(
            category: $this->category,
            filter: $this->filter,
            search: $this->search,
            status: $this->status,
        );

        $categories = Category::all()->toBase();

        return view('livewire.ideas-index', compact('ideas', 'categories'));
    }
}
