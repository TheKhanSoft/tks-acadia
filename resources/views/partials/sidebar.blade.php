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

    <x-menu-item title="Users" icon="o-users" link="" route="users.index" wire:navigate /> 
    <x-menu-item active-by-route title="Office Types" icon="o-tag" :current="request()->routeIs('offices.types')" wire:navigate link="{{ route('offices.types') }}"  /> 
    <x-menu-item active-by-route title="Offices" icon="o-tag"  wire:navigate link="{{ route('offices.index') }}"  /> 
    <x-menu-item active-by-route title="Campuses" icon="o-tag" link="{{ route('campuses.index') }}" wire:navigate /> 

    <x-menu-sub title="Academics" icon="o-academic-cap">
        <x-menu-item title="Subjects" icon="o-book-open" link="{{ route('subjects.index') }}" wire:navigate />
        <x-menu-item title="Programs" icon="o-briefcase" link="{{ route('programs.index') }}" wire:navigate />
        <x-menu-item title="Faculties" icon="o-users" link="{{ route('faculties.index') }}" wire:navigate />
    </x-menu-sub>
    
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
