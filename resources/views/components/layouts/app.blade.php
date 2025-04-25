<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
</head>

<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">
    {{-- NAVBAR --}}
    <x-navbar sticky>
        <x-slot:brand>
            <x-brand class="p-0 pt-0" />
        </x-slot:brand>

        {{-- Right side actions --}}
        <x-slot:actions>
            <x-button label="Messages" icon="o-envelope" link="###" class="btn-ghost btn-sm" responsive />
            <x-button label="Notifications" icon="o-bell" link="###" class="btn-ghost btn-sm" responsive />
        </x-slot:actions>
    </x-navbar>
    
    {{-- NAVBAR mobile only --}}
    <x-navbar sticky class="lg:hidden">
        <x-slot:brand>
            <x-brand />
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-navbar>

    {{-- MAIN --}}
    <x-main full-width>
    
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-200 lg:bg-inherit">

            {{-- BRAND --}}
            {{-- <x-app-brand class="p-5 pt-3" /> --}}

            {{-- MENU --}}
            <x-menu active-by-route class="pt-3">

                {{-- User --}}
                @if($user = auth()->user())
                    <x-menu-separator />

                    <x-list-item :item="$user" label="name" sub-label="email" no-separator no-hover class="-mx-2 !-my-2 rounded">
                        <x-slot:actions>
                            <x-button icon="o-power" class="btn-circle btn-ghost btn-xs" tooltip-position="left" tooltip="Logout" link="/logout" />
                        </x-slot:actions>
                    </x-list-item>

                    <x-menu-separator />
                @endif

                <x-menu-item title="Users" icon="o-users" link="" route="users.index" /> {{-- Changed icon --}}
                <x-menu-item title="Office Types" icon="o-tag" route="office-types.index" /> {{-- Added Office Types link --}}
                
                <x-menu-sub title="Settings" icon="o-cog-6-tooth">
                    <x-menu-item title="Wifi" icon="o-wifi" link="####" />
                    <x-menu-item title="Archives" icon="o-archive-box" link="####" />
                </x-menu-sub>
                
                <x-menu-item title="Yet another menu item" icon="o-sparkles" link="/kkj" />
                
                <x-menu-item title="Charts" icon="o-chart-pie">
                    <x-badge label="2" class="bg-warning" />
                    <x-icon name="o-heart" class="text-secondary" />
                </x-menu-item>
            </x-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>
    @livewireScripts
    @maryScripts

    {{--  TOAST area --}}
    <x-toast />
    @stack('scripts')
    
    
</body>
</html>
