<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Vezérlőpult') }}
        </h1>
    </x-slot>

    @can('viewAny', App\Models\User::class)
    <div class="pt-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex">
                        <h2 class="block font-semibold mr-5 text-xl text-gray-80">
                            {{ __('Felhasználók') }}
                        </h2>
                        <x-buttons.edit :link="route('register')">
                            {{ __('Új hozzáadása') }}
                        </x-buttons.edit>
                    </div>
                    <div class="mt-4">
                        <table class="table-fixed border-collapse border border-gray-400">
                            <thead class="bg-gray-300">
                                <tr>
                                    <th class="px-4">{{ __('Felhasználónév') }}</th>
                                    <th class="px-4">{{ __('E-mail') }}</th>
                                    <th class="px-4">{{ __('Szerepkör') }}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)    
                                <tr>
                                    <td class="px-4 border border-gray-400">{{ $user->name }}</td>
                                    <td class="px-4 border border-gray-400">{{ $user->email }}</td>
                                    <td class="px-4 border border-gray-400">
                                        @switch($user->access_level)

                                            @case(1)    
                                                {{ __('Adminisztrátor')  }}
                                                @break

                                            @case(2)    
                                                {{ __('Menedszer')  }}
                                                @break

                                                @case(3)    
                                                {{ __('Szerkesztő')  }}
                                                @break

                                            @default
                                                {{ __('-') }}

                                        @endswitch
                                    </td>
                                    <td class="p-1 border border-gray-400">
                                    @can('updateAccess', $user)
                                        <x-buttons.edit :link="__('/account-access/').$user->id">
                                            {{ __('Szerepkör módosítása') }}
                                        </x-buttons.edit>
                                    @endcan
                                    </td>
                                    <td class="px-1 border border-gray-400">
                                    @can('delete', $user)
                                        <x-buttons.delete :action="__('/account/').$user->id" :question="__('Biztosan törölni szeretné '.$user->name.' felhasználót?')"/>
                                    @endcan
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan

    <div class="p-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex">
                        <h2 class="block font-semibold mr-5 text-xl text-gray-80">
                            {{ __('Kategóriák') }}
                        </h2>
                        <x-buttons.edit :link="route('create-category')">
                            {{ __('Új hozzzáadása') }}
                        </x-buttons.edit>
                    </div>
                    <div class="mt-4">
                        <table class="table-fixed border-collapse border border-gray-400">
                            <thead class="bg-gray-300">
                                <tr>
                                    <th class="px-4">{{ __('Címke') }}</th>
                                    <th class="px-4">{{ __('Pozíció') }}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($categories as $category)    
                                <tr>
                                    <td class="px-4 border border-gray-400">{{ $category->title }}</td>
                                    <td class="px-4 border border-gray-400">{{ $category->position }}</td>
                                    <td class="p-1 border border-gray-400">
                                        <x-buttons.edit :link="__('/update-category/').$category->id">
                                            {{ __('Módosít') }}
                                        </x-buttons.edit>
                                    </td>
                                    <td class="px-1 border border-gray-400">
                                        <x-buttons.delete :action="__('/category/').$category->id" :question="__('Biztosan törölni szeretné '.$category->title.' kategóriát?')"/>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex">
                        <h2 class="block font-semibold mr-5 text-xl text-gray-80">
                            {{ __('Oldalak') }}
                        </h2>
                        <x-buttons.edit :link="route('create-page')">
                            {{ __('Új hozzzáadása') }}
                        </x-buttons.edit>
                    </div>
                    <div class="mt-4">
                        <table class="table-fixed border-collapse border border-gray-400">
                            <thead class="bg-gray-300">
                                <tr>
                                    <th class="px-4">{{ __('Cím') }}</th>
                                    <th class="px-4">{{ __('Cím láthatósága') }}</th>
                                    <th class="px-4">{{ __('Kategória') }}</th>
                                    <th class="px-4">{{ __('Pozíció') }}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($pages as $page)    
                                <tr>
                                    <td class="px-4 border border-gray-400">{{ $page->title }}</td>
                                    <td class="px-4 border border-gray-400">{{ $page->title_visibility ? __('Igen') : __('Nem') }}</td>
                                    <td class="px-4 border border-gray-400">{{ $page->category->title }}</td>
                                    <td class="px-4 border border-gray-400">{{ $page->position }}</td>
                                    <td class="p-1 border border-gray-400">
                                        <x-buttons.edit :link="__('/update-page/').$page->id">
                                            {{ __('Módosít') }}
                                        </x-buttons.edit>
                                    </td>
                                    <td class="px-1 border border-gray-400">
                                        <x-buttons.delete :action="__('/page/').$page->id" :question="__('Biztosan törölni szeretné '.$page->title.' oldalt és annak tartalmát?')"/>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
