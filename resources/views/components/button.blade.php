<button {{ $attributes->merge([
    'type' => $type ?? 'button',
    'class' => 'ripple inline-flex items-center justify-center gap-2 font-medium transition-all duration-200 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed ' . match($variant ?? 'primary') {
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'success' => 'btn-success',
        'danger' => 'btn-danger',
        'ghost' => 'bg-transparent hover:bg-white/10 text-gray-300 hover:text-white py-2 px-4 rounded-lg',
        default => 'btn-primary',
    } . ' ' . match($size ?? 'md') {
        'sm' => 'text-xs py-1.5 px-3',
        'md' => 'text-sm py-2.5 px-5',
        'lg' => 'text-base py-3 px-6',
        default => 'text-sm py-2.5 px-5',
    }
]) }}>
    @isset($icon)
        <span>{{ $icon }}</span>
    @endisset
    {{ $slot }}
</button>