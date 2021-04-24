<div>
    <div class="filters flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-6">
        <div class="w-full md:w-1/3">
            <select wire:model="category" name="category" id="category" class="w-full rounded-xl border-none px-4 py-2 transition duration-150 ease-in cursor-pointer">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->name }}">{{ $category->alias }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-1/3">
            <select wire:model="filter" name="other_filters" class="w-full rounded-xl border-none px-4 py-2 transition duration-150 ease-in cursor-pointer">
                <option value="">No Filter</option>
                <option value="top_voted">Top Voted</option>
                @auth
                    <option value="user_ideas">My Ideas</option>
                @endauth
            </select>
        </div>
        <div class="w-full md:w-2/3 relative">
            <input type="search" placeholder="Find an idea" class="w-full rounded-xl bg-white border-none placeholder-gray-900 px-4 py-2 pl-8 transition duration-150 ease-in">
            <div class="absolute top-0 flex itmes-center h-full ml-2">
                <svg class="w-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </div> <!-- end filters -->

    <div class="ideas-container space-y-6 my-8">
        @forelse ($ideas as $idea)
            <livewire:idea-index :idea="$idea" :key="$idea->id" />
        @empty
            <p class="text-base text-center">
                <span class="font-semibold block mb-2 text-xl">No ideas were found...</span>
                May be you should publish the one?
            </p>
        @endforelse
    </div> <!-- end ideas-container -->

    <div class="my-8">
        {{ $ideas->appends(request()->query())->links() }}
    </div>
</div>
