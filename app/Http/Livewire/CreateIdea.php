<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Idea;
use App\Models\Vote;
use Illuminate\Http\Response;
use Livewire\Component;

class CreateIdea extends Component
{
    public $title;
    public $category = 1;
    public $description;

    protected $rules = [
        'title' => 'required|min:10',
        'category' => 'required|integer',
        'description' => 'required|min:10',
    ];

    public function createIdea()
    {
        if (auth()->check()) {
            $this->validate();

            $idea = Idea::create([
                'user_id' => auth()->id(),
                'category_id' => $this->category,
                'status_id' => 1,
                'title' => $this->title,
                'description' => $this->description,
            ]);

            Vote::create([
                'user_id' => auth()->id(),
                'idea_id' => $idea->id,
            ]);

            session()->flash('success_message', 'The idea was successfuly created!');

            $this->reset();

            return redirect()->route('idea.index');
        }

        abort(Response::HTTP_FORBIDDEN);
    }

    public function render()
    {
        return view('livewire.create-idea', [
            'categories' => Category::all(),
        ]);
    }
}
