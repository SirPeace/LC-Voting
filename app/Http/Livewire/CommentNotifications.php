<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\Idea;
use Livewire\Component;
use Illuminate\Http\Response;
use Illuminate\Notifications\DatabaseNotification;

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

    public function getNotificationsCount(): void
    {
        $this->notificationsCount = optional(auth()->user())
            ->unreadNotifications()
            ->count();
    }

    public function markAllAsRead()
    {
        if (auth()->guest()) {
            return abort(Response::HTTP_FORBIDDEN);
        }

        auth()->user()->unreadNotifications->markAsRead();

        $this->getNotifications();
        $this->getNotificationsCount();
    }

    public function navigateAndMarkAsRead(string $notificationId)
    {
        if (auth()->guest()) {
            return abort(Response::HTTP_FORBIDDEN);
        }

        $notification = DatabaseNotification::query()
            ->findOrFail($notificationId);

        $notification->markAsRead();

        $this->navigateToComment($notification);
    }

    public function navigateToComment($notification)
    {
        $ideaSlug = @$notification->data['idea_slug'];

        if (!$ideaSlug) {
            return abort(
                Response::HTTP_FORBIDDEN,
                'Notification must have an idea slug to navigate'
            );
        }

        $idea = Idea::find($notification->data['idea_id']);
        if (!$idea) {
            session()->flash('error_message', 'The idea does not exist!');

            return redirect()->route('idea.index');
        }

        $comment = Comment::find($notification->data['comment_id']);
        if (!$comment) {
            session()->flash('error_message', 'The comment does not exist!');

            return redirect()->route('idea.index');
        }

        $ideaCommentsIds = $idea->comments()->pluck('id');

        $commentIndex = $ideaCommentsIds->search($comment->id);
        $commentPage = (int) ceil(($commentIndex + 1) / $idea->getPerPage());

        return redirect()
            ->route('idea.show', ['idea' => $ideaSlug, 'page' => $commentPage])
            ->with('scrollToComment', $comment->id);
    }

    public function mount()
    {
        $this->notifications = collect([]);
        $this->getNotificationsCount();

        if ($this->notificationsCount > self::NOTIFICATIONS_THRESHOLD) {
            $this->notificationsCount = self::NOTIFICATIONS_THRESHOLD."+";
        }
    }

    public function render()
    {
        return view('livewire.comment-notifications');
    }
}
