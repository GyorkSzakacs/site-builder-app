<x-app-layout>
    <x-slot name="header">
        
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Galéria') }}
        </h2>
         
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(isset($images) && count($images) > 0)
            <div class="w-full flex flex-wrap bg-white shadow-sm sm:rounded-lg">
                
                @foreach($images as $image => $path)
                <div class="m-2 p-1 bg-white border border-gray-200">
                   <a href="#">
                        <img src="{{ asset($path) }}" width="120">
                    </a>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-white shadow-sm sm:rounded-lg">
                
                <div class="p-6 bg-white border-b border-gray-200">
                   {{ __('Nincsenek feltöltött képek!') }}
                </div>
            </div>
        @endif
        </div>
       
    </div>
        
</x-app-layout>
