<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body  class="bg-gray-100">
    <img class="hidden xl:flex w-40 z-10 fixed bottom-0 left-0" src="/img/radars.png" alt="">
    <div id="app">
        <section class="mt-16 px-8">
            <main class="container mx-auto">
                <div class="lg:flex lg:justify-between">
                    <div class="pt-4 p-4 lg:w-1/6 mb-6 sm:w-2/5 sm:mx-auto" style="max-width: 700px;">
                        @if(auth()->user()->group)
                            <group-panel class="mx-5" :user="{{ auth()->user() }}" :group="{{ auth()->user()->group }}"></group-panel>
                        @else
                            <empty-group-panel :user="{{ auth()->user() }}"></empty-group-panel>
                        @endif
                    </div>
                    
                    <div class="lg:w-4/6 pt-7 mx-7 mb-2 sm:mb-12 bg-white shadow-lg rounded-b pt-10 pb-4 px-7 border border-gray-200" >
                        
                        {{ $slot }}

                    </div>
                  
                    <div class="mt-10 lg:w-1/6 mb-6 sm:w-2/5 md:mx-auto lg:mx-0 sm:mx-auto" style="max-width: 700px;">
                        @if(auth()->user()->group != null)
                            <members-panel :user="{{ auth()->user() }}" :members="{{ auth()->user()->group->users->except(auth()->user()->id) }}" :group="{{ auth()->user()->group }}"></members-panel>
                        @endif
                        
                    </div>
                    <div class="h-6"></div>
                </div>

            </main>
        </section>
        <header class="absolute top-0 w-full p-2 shadow-2xl" style="background-color: #5a819e;">
            <div>
                <navbar :user="{{ auth()->user() }}" route_dashboard="{{ route('dashboard') }}"></navbar>    
            </div>
        
        </header>
    </div>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>