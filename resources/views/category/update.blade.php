<x-guest-layout>
    <x-auth-card>

    <div class="mb-4">
            <h1 class="font-semibold text-2xl text-center">{{ __('Kategória módosítása') }}</h1>
        </div>

        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="/category/{{ $category->id }}">
            @csrf

            @method('PATCH')

            <!-- Title -->
            <div>
                <x-label for="title" :value="__('Címke')" />

                <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $category->title)" required autofocus />
            </div>

            <!-- Position -->
            <div class="mt-4">
                <x-label for="position" :value="__('Pozíció')" />

                <x-input id="position" class="block mt-1" type="number" name="position" :value="old('position', $category->position)" min="1" max="{{ $max }}" required />
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
