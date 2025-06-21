 {{-- NAVBAR --}}
 <x-nav sticky full-width>
    <x-slot:brand>
        <x-app-brand class="p-0 pt-0 pb-4" />
    </x-slot:brand>

    {{-- Right side actions --}}
    <x-slot:actions>
        <x-button tooltip-left="Messages" icon="o-envelope" link="#" class="btn-ghost btn-sm" responsive />
        <x-dropdown >
            <x-slot:trigger>
                <x-button tooltip-bottom="Notifications" icon="o-bell" class="btn-ghost btn-sm" responsive />
            </x-slot:trigger>
        
            <x-menu-item title="Archive" />
            <x-menu-item title="Move" />
        </x-dropdown>

        <x-theme-toggle tooltip-bottom="Change Theme" />

    </x-slot:actions>
</x-nav>

{{-- NAVBAR mobile only --}}
<x-navbar sticky class="lg:hidden">
    <x-slot:brand>
        <x-app-brand class="p-0 pt-0 pb-4" />
    </x-slot:brand>

    <x-slot:actions>
        <label for="main-drawer" label="MENU" class="lg:hidden me-3">
            <x-icon name="o-bars-3" class="cursor-pointer" />
            <x-icon name="o-envelope" class="cursor-pointer" />
            <x-icon name="o-bell" class="cursor-pointer" />
        </label>
    </x-slot:actions>

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

            <x-menu-item title="Users" icon="o-users" link="" route="users.index" /> 
            <x-menu-item active-by-route title="Office Types" icon="o-tag" link="{{ route('offices.types') }}"  /> 
            <x-menu-item title="Campuses" icon="o-tag" link="campuses" /> 

            
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
</x-navbar>