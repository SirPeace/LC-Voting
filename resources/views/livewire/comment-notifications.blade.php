<div x-data="{ isOpen: false }" wire:poll="getNotificationsCount" class="relative">
    <button
        @click="
            isOpen = !isOpen
            if (isOpen) Livewire.emit('getNotifications')
        "
    >
        <svg class="h-8 w-8 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
        </svg>

        @if ($notificationsCount > 0)
            <div class="absolute rounded-full bg-red text-white text-xxs w-6 h-6 flex justify-center items-center border-2 -top-1 -right-1">
                {{ $notificationsCount }}
            </div>
        @endif
    </button>
    <ul
        class="absolute w-76 md:w-96 text-left text-gray-700 text-sm bg-white shadow-dialog rounded-xl max-h-128 overflow-y-auto z-10 -right-28 md:-right-12"
        x-cloak
        x-show.transition.origin.top="isOpen"
        @click.away="isOpen = false"
        @keydown.escape.window="isOpen = false"
    >
        @if (!$isLoading && $notifications->isNotEmpty())
            @foreach ($notifications as $notification)
                <li>
                    <a
                        href="#"
                        wire:click.prevent="navigateAndMarkAsRead('{{ $notification->id }}')"
                        @click.prevent="isOpen = false"
                        class="flex hover:bg-gray-100 transition duration-150 ease-in px-5 py-3"
                    >
                        <img
                            src="{{ $notification->data['user_avatar'] }}"
                            class="rounded-xl w-10 h-10"
                            alt="avatar"
                        >
                        <div class="ml-4">
                            <div class="line-clamp-6">
                                <span class="font-semibold">{{ $notification->data['user_name'] }}</span> commented on
                                <span class="font-semibold">{{ $notification->data['idea_title'] }}</span>:
                                <blockquote class="py-2 italic text-gray-600">{{ $notification->data['comment_body'] }}</blockquote>
                            </div>
                            <div class="text-xs text-gray-500 mt-2">{{ $notification->data['comment_created_at'] }}</div>
                        </div>
                    </a>
                </li>
            @endforeach

            <li class="border-t border-gray-300 text-center">
                <button
                    class="w-full block font-semibold hover:bg-gray-100 transition duration-150 ease-in px-5 py-4"
                    wire:click="markAllAsRead"
                >
                    Mark all as read
                </button>
            </li>
        @elseif ($isLoading)
            @foreach (range(1, 3) as $_)
                <li class="animate-pulse flex items-center px-5 py-3">
                    <div class="bg-gray-200 rounded-xl w-10 h-10"></div>
                    <div class="flex-1 ml-4 space-y-2">
                        <div class="bg-gray-200 w-full rounded h-4"></div>
                        <div class="bg-gray-200 w-full rounded h-4"></div>
                        <div class="bg-gray-200 w-1/2 rounded h-4"></div>
                    </div>
                </li>
            @endforeach
        @else
            <li class="flex justify-center align-center py-8">No new comments yet</li>
        @endif
    </ul>
</div>
