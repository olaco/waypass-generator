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

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Approval Progress</span>
                    <span class="text-sm font-medium text-gray-700">{{ $progress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-gray-800 h-2.5 rounded-full transition-all duration-500"
                         style="width: {{ $progress }}%"></div>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="mb-6">
                <span class="px-3 py-1 text-sm font-medium rounded-full
                    @if($currentStage === 'approved')
                        bg-green-100 text-green-800
                    @elseif(str_contains($currentStage, 'pending'))
                        bg-yellow-100 text-yellow-800
                    @else
                        bg-gray-100 text-gray-800
                    @endif">
                    @if($currentStage === 'approved')
                        ✅ Fully Approved
                    @else
                        ⏳ {{ ucfirst(str_replace('_', ' ', $currentStage)) }}
                    @endif
                </span>
                @if($nextApprover && $nextApprover !== 'Complete')
                    <span class="ml-2 text-sm text-gray-500">
                        Next: {{ $nextApprover }}
                    </span>
                @endif
            </div>

            <!-- Approval Chain -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                @foreach(['warehouse_manager', 'auditor', 'supply_chain_manager'] as $stage)
                    @php
                        $status = $approvalStatus[$stage] ?? ['approved' => false, 'name' => null, 'date' => null];
                        $isApproved = $status['approved'];
                    @endphp
                    <div class="border rounded-lg p-4 {{ $isApproved ? 'border-gray-800 bg-gray-50' : 'border-gray-200' }}">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xl">{{ $this->getStageIcon($stage) }}</span>
                            <span class="font-medium text-gray-900">{{ $this->getStageLabel($stage) }}</span>
                            @if($isApproved)
                                <span class="ml-auto text-green-600">✅</span>
                            @else
                                <span class="ml-auto text-gray-400">⏳</span>
                            @endif
                        </div>

                        @if($isApproved)
                            <div class="text-sm text-gray-600">
                                <p>✅ Approved by: <strong>{{ $status['name'] ?? 'N/A' }}</strong></p>
                                @if($status['date'])
                                    <p class="text-xs text-gray-500">Date: {{ \Carbon\Carbon::parse($status['date'])->format('M d, Y H:i') }}</p>
                                @endif
                            </div>
                        @else
                            <div class="text-sm text-gray-400">
                                <p>⏳ Awaiting approval</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Approval Buttons - Only show to the relevant role -->
            <div class="flex flex-wrap gap-3">
                {{-- Warehouse Manager Button --}}
                @if($isWarehouseManager && !$isWarehouseApproved && $waybill->status === 'pending_warehouse')
                    <button wire:click="approveWarehouse"
                            class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                        🏢 Approve as Warehouse Manager
                    </button>
                @endif

                {{-- Auditor Button --}}
                @if($isAuditor && $isWarehouseApproved && !$isAuditApproved && $waybill->status === 'pending_auditor')
                    <button wire:click="approveAudit"
                            class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                        📋 Approve as Auditor
                    </button>
                @endif

                {{-- Supply Chain Manager Button --}}
                @if($isSupplyChainManager && $isAuditApproved && !$isSupplyChainApproved && $waybill->status === 'pending_supply_chain')
                    <button wire:click="approveSupplyChain"
                            class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                        🔗 Approve as Supply Chain Manager
                    </button>
                @endif

                {{-- Fully Approved Message --}}
                @if($currentStage === 'approved')
                    <div class="bg-green-100 text-green-800 font-bold py-2 px-6 rounded-lg">
                        ✅ Waybill Fully Approved!
                        @if($waybill->gatepass_no)
                            <br><span class="text-sm">Gate Pass: {{ $waybill->gatepass_no }}</span>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Gate Pass Display -->
            @if($waybill->gatepass_no)
                <div class="mt-4 p-4 bg-gray-100 border border-gray-300 rounded-lg">
                    <p class="text-sm font-medium text-gray-700">Gate Pass Number</p>
                    <p class="text-xl font-bold text-gray-900">{{ $waybill->gatepass_no }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
