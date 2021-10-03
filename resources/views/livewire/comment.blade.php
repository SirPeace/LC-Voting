<div
    data-comment-id="{{ $comment->id }}"
    class="
        @if ($comment->status_id) is-status-update status-{{ str_replace('_', '-', $comment->status->name) }} @endif
        comment-container relative transition duration-500 ease-in rounded-xl flex mt-4 bg-white
    "
>
    <div class="flex flex-col md:flex-row flex-1 px-4 py-6 rounded-xl">
        <div class="flex-none">
            <a href="#">
                <img src="{{ $comment->user->getAvatar() }}" alt="avatar"
                     class="w-14 h-14 rounded-xl">
            </a>
            @if ($comment->user->isAdmin())
                <div class="md:text-center uppercase text-blue text-xxs font-bold mt-1">Admin</div>
            @endif
        </div>
        <div class="w-full md:mx-4">
            <div class="text-gray-600 line-clamp-3">
                @admin
                    @if ($spamMarksCount > 0)
                        <div class="text-red-600 mb-3 line-clamp-3">
                            Spam Reports: {{ $spamMarksCount }}
                        </div>
                    @endif
                @endadmin

                @if ($comment->status_id)
                    <h4 class="text-xl font-semibold mb-3">
                        Status Changed to "{{ $comment->status->alias }}"
                    </h4>
                @endif

                <div class="mt-4 md:mt-0">
                    {{ $comment->body }}
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <div class="flex items-center text-xs text-gray-400 font-semibold space-x-2">
                    <div class="@if ($comment->user->isAdmin()) text-blue @endif font-bold text-gray-900">
                        {{ $comment->user->name }}
                    </div>
                    <div>&bull;</div>
                    @if ($comment->user_id === $comment->idea->user_id)
                        <span class="bg-gray-200 px-3 py-1 border border-gray-300 rounded-xl text-gray-500 font-bold">OP</span>
                        <div>&bull;</div>
                    @endif
                    <div>{{ $comment->created_at->diffForHumans() }}</div>
                </div>
                @auth
                    <div
                        class="flex items-center space-x-2"
                        x-data="{ isOpen: false }"
                    >
                        <div class="relative">
                            <button
                                    class="relative bg-gray-100 hover:bg-gray-200 border rounded-full h-7 transition duration-150 ease-in py-2 px-3"
                                    @click="isOpen = !isOpen">
                                <svg fill="currentColor" width="24" height="6">
                                    <path d="M2.97.061A2.969 2.969 0 000 3.031 2.968 2.968 0 002.97 6a2.97 2.97 0 100-5.94zm9.184 0a2.97 2.97 0 100 5.939 2.97 2.97 0 100-5.939zm8.877 0a2.97 2.97 0 10-.003 5.94A2.97 2.97 0 0021.03.06z"
                                        style="color: rgba(163, 163, 163, .5)">
                                </svg>
                            </button>
                            <ul
                                class="absolute w-44 text-left font-semibold bg-white shadow-dialog rounded-xl z-10 py-3 md:ml-8 top-8 md:top-6 right-0 md:left-0 text-black"
                                x-cloak
                                x-show.transition.origin.top.left="isOpen"
                                @click.away="isOpen = false"
                                @keydown.escape.window="isOpen = false">
                                @can('update', $comment)
                                    <li>
                                        <a
                                            data-test-id="edit-comment-link"
                                            href="#"
                                            class="hover:bg-gray-100 block transition duration-150 ease-in px-5 py-3"
                                            @click="
                                                isOpen = false
                                                Livewire.emit('setEditComment', {{ $comment->id }})
                                            "
                                        >
                                            Edit Comment
                                        </a>
                                    </li>
                                @endcan
                                @can('delete', $comment)
                                    <li>
                                        <a
                                            data-test-id="delete-comment-link"
                                            href="#"
                                            class="hover:bg-gray-100 block transition duration-150 ease-in px-5 py-3"
                                            @click="
                                                isOpen = false
                                                Livewire.emit('setDeleteComment', {{ $comment->id }})
                                            "
                                        >
                                            Delete Comment
                                        </a>
                                    </li>
                                @endcan
                                @auth
                                    @if (auth()->user()->isAdmin())
                                        <li>
                                            <a
                                                data-test-id="mark-comment-as-not-spam-link"
                                                href="#"
                                                class="hover:bg-gray-100 block transition duration-150 ease-in px-5 py-3"
                                                @click="
                                                    isOpen = false
                                                    Livewire.emit('setMarkAsNotSpamComment', {{ $comment->id }})
                                                "
                                            >
                                                Not Spam
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <a
                                                data-test-id="mark-comment-as-spam-link"
                                                href="#"
                                                class="hover:bg-gray-100 block transition duration-150 ease-in px-5 py-3"
                                                @click="
                                                    isOpen = false
                                                    Livewire.emit('setMarkAsSpamComment', {{ $comment->id }})
                                                "
                                            >
                                                Mark as Spam
                                            </a>
                                        </li>
                                    @endif
                                @endauth
                            </ul>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div> <!-- end comment-container -->
