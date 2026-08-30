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

        @auth
            @php
                $userRole = auth()->user()->role ?? 'viewer';
            @endphp

            {{-- ==========================================
            CREATE WAYBILL - Show for DIST_OFFICER & ADMIN only
            ========================================== --}}
            @if($userRole === 'dist_officer' || $userRole === 'admin')
                <a href="{{ route('waybills.create') }}"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition duration-150 {{ request()->routeIs('waybills.create') ? 'bg-gray-100 font-semibold' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Create Waybill</span>
                </a>
            @endif

            {{-- ==========================================
            DISTRIBUTION OFFICER
            ========================================== --}}
            @if($userRole === 'dist_officer')
                <a href="{{ route('waybills.dist-officer') }}"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition duration-150 {{ request()->routeIs('waybills.dist-officer') ? 'bg-gray-100 font-semibold' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span>My Waybills</span>
                    @if($draftCount ?? 0 > 0)
                        <span class="ml-auto bg-gray-200 text-gray-700 text-xs font-bold px-2 py-1 rounded-full">
                            {{ $draftCount }}
                        </span>
                    @endif
                </a>
            @endif

            {{-- ==========================================
            WAREHOUSE MANAGER
            ========================================== --}}
            @if($userRole === 'warehouse_manager')
                <a href="{{ route('waybills.warehouse') }}"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition duration-150 {{ request()->routeIs('waybills.warehouse') ? 'bg-gray-100 font-semibold' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>Warehouse Approvals</span>
                    @if($warehousePendingCount ?? 0 > 0)
                        <span class="ml-auto bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full">
                            {{ $warehousePendingCount }}
                        </span>
                    @endif
                </a>
            @endif

            {{-- ==========================================
            AUDITOR
            ========================================== --}}
            @if($userRole === 'auditor')
                <a href="{{ route('waybills.auditor') }}"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition duration-150 {{ request()->routeIs('waybills.auditor') ? 'bg-gray-100 font-semibold' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Auditor Approvals</span>
                    @if($auditorPendingCount ?? 0 > 0)
                        <span class="ml-auto bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full">
                            {{ $auditorPendingCount }}
                        </span>
                    @endif
                </a>
            @endif

            {{-- ==========================================
            SUPPLY CHAIN MANAGER
            ========================================== --}}
            @if($userRole === 'supply_chain_manager')
                <a href="{{ route('waybills.supply-chain') }}"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition duration-150 {{ request()->routeIs('waybills.supply-chain') ? 'bg-gray-100 font-semibold' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>Supply Chain Approvals</span>
                    @if($supplyChainPendingCount ?? 0 > 0)
                        <span class="ml-auto bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full">
                            {{ $supplyChainPendingCount }}
                        </span>
                    @endif
                </a>
            @endif

            {{-- ==========================================
            ADMIN
            ========================================== --}}
            @if($userRole === 'admin')
                <a href="{{ route('waybills.approvals') }}"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition duration-150 {{ request()->routeIs('waybills.approvals') ? 'bg-gray-100 font-semibold' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Pending Approvals</span>
                    @if($pendingApprovalsCount ?? 0 > 0)
                        <span class="ml-auto bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full">
                            {{ $pendingApprovalsCount }}
                        </span>
                    @endif
                </a>

                <!-- User Management (Admin only) -->
                <a href="{{ route('admin.users') }}"
                   class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition duration-150 {{ request()->routeIs('admin.users') ? 'bg-gray-100 font-semibold' : '' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>User Management</span>
                </a>
            @endif
        @endauth

        <hr class="my-4 border-gray-200">

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

        <!-- Reports -->
        <a href="{{ route('waybills.reports') }}"
           class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition duration-150 {{ request()->routeIs('waybills.reports') ? 'bg-gray-100 font-semibold' : '' }}">
            <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2-2V9a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H9zm0 0a2 2 0 01-2-2V9a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H9z"/>
            </svg>
            <span>Reports</span>
        </a>

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
