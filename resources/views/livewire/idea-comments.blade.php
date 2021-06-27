<div x-data="{}"
     x-init="
        Livewire.hook('message.processed', (message, component) => {
            if (['gotoPage', 'previousPage', 'nextPage'].includes(message.updateQueue[0].method)) {
                const firstComment = document.querySelector('.comment-container:first-child')
                firstComment.scrollIntoView({ behavior: 'smooth'})
            }

            if (
                message.updateQueue[0].payload.event === 'ideaCommentCreated' &&
                message.component.fingerprint.name === 'idea-comments'
            ) {
                const lastComment = document.querySelector('.comment-container:last-child')
                lastComment.scrollIntoView({ behavior: 'smooth'})
                lastComment.classList.add('bg-green-50')

                setTimeout(() => {
                    lastComment.classList.remove('bg-green-50')
                }, 5000)
            }
        })
     ">
    @if ($comments->isNotEmpty())
        <div class="comments-container relative space-y-6 md:ml-22 pt-4 my-8 mt-1">
            @foreach ($comments as $comment)
                <livewire:idea-comment :comment="$comment" :key="$comment->id" />
            @endforeach
        </div> <!-- end comments-container -->

        <div class="my-8 md:ml-22">
            {{ $comments->onEachSide(2)->links() }}
        </div>
    @else
        <p class="text-base text-center mt-8">
            <img src="{{ asset('/img/no-ideas.svg') }}" class="inline mb-4" />
            <span class="font-semibold block mb-2">There are no comments yet...</span>
        </p>
    @endif
</div>
