@can('update', $idea)
    <livewire:edit-idea-modal :idea="$idea" />
@endcan

@can('delete', $idea)
    <livewire:delete-idea-modal :idea="$idea" />
@endcan

@auth
    <livewire:mark-idea-as-spam-modal :idea="$idea" />
@endauth

@admin
    <livewire:mark-idea-as-not-spam-modal :idea="$idea" />
@endadmin

@auth
    <livewire:edit-comment-modal />
@endauth

@auth
    <livewire:delete-comment-modal />
@endauth

@auth
    <livewire:mark-comment-as-spam-modal />
@endauth

@admin
    <livewire:mark-comment-as-not-spam-modal />
@endadmin
