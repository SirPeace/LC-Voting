<x-confirm-modal
    open-livewire-event="markAsSpamCommentModalInit"
    close-event="commentWasMarkedAsSpam"
    title="Mark comment as spam"
    description="Are you sure you want to mark this comment as spam?"
    wire-click="markAsSpam"
    action-text="Mark as spam"
/>
