@component('mail::message')
# Idea Status Updated

The idea: **{{ $idea->title }}**<br>
by: **{{ $idea->user->name }}**

has been updated to a status: *{{ $idea->status->alias }}*

@component('mail::button', ['url' => route('idea.show', $idea)])
View Idea
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent
