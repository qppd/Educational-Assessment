<div x-data="{ show: false }"
     x-show="show"
     x-cloak
     @@keydown.escape.window="show = false"
     @@open-modal.window="if ($event.detail === '{{ $id }}') show = true"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     role="dialog"
     aria-modal="true">
    <!-- Backdrop -->
    <div @@click="show = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

    <!-- Modal Panel -->
    <div @@click.stop
         @class([
             'relative w-full bg-surface rounded-2xl border border-white/5 shadow-2xl max-h-[90vh] overflow-y-auto',
             'max-w-sm' => $size === 'sm',
             'max-w-md' => ($size === 'md' || $size === ''),
             'max-w-lg' => $size === 'lg',
             'max-w-2xl' => $size === 'xl',
         ])
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
            <h3 class="text-lg font-semibold text-gray-200">{{ $title }}</h3>
            <button @@click="show = false"
                    class="text-gray-400 hover:text-white transition-colors p-1 rounded-lg hover:bg-white/5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="px-6 py-4">
            {{ $slot }}
        </div>
    </div>
</div>