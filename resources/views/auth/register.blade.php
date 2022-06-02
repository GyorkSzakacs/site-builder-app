<x-guest-layout>
    <x-auth-card>

    <div class="mb-4">
            <h1 class="font-semibold text-2xl text-center">{{ __('Új felhasználó hozzáadása') }}</h1>
        </div>

        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-label for="name" :value="__('Név')" />

                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-label for="email" :value="__('E-mail')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Jelszó')" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('Jelszó megerősítése')" />

                <x-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required />
            </div>

            <!-- Access -->
            <div class="mt-4">
                <x-label for="access_level" :value="__('Szerepkör')" />

                <select name="access_level" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value=1>{{ __('Adminisztrátor') }}</option>
                    <option value=2>{{ __('Menedzser') }}</option>
                    <option value=3 selected>{{ __('Szerkesztő') }}</option>
                </select>
            </div>

            <div class="flex items-center justify-end mt-4">

                <x-cancel :link="route('dashboard')">
                    {{ __('Mégse') }}
                </x-cancel>
                
                <x-button class="ml-4">
                    {{ __('Hozzáadás') }}
                </x-button>

            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
