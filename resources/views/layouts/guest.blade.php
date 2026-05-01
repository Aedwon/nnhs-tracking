<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-navy bg-eggshell antialiased min-h-screen border-t-8 border-navy flex flex-col justify-center items-center relative">
        <!-- Abstract geometry for background depth -->
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none flex justify-center items-center opacity-5">
            <div class="w-[800px] h-[800px] border-[1px] border-navy rotate-45 flex items-center justify-center">
                <div class="w-[600px] h-[600px] border-[1px] border-navy rotate-12"></div>
            </div>
        </div>

        <div class="z-10 w-full max-w-lg">
            <div class="text-center mb-10">
                <a href="/" class="inline-block border-2 border-navy p-4 bg-white shadow-[4px_4px_0_0_#0B132B] hover:shadow-[6px_6px_0_0_#0B132B] hover:-translate-y-1 transition-all duration-200">
                    <x-application-logo class="w-16 h-16 fill-current text-crimson" />
                </a>
                <h1 class="mt-6 text-3xl font-display font-bold tracking-tight text-navy uppercase">NNHS Grading System</h1>
                <p class="mt-2 text-sm text-navy/70 tracking-widest uppercase">Official Personnel Portal</p>
            </div>

            <div class="w-full bg-white border-2 border-navy shadow-[8px_8px_0_0_#0B132B] p-8 sm:p-12">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-xs text-navy/50 font-mono tracking-widest uppercase">
                &copy; {{ date('Y') }} NNHS. Authorized Access Only.
            </div>
        </div>
    </body>
</html>
