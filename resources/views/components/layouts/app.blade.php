<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">

    {{-- NAVBAR --}}
    @include('partials.navbar')

    {{-- MAIN --}}
    <x-main full-width>

        {{-- SIDEBAR --}}
        @include('partials.sidebar')

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    @livewireScripts

    {{--  TOAST area --}}
    <x-toast />
    

    @stack('scripts')

</body>

</html>
