<div>
    @if(session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Supply Chain Manager Approvals</h2>
        <p class="text-sm text-gray-500 mt-1">Waybills awaiting your final approval</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Pending Your Approval</p>
            <p class="text-2xl font-bold text-gray-900">{{ $counts['pending'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Total Waybills</p>
            <p class="text-2xl font-bold text-gray-900">{{ $counts['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Role</p>
            <p class="text-lg font-bold text-blue-600">🔗 Supply Chain Manager</p>
        </div>
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waybill No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prepared By</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approval Trail</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($waybills as $waybill)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-4 py-4 text-sm font-medium text-gray-900">
                            {{ $waybill->reference_no }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500">
                            {{ $waybill->waybill_no }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500">
                            {{ $waybill->items->count() }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500">
                            {{ $waybill->preparer->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500">
                            {{ $waybill->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-4 py-4">
                            <button wire:click="$set('selectedWaybill', {{ $waybill->id }})"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Trail
                            </button>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <button wire:click="openApprovalModal({{ $waybill->id }})"
                                        class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded transition duration-150">
                                    ✅ Approve
                                </button>
                                <button wire:click="openRejectModal({{ $waybill->id }})"
                                        class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded transition duration-150">
                                    ❌ Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-lg font-medium text-gray-700">No waybills pending your approval</p>
                                <p class="text-sm text-gray-500 mt-1">All waybills have been processed.</p>
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

    <!-- Approval Modal -->
    @if($showApprovalModal && $selectedWaybill)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeApprovalModal"></div>

            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Confirm Final Approval</h3>
                    <button wire:click="closeApprovalModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600">You are about to approve the following waybill:</p>
                    <div class="mt-2 p-3 bg-gray-50 rounded">
                        <p><strong>Reference:</strong> {{ $selectedWaybill->reference_no }}</p>
                        <p><strong>Waybill No:</strong> {{ $selectedWaybill->waybill_no }}</p>
                        <p><strong>Items:</strong> {{ $selectedWaybill->items->count() }}</p>
                        <p><strong>Prepared By:</strong> {{ $selectedWaybill->preparer->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4">
                    <p class="text-sm text-yellow-700">
                        ⚠️ This is the final approval. A Gate Pass will be generated automatically.
                    </p>
                </div>

                <div class="flex justify-end gap-3">
                    <button wire:click="closeApprovalModal"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded transition duration-150">
                        Cancel
                    </button>
                    <button wire:click="approveAsSupplyChain"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded transition duration-150">
                        ✅ Confirm Final Approval
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Rejection Modal -->
    @if($showRejectModal && $waybillToReject)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeRejectModal"></div>

            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Reject Waybill</h3>
                    <button wire:click="closeRejectModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600">You are about to reject the following waybill:</p>
                    <div class="mt-2 p-3 bg-gray-50 rounded">
                        <p><strong>Reference:</strong> {{ $waybillToReject->reference_no }}</p>
                        <p><strong>Waybill No:</strong> {{ $waybillToReject->waybill_no }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Rejection Reason</label>
                    <textarea wire:model="rejectionReason"
                              rows="3"
                              class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                              placeholder="Please provide a reason for rejection..."></textarea>
                    @error('rejectionReason')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button wire:click="closeRejectModal"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded transition duration-150">
                        Cancel
                    </button>
                    <button wire:click="rejectWaybill"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded transition duration-150">
                        ❌ Confirm Rejection
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
