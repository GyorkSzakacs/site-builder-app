<x-guest-layout>
    <x-auth-card>

    <div class="mb-4">
            <h1 class="font-semibold text-2xl text-center">{{ $user->name }}</h1>
            <h2  class="font-semibold text-xl text-gray-600 text-center">{{ __('hozzáférési szintjének módosítása') }}</h2>
    </div>

        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="/account-access/{{ $user->id }}">
            @csrf

            @method('PATCH')

            <!-- Access -->
            <div class="mt-4">
                <x-label for="access_level" :value="__('Szerepkör')" />

                <select name="access_level" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @switch($user->access_level)
                    
                    @case(1)
                        <option value=1 selected>{{ __('Adminisztrátor') }}</option>
                        <option value=2>{{ __('Menedzser') }}</option>
                        <option value=3>{{ __('Szerkesztő') }}</option>
                        @break

                    @case(2)
                        <option value=1>{{ __('Adminisztrátor') }}</option>
                        <option value=2 selected>{{ __('Menedzser') }}</option>
                        <option value=3>{{ __('Szerkesztő') }}</option>
                        @break

                    @case(3)
                        <option value=1>{{ __('Adminisztrátor') }}</option>
                        <option value=2>{{ __('Menedzser') }}</option>
                        <option value=3 selected>{{ __('Szerkesztő') }}</option>
                        @break

                    @default
                        <option value=1>{{ __('Adminisztrátor') }}</option>
                        <option value=2>{{ __('Menedzser') }}</option>
                        <option value=3 selected>{{ __('Szerkesztő') }}</option>
                @endswitch
                </select>
            </div>

            <div class="flex items-center justify-end mt-4">

                <x-cancel :link="route('dashboard')">
                    {{ __('Mégse') }}
                </x-cancel>

                <x-button class="ml-4">
                    {{ __('Módosítás') }}
                </x-button>

            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
