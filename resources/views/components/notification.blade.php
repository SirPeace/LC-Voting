@props(['event' => null, 'flash' => null])

<div x-data="{
        isVisible: false,
        message: null,

        showNotification(message) {
            this.isVisible = true
            this.message = message

            setTimeout(() => {
                this.isVisible = false
            }, 5000)
        }
     }"
     x-init="
        @if ($flash)
            $nextTick(() => showNotification('{{ $flash }}'))
        @else
            Livewire.on('{{ $event }}', message => showNotification(message))
        @endif
     ">
    <div x-show="isVisible"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-x-8"
         x-transition:enter-end="opacity-100 transform translate-x-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-x-0"
         x-transition:leave-end="opacity-0 transform translate-x-8"
         class="flex justify-between items-center fixed z-10 right-0 bottom-0 transform scale-90 sm:scale-95 sm:mr-6 mb-8 w-full sm:max-w-sm shadow-lg border bg-white rounded-lg px-2 sm:px-5 py-3 sm:py-4">

        <div class="flex items-center">
            <svg class="text-green mr-2 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-semibold sm:text-base text-gray-700" x-text="message"></span>
        </div>

        <button @click="isVisible = false">
            <svg class="h-5 w-5 text-gray-500 hover:text-red" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
