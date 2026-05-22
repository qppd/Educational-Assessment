<div class="stat-card" x-data="{ count: 0, target: {{ $value ?? 0 }} }"
     x-intersect="() => { let i = 0; const t = target; const interval = setInterval(() => { i += Math.ceil(t / 30); if (i >= t) { i = t; clearInterval(interval); } count = i; }, 30); }">
    @isset($icon)
        <div class="text-3xl flex-shrink-0">{{ $icon }}</div>
    @endisset
    <div class="flex-1 min-w-0">
        <p class="text-2xl font-bold gradient-text" x-text="count"></p>
        <p class="text-xs text-gray-400 mt-0.5">{{ $label ?? '' }}</p>
    </div>
</div>