<x-manager-layout>
    <x-auth-card>

    <div class="mb-4">
            <h1 class="font-semibold text-2xl text-center">{{ __('Új oldal létrehozása') }}</h1>
        </div>

        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="/page">
            @csrf

            <!-- Title -->
            <div>
                <x-label for="title" :value="__('Cím')" />

                <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
            </div>

            <!-- Title visibility-->
            <div class="mt-4">
                <x-label for="title_visibility" :value="__('Cím láthatósága')" />

                <div class="flex ml-2">
                    <x-input id="visibility_true" class="block mt-1" type="radio" name="title_visibility" :value=1 checked/>
                    <x-label for="visibility_true" class="block p-1 text-xs" :value="__('Igen')" />
                </div>

                <div class="flex ml-2">
                    <x-input id="visibility_false" class="block mt-1" type="radio" name="title_visibility" :value=0 />
                    <x-label for="visibility_false" class="block p-1 text-xs" :value="__('Nem')" />
                </div>
            </div>

            <!-- Category -->
            <div class="mt-4">
                <x-label for="category_id" :value="__('Kategória')" />

                <select name="category_id" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">{{ __('Új kategória') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end mt-4">

                <x-cancel :link="route('dashboard')">
                    {{ __('Mégse') }}
                </x-cancel>
                
                <x-button class="ml-4">
                    {{ __('Létrehozás') }}
                </x-button>

            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
