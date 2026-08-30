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
        <h2 class="text-2xl font-bold text-gray-900">📋 My Waybills</h2>
        <p class="text-sm text-gray-500 mt-1">Manage your waybills</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ $counts['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-400">
            <p class="text-sm text-gray-500">Draft</p>
            <p class="text-2xl font-bold text-gray-900">{{ $counts['draft'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-400">
            <p class="text-sm text-gray-500">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $counts['pending'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-400">
            <p class="text-sm text-gray-500">Approved</p>
            <p class="text-2xl font-bold text-green-600">{{ $counts['approved'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-400">
            <p class="text-sm text-gray-500">Rejected</p>
            <p class="text-2xl font-bold text-red-600">{{ $counts['rejected'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <input type="text"
               wire:model.live.debounce.300ms="search"
               placeholder="Search by reference or waybill no..."
               class="rounded border-gray-300 focus:border-gray-500 focus:ring-gray-500">
        <select wire:model.live="statusFilter"
                class="rounded border-gray-300 focus:border-gray-500 focus:ring-gray-500">
            <option value="">All Statuses</option>
            <option value="draft">Draft</option>
            <option value="pending_warehouse">Pending Warehouse</option>
            <option value="pending_auditor">Pending Auditor</option>
            <option value="pending_supply_chain">Pending Supply Chain</option>
            <option value="pending_final">Pending Final</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waybill No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($waybills as $waybill)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-4 py-4 text-sm font-medium text-gray-900">{{ $waybill->reference_no }}</td>
                        <td class="px-4 py-4 text-sm text-gray-500">{{ $waybill->waybill_no }}</td>
                        <td class="px-4 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $this->getStatusBadgeClass($waybill->status) }}">
                                {{ $this->getStatusLabel($waybill->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500">{{ $waybill->items->count() }}</td>
                        <td class="px-4 py-4">
                            @if($waybill->status !== 'draft' && $waybill->status !== 'approved' && $waybill->status !== 'rejected')
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-gray-800 h-2 rounded-full" style="width: {{ $this->getApprovalProgress($waybill) }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $this->getApprovalProgress($waybill) }}%</span>
                                </div>
                            @elseif($waybill->status === 'approved')
                                <span class="text-xs text-green-600">✅ Approved</span>
                            @elseif($waybill->status === 'rejected')
                                <span class="text-xs text-red-600">❌ Rejected</span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500">{{ $waybill->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                @if($waybill->status === 'draft')
                                    <a href="{{ route('waybills.edit', $waybill) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button wire:click="openSubmitModal({{ $waybill->id }})" class="text-green-600 hover:text-green-800" title="Submit for Approval">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                    </button>
                                    <button wire:click="openDeleteModal({{ $waybill->id }})" class="text-red-600 hover:text-red-800" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                @else
                                    <a href="{{ route('waybills.show', $waybill) }}" class="text-gray-600 hover:text-gray-900" title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    {{-- PDF Download Button - Only when fully approved --}}
                                    @if($waybill->status === 'approved' && $waybill->isFullyApproved())
                                        <a href="{{ route('waybills.pdf', $waybill) }}"
                                           target="_blank"
                                           class="text-red-600 hover:text-red-800 transition duration-150"
                                           title="Download PDF">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v6m0 0l-3-3m3 3l3-3"/>
                                            </svg>
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-lg font-medium text-gray-700">No waybills found</p>
                                <p class="text-sm text-gray-500 mt-1">Create your first waybill to get started.</p>
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

    <!-- Submit Modal -->
    @if($showSubmitModal && $selectedWaybill)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeSubmitModal"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Submit for Approval</h3>
                    <button wire:click="closeSubmitModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-600">Submit this waybill for approval:</p>
                    <div class="mt-2 p-3 bg-gray-50 rounded">
                        <p><strong>Reference:</strong> {{ $selectedWaybill->reference_no }}</p>
                        <p><strong>Waybill No:</strong> {{ $selectedWaybill->waybill_no }}</p>
                        <p><strong>Items:</strong> {{ $selectedWaybill->items->count() }}</p>
                    </div>
                </div>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4">
                    <p class="text-sm text-yellow-700">⚠️ Once submitted, this waybill cannot be edited.</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button wire:click="closeSubmitModal" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded">Cancel</button>
                    <button wire:click="submitForApproval" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">📤 Submit</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Modal -->
    @if($showDeleteModal && $waybillToDelete)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeDeleteModal"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Delete Waybill</h3>
                    <button wire:click="closeDeleteModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-600">Delete this waybill?</p>
                    <div class="mt-2 p-3 bg-gray-50 rounded">
                        <p><strong>Reference:</strong> {{ $waybillToDelete->reference_no }}</p>
                        <p><strong>Waybill No:</strong> {{ $waybillToDelete->waybill_no }}</p>
                    </div>
                </div>
                <div class="bg-red-50 border-l-4 border-red-500 p-3 mb-4">
                    <p class="text-sm text-red-700">⚠️ This action cannot be undone.</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button wire:click="closeDeleteModal" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded">Cancel</button>
                    <button wire:click="deleteWaybill" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">🗑️ Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
