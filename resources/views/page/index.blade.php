<x-app-layout>
    <x-slot name="header">
        
        @if(isset($page))
            @if($page->title_visibility)
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $page->title }}
                </h2>
            @else
                <h2 class="invisible font-semibold text-xl text-gray-800 leading-tight">
                    {{ $page->title }}
                </h2>
            @endif
        @else
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Site Builder App by Szakács Györk') }}
            </h2>
        @endif
        
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    Hozza létre weboldalát néhány kattintással!
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
