<div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <!-- Header -->


            <!-- Filters -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search by reference, waybill no..."
                           class="w-full rounded border-gray-300 focus:border-gray-500 focus:ring-gray-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Filter</label>
                    <select wire:model.live="statusFilter"
                            class="w-full rounded border-gray-300 focus:border-gray-500 focus:ring-gray-500">
                        <option value="">All Statuses</option>
                        <option value="draft">Draft</option>
                        <option value="pending_warehouse">Pending Warehouse</option>
                        <option value="pending_auditor">Pending Auditor</option>
                        <option value="pending_supply_chain">Pending Supply Chain</option>
                        <option value="pending_final">Pending Final</option>
                        <option value="approved">Approved</option>
                        <option value="returned">Returned</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Items Per Page</label>
                    <select wire:model.live="perPage"
                            class="w-full rounded border-gray-300 focus:border-gray-500 focus:ring-gray-500">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th wire:click="sortBy('reference_no')"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-1">
                                    Reference No
                                    <span class="text-gray-400">
                                        @if($sortField === 'reference_no')
                                            {!! $sortDirection === 'asc' ? '↑' : '↓' !!}
                                        @endif
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('waybill_no')"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-1">
                                    Waybill No
                                    <span class="text-gray-400">
                                        @if($sortField === 'waybill_no')
                                            {!! $sortDirection === 'asc' ? '↑' : '↓' !!}
                                        @endif
                                    </span>
                                </div>
                            </th>
                            <th wire:click="sortBy('status')"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-1">
                                    Status
                                    <span class="text-gray-400">
                                        @if($sortField === 'status')
                                            {!! $sortDirection === 'asc' ? '↑' : '↓' !!}
                                        @endif
                                    </span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Items
                            </th>
                            <th wire:click="sortBy('created_at')"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center gap-1">
                                    Created
                                    <span class="text-gray-400">
                                        @if($sortField === 'created_at')
                                            {!! $sortDirection === 'asc' ? '↑' : '↓' !!}
                                        @endif
                                    </span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($waybills as $waybill)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $waybill->reference_no }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $waybill->waybill_no }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ ucfirst(str_replace('_', ' ', $waybill->status)) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $waybill->items->count() }} items
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $waybill->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-3">
                                    <!-- View -->
                                    <a href="{{ route('waybills.show', $waybill) }}"
                                       class="text-gray-600 hover:text-gray-900 transition duration-150"
                                       title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    <!-- PDF -->
                                    <a href="{{ route('waybills.pdf', $waybill) }}"
                                       target="_blank"
                                       class="text-gray-600 hover:text-gray-900 transition duration-150"
                                       title="Download PDF">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v6m0 0l-3-3m3 3l3-3"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium">No waybills found</p>
                                    <p class="text-sm mt-1">Create your first waybill to get started.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $waybills->links() }}
            </div>
        </div>
    </div>
</div>
