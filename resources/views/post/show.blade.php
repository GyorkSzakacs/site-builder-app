<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!--meta data for facebook-->
        <meta property="og:url"           content="{{ request()->url() }}" />
        <meta property="og:type"          content="article" />
        <meta property="og:title"         content="{{ $post->title }}" />
        <meta property="og:description"   content="{{ $post->description }}" />
        <meta property="og:image"         content="{{ asset($post->post_image) }}" />

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            <header class="bg-white shadow">
                <div class="flex max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="mr-5">
                        {{ $post->title }}
                    </h1>

                    <!-- Load Facebook SDK for JavaScript -->
                    <div id="fb-root"></div>
                    <script>
                        (function(d, s, id) {
                            var js, fjs = d.getElementsByTagName(s)[0];

                            if (d.getElementById(id)) return;

                            js = d.createElement(s); js.id = id;
                            js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0";
                            fjs.parentNode.insertBefore(js, fjs);
                        }
                        (document, 'script', 'facebook-jssdk'));
                    </script>

                    <!-- Your share button code -->
                    <div class="fb-share-button" 
                        data-href="https://www.your-domain.com/your-page.html" 
                        data-layout="button_count">
                    </div>

                    @can('update', $post)
                    <x-buttons.edit :link="url('/update-post/'.$post->id)" class="ml-5">
                        {{ __('Módosít') }}
                    </x-buttons.edit>
                    @endcan

                    @can('delete', $post)
                    <x-buttons.delete :action="__('/post/'.$post->id)" :question="__('Biztosan törölni szeretné '.$post->title.' bejegyzést?')" class="ml-2"/>
                    @endcan
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <div class="py-12">
                    <div class="flex max-w-7xl mx-auto sm:px-6 lg:px-8">

                        <div class="w-full border-b border-gray-200 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white ">
                                {!! $post->content !!}
                            </div>
                        </div>

                        @can('view', $post)
                        <div class="max-w-3xl ml-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="mb-2">Rövid leírás:</h3>    
                                @if(!empty($post->description))
                                    {{ $post->description }}
                                @else
                                   {{ __('-') }}
                                @endif
                            </div>
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="mb-2">Bejegyzés kép (Megosztáshoz):</h3>
                                @if(!empty($post->post_image))
                                    <img src="{{ asset($post->post_image) }}">
                                @else
                                   {{ __('-') }}
                                @endif
                            </div>
                        </div>
                        @endcan

                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
