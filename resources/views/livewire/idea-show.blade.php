<div>
    <div class="idea-container bg-white rounded-xl flex mt-4">
        <div class="flex flex-col md:flex-row flex-1 px-4 py-6">
            <div class="flex-none mx-2 md:mx-2">
                <a href="#">
                    <img src="{{ $idea->user->getAvatar() }}" alt="avatar" class="w-14 h-14 rounded-xl">
                </a>
            </div>
            <div class="md:w-full mx-2 md:mx-4">
                <h4 class="text-xl font-semibold mt-2 md:mt-0">
                    {{ $idea->title }}
                </h4>
                @admin
                    @if ($idea->spamMarks()->count() > 0)
                        <div class="text-red-600 mt-3 line-clamp-3">
                            Spam Reports: {{ $idea->spamMarks()->count() }}
                        </div>
                    @endif
                @endadmin
                <div class="text-gray-600 mt-3">
                    {{ $idea->description }}
                </div>

                <div class="flex flex-col md:flex-row md:items-center justify-between mt-6">
                    <div class="flex items-center text-xs text-gray-400 font-semibold space-x-2">
                        <div class="hidden md:block font-bold text-gray-900">{{ $idea->user->first_name }}</div>
                        <div class="hidden md:block">&bull;</div>
                        <div>{{ $idea->created_at->diffForHumans() }}</div>
                        <div>&bull;</div>
                        <div>{{ $idea->category->alias }}</div>
                        <div>&bull;</div>
                        <div class="text-gray-900">{{ $idea->comments()->count() }} Comments</div>
                    </div>

                    <div class="flex justify-between mt-4 md:mt-0">
                        <div class="flex items-center md:hidden md:mt-0" x-data>
                            <div class="bg-gray-100 text-center rounded-xl h-10 px-4 py-2 pr-8">
                                <div class="text-sm font-bold leading-none @if ($isVoted) text-blue @endif">{{ $votesCount }}</div>
                                <div class="text-xxs font-semibold leading-none text-gray-400">Votes</div>
                            </div>

                            @if ($isVoted)
                                <button
                                        class="w-20 bg-blue hover:bg-blue-hover text-white font-bold text-xxs uppercase rounded-xl transition duration-150 ease-in px-4 py-3 -mx-5"
                                        wire:click="unvote">
                                    Voted
                                </button>
                            @else
                                <button
                                        class="w-20 bg-gray-200 border border-gray-200 font-bold text-xxs uppercase rounded-xl hover:border-gray-400 transition duration-150 ease-in px-4 py-3 -mx-5"
                                        wire:click="vote">
                                    Vote
                                </button>
                            @endif
                        </div>

                        <div
                             class="flex items-center space-x-2 md:mt-0"
                             x-data="{ isOpen: false }">
                            <div
                                 class="{{ $idea->status->getStylingClasses() }} text-xxs font-bold uppercase leading-none rounded-full text-center w-28 h-7 py-2 px-4">
                                {{ $idea->status->alias }}</div>
                            @auth
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
                                        class="absolute w-44 text-left font-semibold bg-white shadow-dialog rounded-xl z-10 py-3 md:ml-8 top-8 md:top-6 right-0 md:left-0"
                                        x-cloak
                                        x-show.transition.origin.top.left="isOpen"
                                        @click.away="isOpen = false"
                                        @keydown.escape.window="isOpen = false">
                                        @can('update', $idea)
                                            <li>
                                                <a
                                                   href="#"
                                                   class="hover:bg-gray-100 block transition duration-150 ease-in px-5 py-3"
                                                   @click="
                                                        isOpen = false
                                                        $dispatch('custom-show-edit-modal')
                                                    "
                                                >
                                                    Edit idea
                                                </a>
                                            </li>
                                        @endcan
                                        @can('delete', $idea)
                                            <li>
                                                <a
                                                   href="#"
                                                   class="hover:bg-gray-100 block transition duration-150 ease-in px-5 py-3"
                                                   @click="
                                                        isOpen = false
                                                        $dispatch('custom-show-delete-modal')
                                                   "
                                                >
                                                    Delete idea
                                                </a>
                                            </li>
                                        @endcan
                                        @auth
                                            @if (auth()->user()->isAdmin())
                                                <li>
                                                    <a href="#"
                                                       class="hover:bg-gray-100 block transition duration-150 ease-in px-5 py-3"
                                                       @click="
                                                            isOpen = false
                                                            $dispatch('custom-show-mark-idea-as-not-spam-modal')
                                                       "
                                                    >
                                                        Mark as Not Spam
                                                    </a>
                                                </li>
                                            @else
                                                <li>
                                                    <a href="#"
                                                       class="hover:bg-gray-100 block transition duration-150 ease-in px-5 py-3"
                                                       @click="
                                                            isOpen = false
                                                            $dispatch('custom-show-mark-idea-as-spam-modal')
                                                       "
                                                    >
                                                        Mark as Spam
                                                    </a>
                                                </li>
                                            @endif
                                        @endauth
                                    </ul>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end idea-container -->
    <div class="buttons-container flex items-center justify-between mt-6">
        <div class="w-full md:w-auto flex flex-col md:flex-row items-center md:space-x-4 md:ml-6">
            <livewire:create-comment :idea="$idea" />

            @admin
                <livewire:set-status :idea="$idea" />
            @endadmin
        </div>

        <div class="hidden md:flex items-center space-x-3">
            <div class="bg-white font-semibold text-center rounded-xl px-3 py-2">
                <div class="text-xl leading-snug @if ($isVoted) text-blue @endif">{{ $idea->votes()->count() }}</div>
                <div class="text-gray-400 text-xs leading-none">Votes</div>
            </div>

            @if ($isVoted)
                <button wire:click="unvote"
                        type="button"
                        class="w-32 h-11 text-xs bg-blue hover:bg-blue-hover text-white border border-blue font-semibold uppercase rounded-xl  transition duration-150 ease-in px-6 py-3">
                    Voted
                </button>
            @else
                <button
                        wire:click="vote"
                        type="button"
                        class="w-32 h-11 text-xs bg-gray-200 font-semibold uppercase rounded-xl border border-gray-200 hover:border-gray-400 transition duration-150 ease-in px-6 py-3">
                    Vote
                </button>
            @endif
        </div>
    </div> <!-- end buttons-container -->
</div>
