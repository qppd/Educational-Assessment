<div {{ $attributes->merge(['class' => 'overflow-x-auto']) }}>
    <table class="w-full text-sm">
        <thead>
            <tr class="text-xs text-gray-400 uppercase border-b border-white/5">
                {{ $head ?? '' }}
            </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
            {{ $slot }}
        </tbody>
    </table>
</div>