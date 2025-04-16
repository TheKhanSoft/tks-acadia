<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <div class="navbar border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <label for="main-drawer" class="btn btn-ghost drawer-button lg:hidden">
                <x-icon name="o-bars-3" class="h-5 w-5" />
            </label>

            <a href="{{ route('dashboard') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
                <x-app-logo />
            </a>

            <div class="navbar-start hidden lg:flex -mb-px">
                <ul class="menu menu-horizontal">
                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" wire:navigate>
                            <x-icon name="o-squares-2x2" class="h-5 w-5" />
                            {{ __('Dashboard') }}
                        </a>
                    </li>
                </ul>
            </div>

            <div class="flex-1"></div>

            <div class="navbar-end me-1.5 space-x-0.5 rtl:space-x-reverse py-0">
                <div class="tooltip tooltip-bottom" data-tip="{{ __('Search') }}">
                    <a href="#" class="btn btn-ghost btn-circle">
                        <x-icon name="o-magnifying-glass" class="h-5 w-5" />
                    </a>
                </div>
                
                <div class="tooltip tooltip-bottom hidden lg:flex" data-tip="{{ __('Repository') }}">
                    <a href="https://github.com/laravel/livewire-starter-kit" target="_blank" class="btn btn-ghost btn-circle">
                        <x-icon name="o-folder" class="h-5 w-5" />
                    </a>
                </div>
                
                <div class="tooltip tooltip-bottom hidden lg:flex" data-tip="{{ __('Documentation') }}">
                    <a href="https://laravel.com/docs/starter-kits" target="_blank" class="btn btn-ghost btn-circle">
                        <x-icon name="o-book-open" class="h-5 w-5" />
                    </a>
                </div>
            </div>

            <!-- Desktop User Menu -->
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="avatar btn btn-ghost btn-circle">
                    <div class="w-10 rounded-full bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                        <span class="text-xl">{{ auth()->user()->initials() }}</span>
                    </div>
                </div>
                <ul tabindex="0" class="dropdown-content menu menu-sm z-[1] mt-3 w-52 rounded-box bg-base-100 p-2 shadow">
                    <li>
                        <div class="flex items-center gap-2 p-2">
                            <div class="avatar">
                                <div class="w-8 rounded-full bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    <span>{{ auth()->user()->initials() }}</span>
                                </div>
                            </div>
                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </li>
                    <li><div class="divider my-0"></div></li>
                    <li>
                        <a href="{{ route('settings.profile') }}" wire:navigate>
                            <x-icon name="o-cog-6-tooth" class="h-5 w-5" />
                            {{ __('Settings') }}
                        </a>
                    </li>
                    <li><div class="divider my-0"></div></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full text-left">
                                <x-icon name="o-arrow-right-on-rectangle" class="h-5 w-5" />
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="drawer-side z-40 lg:hidden">
            <label for="main-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <div class="menu min-h-full w-80 border-e border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <label for="main-drawer" class="btn btn-ghost drawer-button mb-4">
                    <x-icon name="o-x-mark" class="h-5 w-5" />
                </label>

                <a href="{{ route('dashboard') }}" class="ms-1 mb-4 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                    <x-app-logo />
                </a>

                <ul class="menu rounded-box">
                    <li class="menu-title">{{ __('Platform') }}</li>
                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" wire:navigate>
                            <x-icon name="o-squares-2x2" class="h-5 w-5" />
                            {{ __('Dashboard') }}
                        </a>
                    </li>
                </ul>

                <div class="flex-1 mt-8"></div>

                <ul class="menu rounded-box">
                    <li>
                        <a href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                            <x-icon name="o-folder" class="h-5 w-5" />
                            {{ __('Repository') }}
                        </a>
                    </li>
                    <li>
                        <a href="https://laravel.com/docs/starter-kits" target="_blank">
                            <x-icon name="o-book-open" class="h-5 w-5" />
                            {{ __('Documentation') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{ $slot }}

        @stack('scripts')
    </body>
</html>
