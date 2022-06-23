<x-manager-layout>
    <x-auth-card>

    <div class="mb-4">
            <h1 class="font-semibold text-2xl text-center">{{ $page->title.__(' oldal módosítása') }}</h1>
        </div>

        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="/page/{{ $page->id }}">
            @csrf

            @method('PATCH')

            <!-- Title -->
            <div>
                <x-label for="title" :value="__('Cím')" />

                <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $page->title)" required autofocus />
            </div>

            <!-- Title visibility-->
            <div class="mt-4">
                <x-label for="title_visibility" :value="__('Cím láthatósága')" />
                @if($page->title_visibility)
                <div class="flex ml-2">
                    <x-input id="visibility_true" class="block mt-1" type="radio" name="title_visibility" :value=1 checked/>
                    <x-label for="visibility_true" class="block p-1 text-xs" :value="__('Igen')" />
                </div>

                <div class="flex ml-2">
                    <x-input id="visibility_false" class="block mt-1" type="radio" name="title_visibility" :value=0 />
                    <x-label for="visibility_false" class="block p-1 text-xs" :value="__('Nem')" />
                </div>
                @else
                <div class="flex ml-2">
                    <x-input id="visibility_true" class="block mt-1" type="radio" name="title_visibility" :value=1 />
                    <x-label for="visibility_true" class="block p-1 text-xs" :value="__('Igen')" />
                </div>

                <div class="flex ml-2">
                    <x-input id="visibility_false" class="block mt-1" type="radio" name="title_visibility" :value=0 checked/>
                    <x-label for="visibility_false" class="block p-1 text-xs" :value="__('Nem')" />
                </div>
                @endif
            </div>

            <!-- Category -->
            <div class="mt-4">
                <x-label for="category_id" :value="__('Kategória')" />

                <select name="category_id" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">{{ __('Új kategória') }}</option>
                    @foreach($categories as $category)
                        @if($category->id == $page->category_id)
                            <option value="{{ $category->id }}" selected>{{ $category->title }}</option>
                        @else
                            <option value="{{ $category->id }}">{{ $category->title }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Position -->
            <div class="mt-4">
                <x-label for="position" :value="__('Pozíció')" />

                <x-input id="position" class="block mt-1" type="number" name="position" :value="old('position', $page->position)" min="1" max="3" required />
            </div>

            <div class="flex items-center justify-end mt-4">

                <x-cancel :link="route('dashboard')">
                    {{ __('Mégse') }}
                </x-cancel>
                
                <x-button class="ml-4">
                    {{ __('Módosít') }}
                </x-button>

            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
