<x-manager-layout>
    <x-post-editor>

        <div class="mb-4">
            <h1 class="font-semibold text-2xl text-center">{{ __('Új bejegyzés hozzáadása') }}</h1>
        </div>

        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="/post" enctype="multipart/form-data">
            @csrf

            <div class="flex flex-wrap justify-between">
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

            <!-- Description -->
            <div class="mt-4">
                <x-label for="description" :value="__('Rövid leírás')" />

                <textarea name="description" rows="2" cols="40" maxlength="250" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    {{ old('description') }}
                </textarea>
            </div>

            <!-- Post image -->
            <div class="mt-4">
                <x-label for="post_image" :value="__('Bejegyzés kép (Megosztáshoz)')" />

                <input id="post_image" class="block mt-1" type="file" name="post_image" />
            </div>

            <!-- Position -->
            <div class="mt-4">
                <x-label for="position" :value="__('Pozíció')" />

                <x-input id="position" class="block mt-1" type="number" name="position" :value="old('position', $next)" min="1" max="{{ $next }}" required />
            </div>

            </div>

            <!-- Content -->
            <div class="mt-4">
                <x-label for="content" :value="__('Bejegyzés tartalma')" />

                <textarea id="content" name="content" rows="4" cols="40" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    {{ old('content')}}
                </textarea>
            </div>

            <input type="hidden" name="section_id" value="{{ $sectionId }}"/>

            <div class="flex items-center justify-end mt-4">

                <x-cancel :link="url()->previous()">
                    {{ __('Mégse') }}
                </x-cancel>
                
                <x-button class="ml-4">
                    {{ __('Hozzáadás') }}
                </x-button>

            </div>
        </form>
    </x-post-editor>

    <x-scripts.tinymce/>
    
</x-guest-layout>
