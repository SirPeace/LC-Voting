<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class StatusFilters extends Component
{
    /**
     * @var string|null $status 'considering'|'in_progress'|'implemented'|'closed'
     */
    public ?string $status = '';

    protected $queryString = [
        'status' => ['except' => ''],
    ];

    public function mount(): void
    {
        if (Route::currentRouteName() !== 'idea.index') {
            $this->queryString = [];
            $this->status = null;
        }
    }

    public function updating($name, $value)
    {
        // if ($this->getPreviousRouteName() !== 'idea.index' && $name === 'status') {
        return redirect()->to(
            route('idea.index', ['status' => $value])
        );
        // }
    }

    public function render(): View
    {
        return view('livewire.status-filters');
    }

    private function getPreviousRouteName(): string
    {
        return Route::getRoutes()->match(
            request()->create(url()->previous())
        )->getName();
    }
}
