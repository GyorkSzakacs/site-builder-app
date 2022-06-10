@props(['action', 'question'])

<form method="POST" action="{{ $action }}" onsubmit="return confirm('{{ $question }}')">
    @csrf

    @method('DELETE')
                                            
    <button type="submit" {{ $attributes->merge(['class' => 'inline-flex items-center px-2 py-1 bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-800 active:bg-red-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150']) }}>
        {{ __('Töröl') }}
    </button>
</form>