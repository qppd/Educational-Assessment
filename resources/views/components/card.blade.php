<div class="card card-hover cursor-pointer" x-data="{ hover: false }"
     @mouseenter="hover = true" @mouseleave="hover = false"
     {{ $attributes->merge(['class' => '']) }}>
    <div class="flex items-start gap-4">
        @isset($icon)
            <div class="text-3xl flex-shrink-0" :class="hover ? 'scale-110' : ''"
                 style="transition: transform 0.3s ease">
                {{ $icon }}
            </div>
        @endisset
        <div class="flex-1 min-w-0">
            @isset($title)
                <h3 class="font-semibold text-gray-200 truncate">{{ $title }}</h3>
            @endisset
            @isset($description)
                <p class="text-sm text-gray-400 mt-0.5">{{ $description }}</p>
            @endisset
            {{ $slot ?? '' }}
        </div>
        @isset($action)
            <div class="flex-shrink-0">
                {{ $action }}
            </div>
        @endisset
    </div>
</div>