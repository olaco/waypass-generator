<!-- Add this to the navigation menu -->
<nav class="flex items-center gap-4">
    <!-- Existing navigation items -->

    <x-nav-link :href="route('waybills.index')" :active="request()->routeIs('waybills.*')">
        {{ __('Waybills') }}
    </x-nav-link>
</nav>
