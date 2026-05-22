<div x-data="{ show: true }" x-show="show"
     x-init="setTimeout(() => { show = false }, {{ $duration ?? 5000 }})"
     role="alert"
     class="flex items-center gap-3 px-4 py-3 rounded-lg border-l-4 transition-all duration-300"
     :class="{
         'bg-success/10 border-success text-emerald-300': '{{ $type ?? 'success' }}' === 'success',
         'bg-danger/10 border-danger text-red-300': '{{ $type ?? 'success' }}' === 'error' || '{{ $type ?? 'success' }}' === 'danger',
         'bg-amber-500/10 border-accent text-amber-300': '{{ $type ?? 'success' }}' === 'warning',
         'bg-primary/10 border-primary text-indigo-300': '{{ $type ?? 'success' }}' === 'info',
     }">
    <span class="text-lg flex-shrink-0">
        @switch($type ?? 'success')
            @case('success') ✓ @break
            @case('error') @case('danger') ✕ @break
            @case('warning') ⚠ @break
            @default ℹ
        @endswitch
    </span>
    <span class="text-sm flex-1">{{ $slot }}</span>
    <button @click="show = false" class="text-current opacity-60 hover:opacity-100 text-lg leading-none">&times;</button>
</div>