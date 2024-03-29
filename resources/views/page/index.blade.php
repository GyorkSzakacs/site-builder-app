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

                <div class="bg-gray-50 shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-gray-50 border-b border-gray-200">
                        
                    @if(isset($section->posts) && count($section->posts) > 0)
                        @foreach($section->posts->sortBy('position') as $post)
                         
                        <div class="bg-white p-2">
                                
                            @if($post->title_visibility)
                                <a href="{{ url('/'.$page->slug.'/'.$section->slug.'/'.$post->slug) }}" target="_blank" class="mb-2 block font-semibold text-lg text-gray-80 hover:underline">
                                    {{ $post->title}}
                                </a>
                            @else
                                <a href="{{ url('/'.$page->slug.'/'.$section->slug.'/'.$post->slug) }}" target="_blank" class="text-right text-blue-300 mb-2 block font-semibold text-lg text-gray-80 hover:underline">
                                    {{ __('Megnyitás új lapon >>')}}
                                </a>
                            @endif
                   
                                <div class="p-1 overflow-hidden rounded-lg">
                                    {!! $post->content !!}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div>
                            Hozza létre weboldalát néhány kattintással!
                        </div>
                    @endif

                    @can('create', App\Models\Post::class)
                        <div class="flex justify-center">
                            <x-buttons.edit :link="route('create-post', ['id' => $section->id])">
                                {{ __('+') }}
                            </x-buttons.edit>
                        </div>
                    @endcan
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
    <div class="flex justify-center -mt-6 pb-6">
        <x-buttons.edit :link="route('create-section', ['id' => $page->id])">
            {{ __('+') }}
        </x-buttons.edit>
    </div>
    @endcan
</x-app-layout>
