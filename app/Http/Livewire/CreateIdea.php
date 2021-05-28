<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Idea;
use App\Models\Votable;
use Illuminate\Http\Response;
use Livewire\Component;

class CreateIdea extends Component
{
    public $title;
    public $category_id = 1;
    public $description;

    protected $rules = [
        'title' => 'required|min:10',
        'category_id' => 'required|integer|exists:categories,id',
        'description' => 'required|min:10',
    ];

    public function createIdea()
    {
        if (auth()->check()) {
            $this->validate();

            $idea = Idea::create([
                'user_id' => auth()->id(),
                'category_id' => $this->category_id,
                'status_id' => 1,
                'title' => $this->title,
                'description' => $this->description,
            ]);

            Votable::create([
                'user_id' => auth()->id(),
                'votable_id' => $idea->id,
                'votable_type' => Idea::class,
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
