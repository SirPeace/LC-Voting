<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use App\Models\User;
use Livewire\Component;
use App\Models\Category;
use App\Traits\ShouldAuthorize;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class EditIdeaModal extends Component
{
    use ShouldAuthorize;

    public Idea $idea;
    public int $category_id;
    public string $title;
    public string $description;

    protected $rules = [
        'title' => 'required|min:10',
        'category_id' => 'required|integer|exists:categories,id',
        'description' => 'required|min:10',
    ];

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
        $this->title = $idea->title;
        $this->category_id = $idea->category_id;
        $this->description = $idea->description;
    }

    public function updateIdea(): void
    {
        $this->authorize(
            fn () => optional(auth()->user())->can('update', $this->idea)
        );

        $this->validate();

        try {
            $updateStatus = $this->idea->update([
                'title' => $this->title,
                'category_id' => $this->category_id,
                'description' => $this->description,
            ]);

            if ($updateStatus === true) {
                $this->emit("ideaUpdate");
            }
        } catch (QueryException $e) {
            Log::error($e->errorInfo);
        }
    }

    public function render()
    {
        return view('livewire.edit-idea-modal', [
            'categories' => Category::all()->toBase()
        ]);
    }
}
