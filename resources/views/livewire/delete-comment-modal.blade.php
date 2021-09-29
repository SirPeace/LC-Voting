<x-confirm-modal
    open-livewire-event="deleteCommentModalInit"
    close-event="commentDeleted"
    title="Delete comment"
    description="Are you sure you want to delete this comment? It can't be restored later."
    wire-click="deleteComment"
    action-text="Delete comment"
/>
