<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

        <!--jQuerry-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Galéria') }}
                    </h2>

                </div>
            </header>
      
            <!-- Page Content -->
            <main>

                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    @if(isset($images) && count($images) > 0)
                        <div class="w-full flex flex-wrap bg-white shadow-sm sm:rounded-lg">
                            
                            @foreach($images as $image => $path)
                            <div class="m-2 p-1 bg-white border border-gray-200">
                                
                                <img src="{{ asset($path) }}" width="120" class="openModal hover:cursor-pointer">

                                <!-- Image manager popup -->
                                <div class="imgModal fixed z-10 inset-0 invisible overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" id="interestModal">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
                                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                             
                                            <button type="button" class="closeModal pr-2 w-full inline-flex justify-end shadow-sm bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-xl">
                                                {{ __('x') }}
                                            </button>
                                                     
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                   
                                                <div class="sm:flex sm:items-start">
                                                    <div class="text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                       
                                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                            {{ __('Kép kezelése') }}
                                                        </h3>
                                                        
                                                        <div class="mt-2">
                                                            <img src="{{ asset($path) }}" width="360">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-row-reverse">
                                                
                                                <x-buttons.delete :action="__('/image/'.$image)" :question="__('Biztosan törölni szeretné a kiválasztott képet?')" class="ml-2"/>
                                                              
                                                <form method="POST" action="/image/{{ $image }}" >
                                                    @csrf
                                   
                                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-800 active:bg-blue-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                        {{ __('Letöltés') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--End modal-->
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white shadow-sm sm:rounded-lg">
                            
                            <div class="p-6 bg-white border-b border-gray-200">
                            {{ __('Nincsenek feltöltött képek!') }}
                            </div>
                        </div>
                    @endif
                    </div>
                </div>

            </main>
        </div>
        <!--JS for image modal-->
        <script type="text/javascript">
        $(document).ready(function () {
            $('.openModal').on('click', function(e){
                let modal = $(this).siblings('.imgModal')[0];
                $(modal).removeClass('invisible');
            });
            $('.closeModal').on('click', function(e){
                let modal = $(this).parents('.imgModal')[0];
                $(modal).addClass('invisible');
            });
        });
        </script>
    </body> 
</html>
