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
<body>
    <div id="app">
        <header class="p-8">
            <div class="container mx-auto flex justify-between">
                <a href="{{ route('dashboard') }}">
                    <img src="/img/logo.png" width="60px" alt="logo">
                </a>
                <a href="/{{ auth()->user()->username }}/notes" class="shadow w-22 h-10 text-center relative inline-flex justify-center rounded-full border border-gray-300 ml-5 px-4 py-2 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                    Notes
                </a>
                <dropdown></dropdown>
            </div>
        
        </header>
        <section class="px-8">
            <main class="container mx-auto">
                <div class="lg:flex lg:justify-between">
                    <div class="lg:w-1/6 mb-6 sm:w-2/5 lg:mx-0 sm:mx-auto" style="max-width: 700px;">
                        <group-panel :user="{{ auth()->user() }}"></group-panel>
                    </div>
                    
                    <div class="lg:w-4/6 mx-8 mb-2 sm:mb-12 bg-white shadow-lg rounded-lg pb-4 px-8 border border-gray-200" >
                        
                        {{ $slot }}

                    </div>
                  
                    <div class="lg:w-1/6 mb-6 sm:w-2/5 md:mx-auto lg:mx-0 sm:mx-auto" style="max-width: 700px;">
                        @if(auth()->user()->group != null)
                            <members-panel :user="{{ auth()->user() }}" :members="{{ auth()->user()->group->users->except(auth()->user()->id) }}" :groupid="{{ auth()->user()->group->id }}"></members-panel>
                        @endif
                        
                    </div>
                    <div class="h-6"></div>
                </div>

            </main>
        </section>
    </div>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>