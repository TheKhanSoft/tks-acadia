<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">
    {{-- NAVBAR --}}
    <x-nav sticky full-width>
        <x-slot:brand>
            <x-app-brand class="p-0 pt-0 pb-4" />
        </x-slot:brand>

        {{-- Right side actions --}}
        <x-slot:actions>

        </x-slot:actions>
    </x-nav>

    {{-- MAIN --}}
    <x-main full-width>
        {{-- The `$slot` goes here --}}
        @php
            $step = $step ?? 1;
        @endphp
        {{-- <!--
        <x-card>
            <x-steps :wire:model="$step" steps-color="step-primary">
                <x-step step="1" text="Database Configuration" icon="o-cog" />
                <x-step step="2" text="Database Migrations" icon="o-circle-stack" />
                <x-step step="3" text="Database Seeding" icon="o-circle-stack" />
                <x-step step="4" text="Admin Details" icon="o-user" />
                <x-step step="5" text="Default Settings" icon="o-cog-6-tooth" />
            </x-steps>
        </x-card>
        --> --}}
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
