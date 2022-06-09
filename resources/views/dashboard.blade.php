<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Vezérlőpult') }}
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex">
                        <h2 class="block font-semibold mr-5 text-xl text-gray-80">
                            {{ __('Felhasználók') }}
                        </h2>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-2 bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-800 active:bg-blue-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Új hozzzáadása') }}
                        </a>
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
                                        <a href="/account-access/{{ $user->id }}" class="inline-flex items-center px-2 py-1 bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-800 active:bg-blue-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            {{ __('Szerepkör módosítása') }}
                                        </a>
                                    @endcan
                                    </td>
                                    <td class="px-1 border border-gray-400">
                                    @can('delete', $user)
                                        <form method="POST" action="/account/{{ $user->id }}" onsubmit="return confirm('Biztosan törölni szeretné {{ $user->name }} felhasználót?')">
                                            @csrf

                                            @method('DELETE')
                                            
                                            <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-800 active:bg-red-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                {{ __('Töröl') }}
                                            </button>
                                        </form>
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
    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex">
                        <h2 class="block font-semibold mr-5 text-xl text-gray-80">
                            {{ __('Kategóriák') }}
                        </h2>
                        <a href="{{ route('create-category') }}" class="inline-flex items-center px-2 bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-800 active:bg-blue-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Új hozzzáadása') }}
                        </a>
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
                                        <a href="/update-category/{{ $category->id }}" class="inline-flex items-center px-2 py-1 bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-800 active:bg-blue-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            {{ __('Módosít') }}
                                        </a>
                                    </td>
                                    <td class="px-1 border border-gray-400">
                                        <form method="POST" action="/category/{{ $category->id }}" onsubmit="return confirm('Biztosan törölni szeretné {{ $category->title }} kategóriát?')">
                                            @csrf

                                            @method('DELETE')
                                            
                                            <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-800 active:bg-red-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                {{ __('Töröl') }}
                                            </button>
                                        </form>
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
                        <a href="{{ route('create-category') }}" class="inline-flex items-center px-2 bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-800 active:bg-blue-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Új hozzzáadása') }}
                        </a>
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
                            @foreach($categories as $category)    
                                <tr>
                                    <td class="px-4 border border-gray-400">{{ $category->title }}</td>
                                    <td class="px-4 border border-gray-400">{{ $category->title }}</td>
                                    <td class="px-4 border border-gray-400">{{ $category->title }}</td>
                                    <td class="px-4 border border-gray-400">{{ $category->position }}</td>
                                    <td class="p-1 border border-gray-400">
                                        <a href="/update-category/{{ $category->id }}" class="inline-flex items-center px-2 py-1 bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-800 active:bg-blue-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            {{ __('Módosít') }}
                                        </a>
                                    </td>
                                    <td class="px-1 border border-gray-400">
                                        <x-delete :action="route('dashboard')" :question="__('Biztosan törölni szeretné '.$category->title.' kategóriát?')"/>
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
