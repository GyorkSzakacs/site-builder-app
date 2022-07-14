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

    @if(isset($sections) && count($sections) > 0)
        @foreach($sections as $section)
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="flex mb-2">
                    
                    @if($section->title_visibility)
                    <h2 class="block font-semibold mr-5 text-xl text-gray-80">
                        {{ $section->title }}
                    </h2>
                    @else
                    <h2 class="invisible block font-semibold mr-5 text-xl text-gray-80">
                        {{ $section->title }}
                    </h2>
                    @endif

                    @can('update', $section)
                    <x-buttons.edit :link="url('/update-section/'.$section->id)">
                        {{ __('Módosít') }}
                    </x-buttons.edit>
                    @endcan

                    @can('delete', $section)
                    <x-buttons.delete :action="__('/section/'.$section->id)" :question="__('Biztosan törölni szeretné '.$section->title.' szekcíót és annak tartalmát az oldalról ?')" class="ml-2"/>
                    @endcan
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        Hozza létre weboldalát néhány kattintással!
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        Hozza létre weboldalát néhány kattintással!
                    </div>
                </div>

            </div>
        </div>
    @endif

    @can('create', App\Models\Section::class)
    <div class="flex justify-center">
        <x-buttons.edit :link="route('create-section', ['id' => $page->id])">
            {{ __('+') }}
        </x-buttons.edit>
    </div>
    @endcan
</x-app-layout>
