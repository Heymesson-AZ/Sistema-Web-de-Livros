<x-layouts.principal :title="$title ?? null">
    @isset($header)
        <x-slot:header>{{ $header }}</x-slot:header>
    @endisset
    {{ $slot }}
</x-layouts.principal>
