<div x-data="{ isOpen: false }"
     x-init="
        Livewire.on('ideaCommentCreated', () => {
            isOpen = false
        })

        Livewire.hook('message.processed', (message, component) => {
            console.log(message)

            if (
                message.updateQueue[0].payload.event === 'ideaCommentCreated' &&
                message.component.fingerprint.name === 'comments'
            ) {
                const lastComment = document.querySelector('.comment-container:last-child')
                lastComment.scrollIntoView({ behavior: 'smooth'})
                lastComment.classList.add('bg-green-50')

                setTimeout(() => {
                    lastComment.classList.remove('bg-green-50')
                }, 5000)
            }
        })
     "
     class="relative w-full">
    <button type="button"
            @click="
                isOpen = !isOpen

                $nextTick(() => $refs.textarea.focus())
            "
            class="w-full flex items-center justify-center h-12 md:h-11 text-sm bg-blue text-white font-semibold rounded-xl border border-blue hover:bg-blue-hover transition duration-150 ease-in px-6 py-3">
        Reply
    </button>
    <div class="absolute z-10 w-full md:w-104 text-left font-semibold text-sm bg-white shadow-dialog rounded-xl mt-2"
         @click.away="isOpen = false" @keydown.escape.window="isOpen = false" style="display: none;"
         x-show.transition.origin.top="isOpen">
        @auth
            <form action="#" wire:submit.prevent="addComment" class="space-y-4 px-4 py-6">
                <div>
                    <textarea wire:model="comment"
                              x-ref="textarea"
                              name="post_comment" id="post_comment" cols="30" rows="4"
                              class="w-full text-sm bg-gray-100 rounded-xl placeholder-gray-900 border-none px-4 py-2"
                              placeholder="Go ahead, don't be shy. Share your thoughts..."></textarea>

                    @error('comment')
                        <p class="text-red text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-row items-center space-x-3">
                    <button type="submit"
                            class="flex items-center justify-center h-11 w-1/2 text-sm bg-blue text-white font-semibold rounded-xl border border-blue hover:bg-blue-hover transition duration-150 ease-in px-6 py-3">
                        Post Comment
                    </button>
                    <button type="button"
                            class="flex items-center justify-center w-1/2 md:w-32 h-11 text-xs bg-gray-200 font-semibold rounded-xl border border-gray-200 hover:border-gray-400 transition duration-150 ease-in px-6 py-3 mt-0">
                        <svg class="text-gray-600 w-4 transform -rotate-45" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                            </path>
                        </svg>
                        <span class="ml-1">Attach</span>
                    </button>
                </div>
            </form>
        @else
            <div class="my-4 text-center px-4">
                <p class="mb-5">Please login or create an account to post a comment</p>

                <div class="sm:flex sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                    <a
                        href="{{ route('login') }}"
                        class="inline-block justify-center w-full h-11 text-xs bg-blue text-white font-semibold rounded-xl border border-blue hover:bg-blue-hover transition duration-150 ease-in px-6 py-3"
                    >
                        Log in
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="inline-block justify-center w-full h-11 text-xs bg-gray-200 font-semibold rounded-xl border border-gray-200 hover:border-gray-400 transition duration-150 ease-in px-6 py-3"
                    >
                        Register
                    </a>
                </div>
            </div>
        @endauth
    </div>
</div>
