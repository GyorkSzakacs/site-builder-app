@props(['link'])

<a href="{{ $link }}" {{ $attributes->merge(['class' => 'inline-flex items-center px-2 py-1 bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-800 active:bg-blue-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>