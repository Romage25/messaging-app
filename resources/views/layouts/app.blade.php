<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main class="{{ !($noPadding ?? false) ? '' : 'p-0!' }}">
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
