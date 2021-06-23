<x-app-layout>
    <div class="flex items-center">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        <a href="{{ $backURL }}" class="ml-2 font-semibold hover:underline">All ideas</a>
    </div>

    <livewire:idea-show :idea="$idea" />

    <x-idea-modals :idea="$idea" />

    <livewire:idea-comments :idea="$idea" />
</x-app-layout>
