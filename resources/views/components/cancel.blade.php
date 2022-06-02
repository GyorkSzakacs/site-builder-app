@props(['link'])

<a href="{{ $link }}" {{ $attributes->merge(['class' => 'text-sm p-2 text-blue-500 rounded-md hover:bg-gray-100 hover:text-blue-600']) }}>
    {{ $slot }}
</a>