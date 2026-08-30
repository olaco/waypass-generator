<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">❌ Rejected / Returned Waybills</h2>
        <p class="text-sm text-gray-500 mt-1">Waybills that were rejected or returned</p>
    </div>

    <!-- Search -->
    <div class="mb-6">
        <input type="text"
               wire:model.live.debounce.300ms="search"
               placeholder="Search by reference or waybill no..."
               class="w-full md:w-1/3 rounded border-gray-300 focus:border-gray-500 focus:ring-gray-500">
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waybill No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($waybills as $waybill)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $waybill->reference_no }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $waybill->waybill_no }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                {{ ucfirst(str_replace('_', ' ', $waybill->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $waybill->items->count() }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $waybill->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <p class="text-lg font-medium text-gray-700">No rejected waybills</p>
                                <p class="text-sm text-gray-500 mt-1">All waybills have been approved or are in progress.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $waybills->links() }}
    </div>
</div>
