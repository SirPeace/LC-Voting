<div>
    @if ($comments->isNotEmpty())
        <div class="comments-container relative space-y-6 md:ml-22 pt-4 my-8 mt-1">
            @foreach ($comments as $comment)
                <livewire:comment :comment="$comment" :key="$comment->id" />
            @endforeach
        </div>

        <div class="md:ml-22 my-8">
            {{ $comments->onEachSide(0)->links() }}
        </div>
    @else
        <p class="text-base text-center mt-5">
            <img src="{{ asset('/img/404.svg') }}" class="inline mb-4" />
            <span class="font-semibold block mb-2 text-xl">No comments yet...</span>
            Maybe you should add one?
        </p>
    @endif
</div>
