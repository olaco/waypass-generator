<div>
    <div class="mb-6">
        <a href="{{ route('waybills.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Waybills
        </a>
    </div>

    <!-- Flash Messages -->
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

    <!-- Waybill Info -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $waybill->reference_no }}</h2>
                    <p class="text-sm text-gray-500">{{ $waybill->waybill_no }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 text-sm font-medium rounded-full
                        @if($waybill->isFullyApproved())
                            bg-green-100 text-green-800
                        @elseif(str_contains($waybill->status, 'pending'))
                            bg-yellow-100 text-yellow-800
                        @elseif($waybill->status === 'draft')
                            bg-gray-100 text-gray-800
                        @else
                            bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $waybill->status)) }}
                    </span>

                    {{-- Submit for Approval Button - ONLY for Distribution Officer and ONLY when draft --}}
                    @if($isDistOfficer && $waybill->status === 'draft')
                        <button wire:click="submitForApproval"
                                onclick="return confirm('Are you sure you want to submit this waybill for approval?')"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                            📤 Submit for Approval
                        </button>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500">Prepared By</p>
                    <p class="font-medium">{{ $waybill->preparer->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Loaded At</p>
                    <p class="font-medium">{{ $waybill->loaded_at ? $waybill->loaded_at->format('M d, Y H:i') : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Items</p>
                    <p class="font-medium">{{ $waybill->items->count() }} items</p>
                </div>
                @if($waybill->gatepass_no)
                <div>
                    <p class="text-sm text-gray-500">Gate Pass</p>
                    <p class="font-medium text-green-600">{{ $waybill->gatepass_no }}</p>
                </div>
                @endif
            </div>

            {{-- ========================================== --}}
            {{-- GATEPASS & WAYBILL ACTION CONTROLS         --}}
            {{-- ========================================== --}}
            <div class="pt-4 border-t border-gray-200 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    {{-- 👁️ Preview Gatepass: Always accessible across all statuses --}}
                    <a href="{{ route('gatepass.stream', $waybill->id) }}" target="_blank"
                       class="inline-flex items-center bg-slate-700 hover:bg-slate-800 text-white font-bold py-2 px-4 rounded-lg transition duration-200 text-sm shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Preview Gatepass
                    </a>

                    {{-- 📥 Download Gatepass: Enabled ONLY after full approval --}}
                    @if($this->isFullyApproved)
                        <button wire:click="downloadGatepass"
                                class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 text-sm shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Official Gatepass
                        </button>
                    @else
                        <button disabled
                                title="Download locked until Warehouse, Audit, and Supply Chain approvals are completed"
                                class="inline-flex items-center bg-gray-200 text-gray-500 border border-gray-300 font-bold py-2 px-4 rounded-lg text-sm cursor-not-allowed shadow-none opacity-80">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Download Locked (Pending Approval)
                        </button>
                    @endif
                </div>

                @if(!$waybill->isFullyApproved())
                    <span class="text-gray-500 text-xs sm:text-sm">
                        Waiting for: <strong>{{ $waybill->getNextApproverRole() }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Approval Workflow - Only show if NOT Distribution Officer or if status is not draft --}}
    @if(!$isDistOfficer || $waybill->status !== 'draft')
        @livewire('waybill.approval-workflow', ['waybill' => $waybill])
    @else
        {{-- Show simple pending message for Distribution Officer --}}
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">📋 Approval Status</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600">
                        @if($waybill->status === 'draft')
                            This waybill is currently in <strong>Draft</strong> status.
                            <br>Click the <strong>"Submit for Approval"</strong> button above to start the approval process.
                        @elseif($waybill->isFullyApproved())
                            ✅ This waybill has been <strong>Fully Approved</strong>.
                            @if($waybill->gatepass_no)
                                <br>Gate Pass: <strong>{{ $waybill->gatepass_no }}</strong>
                            @endif
                        @elseif(str_contains($waybill->status, 'pending'))
                            ⏳ This waybill is pending approval from:
                            <strong>{{ ucfirst(str_replace('pending_', '', $waybill->status)) }}</strong>
                        @else
                            📋 Status: <strong>{{ ucfirst(str_replace('_', ' ', $waybill->status)) }}</strong>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Items -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Loaded Items</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch No</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cartons</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($waybill->items as $index => $item)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->product_description }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $item->batch_no }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 text-right">{{ $item->no_of_cartons }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 text-right">{{ $item->quantity_loaded }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No items</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-medium text-gray-900">TOTALS</td>
                        <td class="px-6 py-3 text-right font-medium text-gray-900">{{ $waybill->items->sum('no_of_cartons') }}</td>
                        <td class="px-6 py-3 text-right font-medium text-gray-900">{{ $waybill->items->sum('quantity_loaded') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
