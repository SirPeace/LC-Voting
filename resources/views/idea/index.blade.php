<x-app-layout>
    <x-notification message="Idea was successfully deleted" :flash="session('ideaDelete')" />
    <x-notification message="Idea was successfully created" :flash="session('ideaCreate')" />

    <livewire:ideas-index />
</x-app-layout>
