<?php

namespace App\Http\Livewire;

use App\Models\Status;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class StatusFilters extends Component
{
    /**
     * @var $status ''|'open'|'considering'|'in_progress'|'implemented'|'closed'
     */
    public string $status;
    public bool $onIndexPage = true;
    public array $statusesCount;

    public function mount(): void
    {
        $this->statusesCount = Status::getStatusesCount();
        $this->status = request()->status ?? '';

        if (!$this->onIndexPage) {
            $this->status = '';
        }
    }

    public function updatingStatus(string $value)
    {
        $this->emit('updateQueryStringStatus', $value);

        if ($this->getPreviousRouteName() !== 'idea.index') {
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
