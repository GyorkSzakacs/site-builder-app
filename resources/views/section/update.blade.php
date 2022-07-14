<x-manager-layout>
    <x-auth-card>

    <div class="mb-4">
            <h1 class="font-semibold text-2xl text-center">{{ __($section->title.' szekció módosítása') }}</h1>
        </div>

        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="/section/{{ $section->id }}">
            @csrf

            @method('PATCH')

            <!-- Title -->
            <div>
                <x-label for="title" :value="__('Cím')" />

                <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $section->title)" required autofocus />
            </div>

            <!-- Title visibility-->
            <div class="mt-4">
                <x-label for="title_visibility" :value="__('Cím láthatósága')" />

                @if($section->title_visibility)
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

            <!-- Position -->
            <div class="mt-4">
                <x-label for="position" :value="__('Pozíció')" />

                <x-input id="position" class="block mt-1" type="number" name="position" :value="old('position', $section->position)" min="1" max="{{ $max }}" required />
            </div>

            <input type="hidden" name="page_id" value="{{ $section->page_id }}"/>

            <div class="flex items-center justify-end mt-4">

                <x-cancel :link="url()->previous()">
                    {{ __('Mégse') }}
                </x-cancel>
                
                <x-button class="ml-4">
                    {{ __('Módosít') }}
                </x-button>

            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
