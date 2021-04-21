<nav class="hidden md:flex items-center justify-between text-xs text-gray-400">
    <ul class="flex uppercase font-semibold border-b-4 pb-3 space-x-5">
        <li>
            <a
                wire:click.prevent="$set('status', '')"
                class="@if (!$status && $onIndexPage) border-blue text-gray-900 @endif
                transition duration-150 ease-in border-b-4 pb-3 hover:border-blue cursor-pointer"
            >
                All Ideas ({{ $statusesCount['all'] }})
            </a>
        </li>
        <li>
            <a
                wire:click.prevent="$set('status', 'open')"
                class="@if ($status === 'open') border-blue text-gray-900 @endif
                transition duration-150 ease-in border-b-4 pb-3 hover:border-blue cursor-pointer"
            >
                Open ({{ $statusesCount['open'] }})
            </a>
        </li>
        <li>
            <a
                wire:click.prevent="$set('status', 'considering')"
                class="@if ($status === 'considering') border-blue text-gray-900 @endif
                transition duration-150 ease-in border-b-4 pb-3 hover:border-blue cursor-pointer"
            >
                Considering ({{ $statusesCount['considering'] }})
            </a>
        </li>
        <li>
            <a
                wire:click.prevent="$set('status', 'in_progress')"
                class="@if ($status === 'in_progress') border-blue text-gray-900 @endif
                transition duration-150 ease-in border-b-4 pb-3 hover:border-blue cursor-pointer"
            >
                In Progress ({{ $statusesCount['in_progress'] }})
            </a>
        </li>
    </ul>

    <ul class="flex uppercase font-semibold border-b-4 pb-3 space-x-5">
        <li>
            <a
                wire:click.prevent="$set('status', 'implemented')"
                class="@if ($status === 'implemented') border-blue text-gray-900 @endif
                transition duration-150 ease-in border-b-4 pb-3 hover:border-blue cursor-pointer"
            >
                Implemented ({{ $statusesCount['implemented'] }})
            </a>
        </li>
        <li>
            <a
                wire:click="$set('status', 'closed')"
                class="@if ($status === 'closed') border-blue text-gray-900 @endif
                transition duration-150 ease-in border-b-4 pb-3 hover:border-blue cursor-pointer"
            >
                Closed ({{ $statusesCount['closed'] }})
            </a>
        </li>
    </ul>
</nav>
