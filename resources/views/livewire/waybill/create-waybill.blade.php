<div>
    @if(session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('message') }}
            </div>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Create New Waybill</h2>
                <span class="text-sm text-gray-500">Prepared by: {{ Auth::user()->name ?? 'Not logged in' }}</span>
            </div>

            <form wire:submit="save">
                <!-- ==========================================
                SECTION 1: DOCUMENT INFO (3 columns)
                ========================================== -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="reference_no" class="block text-xs font-medium text-gray-700 uppercase tracking-wider">Reference No</label>
                        <input type="text"
                               wire:model="reference_no"
                               id="reference_no"
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                               readonly>
                        <p class="text-xs text-gray-400 mt-1">Auto-generated</p>
                        @error('reference_no')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="waybill_no" class="block text-xs font-medium text-gray-700 uppercase tracking-wider">Waybill No</label>
                        <input type="text"
                               wire:model="waybill_no"
                               id="waybill_no"
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                               readonly>
                        <p class="text-xs text-gray-400 mt-1">Auto-generated</p>
                        @error('waybill_no')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="loaded_at" class="block text-xs font-medium text-gray-700 uppercase tracking-wider">Loaded At</label>
                        <input type="datetime-local"
                               wire:model="loaded_at"
                               id="loaded_at"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('loaded_at')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- ==========================================
                SECTION 2: LOGISTICS INFO (3 columns)
                ========================================== -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label for="vehicle_no" class="block text-xs font-medium text-gray-700 uppercase tracking-wider">Vehicle No</label>
                        <input type="text"
                               wire:model="vehicle_no"
                               id="vehicle_no"
                               placeholder="e.g., ABC-123"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('vehicle_no')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="vehicle_owner" class="block text-xs font-medium text-gray-700 uppercase tracking-wider">Vehicle Owner</label>
                        <input type="text"
                               wire:model="vehicle_owner"
                               id="vehicle_owner"
                               placeholder="Owner name"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('vehicle_owner')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="delivery_to" class="block text-xs font-medium text-gray-700 uppercase tracking-wider">Delivery To</label>
                        <input type="text"
                               wire:model="delivery_to"
                               id="delivery_to"
                               placeholder="Destination"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('delivery_to')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- ==========================================
                SECTION 3: REMOVER INFO (2 columns)
                ========================================== -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="remover_name" class="block text-xs font-medium text-gray-700 uppercase tracking-wider">Remover's Name</label>
                        <input type="text"
                               wire:model="remover_name"
                               id="remover_name"
                               placeholder="Full name of person loading/unloading"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('remover_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-end">
                        <div class="w-full">
                            <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider">Remover's Signature</label>
                            <div class="mt-1 flex items-center gap-3">
                                <button type="button"
                                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-md text-sm transition duration-200 cursor-not-allowed opacity-60">
                                    ✍️ Sign Here
                                </button>
                                <span class="text-xs text-gray-500">(Will be captured after creation)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==========================================
                SECTION 4: REMARKS
                ========================================== -->
                <div class="mt-4">
                    <label for="remarks" class="block text-xs font-medium text-gray-700 uppercase tracking-wider">Remarks</label>
                    <textarea wire:model="remarks"
                              id="remarks"
                              rows="2"
                              placeholder="Additional notes or special conditions..."
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                    @error('remarks')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ==========================================
                SECTION 5: ITEMS (COMPLETE WITH ALL FIELDS)
                ========================================== -->
                <div class="mt-8">
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Items</h3>
                            <p class="text-xs text-gray-500">Product, Batch No, Cartons, and Quantity</p>
                        </div>
                        <button type="button"
                                wire:click="addItem"
                                class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-1.5 px-4 rounded-md transition duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Item
                        </button>
                    </div>

                    <div class="space-y-3">
                        @foreach($items as $index => $item)
                        <div class="grid grid-cols-12 gap-3 items-end p-3 bg-gray-50 rounded-md border border-gray-200">
                            <!-- Product - 4 columns -->
                            <div class="col-span-12 md:col-span-4">
                                <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider">Product</label>
                                <select wire:model="items.{{ $index }}.product_description"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product }}">{{ $product }}</option>
                                    @endforeach
                                </select>
                                @error("items.{$index}.product_description")
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Batch No - 3 columns -->
                            <div class="col-span-12 md:col-span-3">
                                <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider">Batch No</label>
                                <input type="text"
                                       wire:model="items.{{ $index }}.batch_no"
                                       placeholder="e.g., BATCH-001"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @error("items.{$index}.batch_no")
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Cartons - 2 columns -->
                            <div class="col-span-6 md:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider">Cartons</label>
                                <input type="number"
                                       wire:model="items.{{ $index }}.no_of_cartons"
                                       min="1"
                                       placeholder="0"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @error("items.{$index}.no_of_cartons")
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Quantity - 2 columns -->
                            <div class="col-span-5 md:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 uppercase tracking-wider">Quantity</label>
                                <input type="number"
                                       wire:model="items.{{ $index }}.quantity_loaded"
                                       min="1"
                                       placeholder="0"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @error("items.{$index}.quantity_loaded")
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Remove - 1 column -->
                            <div class="col-span-1 md:col-span-1 flex justify-end">
                                @if(count($items) > 1)
                                    <button type="button"
                                            wire:click="removeItem({{ $index }})"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium p-1 hover:bg-red-50 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- ==========================================
                SECTION 6: SUBMIT BUTTONS
                ========================================== -->
                <div class="mt-8 flex flex-wrap gap-3">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md transition duration-200 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Waybill
                    </button>
                    <a href="{{ route('waybills.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-md transition duration-200 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
