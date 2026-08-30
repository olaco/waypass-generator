<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Approved Waybills</h2>
        <p class="text-sm text-gray-500 mt-1">Fully approved waybills</p>
    </div>

    <div class="mb-6">
        <input type="text"
               wire:model.live.debounce.300ms="search"
               placeholder="Search by reference or waybill no..."
               class="w-full md:w-1/3 rounded border-gray-300 focus:border-gray-500 focus:ring-gray-500">
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waybill No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gate Pass</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($waybills as $waybill)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $waybill->reference_no }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $waybill->waybill_no }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $waybill->gatepass_no ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $waybill->items->count() }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $waybill->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">No approved waybills</td>
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
