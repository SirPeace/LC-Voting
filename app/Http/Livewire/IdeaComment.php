<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\IdeaComment as IdeaCommentModel;

class IdeaComment extends Component
{
    public IdeaCommentModel $comment;

    public function render()
    {
        return view('livewire.idea-comment');
    }
}
