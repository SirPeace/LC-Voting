<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CommentNotifications extends Component
{
    const NOTIFICATIONS_THRESHOLD = 10;

    public $notifications;
    public $notificationsCount;
    public $isLoading = true;

    protected $listeners = ['getNotifications'];

    public function getNotifications(): void
    {
        $this->notifications = optional(auth()->user())
            ->unreadNotifications()
            ->latest()
            ->take(self::NOTIFICATIONS_THRESHOLD)
            ->get();

        $this->isLoading = false;
    }

    public function mount()
    {
        $this->notifications = collect([]);
        $this->notificationsCount = optional(auth()->user())
            ->unreadNotifications()
            ->count();

        if ($this->notificationsCount > self::NOTIFICATIONS_THRESHOLD) {
            $this->notificationsCount = self::NOTIFICATIONS_THRESHOLD."+";
        }
    }

    public function render()
    {
        return view('livewire.comment-notifications');
    }
}
