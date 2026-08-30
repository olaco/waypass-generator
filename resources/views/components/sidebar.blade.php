<aside class="w-64 bg-white border-r border-gray-200 min-h-screen fixed left-0 top-16 overflow-y-auto z-10">
    <nav class="p-4 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
           class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-gray-100 font-semibold' : '' }}">
            <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm0 10a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10-10a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zm0 10a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- All Waybills -->
        <a href="{{ route('waybills.index') }}"
           class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition duration-150 {{ request()->routeIs('waybills.index') ? 'bg-gray-100 font-semibold' : '' }}">
            <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>All Waybills</span>
        </a>

        <!-- Create Waybill -->
        <a href="{{ route('waybills.create') }}"
           class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition duration-150 {{ request()->routeIs('waybills.create') ? 'bg-gray-100 font-semibold' : '' }}">
            <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Create Waybill</span>
        </a>

        <!-- Pending Approvals -->
        <a href="{{ route('waybills.approvals') }}"
           class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition duration-150 {{ request()->routeIs('waybills.approvals') ? 'bg-gray-100 font-semibold' : '' }}">
            <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Pending Approvals</span>
            @if($pendingApprovalsCount ?? 0 > 0)
                <span class="ml-auto bg-gray-200 text-gray-700 text-xs font-bold px-2 py-1 rounded-full">
                    {{ $pendingApprovalsCount }}
                </span>
            @endif
        </a>

        <!-- Approved -->
        <a href="{{ route('waybills.approved') }}"
           class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition duration-150 {{ request()->routeIs('waybills.approved') ? 'bg-gray-100 font-semibold' : '' }}">
            <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Approved</span>
        </a>

        <!-- Rejected -->
        <a href="{{ route('waybills.rejected') }}"
           class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition duration-150 {{ request()->routeIs('waybills.rejected') ? 'bg-gray-100 font-semibold' : '' }}">
            <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>Rejected</span>
        </a>

        <hr class="my-4 border-gray-200">

        <!-- Settings -->
        <a href="{{ route('profile.edit') }}"
           class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition duration-150 {{ request()->routeIs('profile.edit') ? 'bg-gray-100 font-semibold' : '' }}">
            <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Settings</span>
        </a>
    </nav>
</aside>
