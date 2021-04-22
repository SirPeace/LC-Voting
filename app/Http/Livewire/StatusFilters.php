<?php

namespace App\Http\Livewire;

use App\Models\Status;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class StatusFilters extends Component
{
    /**
     * @var $status 'open'|'considering'|'in_progress'|'implemented'|'closed'
     */
    public string $status = '';
    public bool $onIndexPage = true;
    public array $statusesCount;

    protected $queryString = [
        'status' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->statusesCount = Status::getStatusesCount();

        if (!$this->onIndexPage) {
            $this->queryString = [];
            $this->status = '';
        }
    }

    public function updating($name, $value)
    {
        // $this->getPreviousRouteName() !== 'idea.index' &&
        if ($name === 'status') {
            return redirect()->to(
                route('idea.index', ['status' => $value])
            );
        }
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
