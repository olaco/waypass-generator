<?php

namespace App\Livewire\Waybill;

use App\Models\Waybill;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateWaybill extends Component
{
    // Document fields
    public $reference_no;
    public $waybill_no;
    public $loaded_at;

    // Logistics fields
    public $vehicle_no;
    public $vehicle_owner;
    public $delivery_to;
    public $remarks;

    // Remover fields
    public $remover_name;

    // Items
    public $items = [];
    public $itemCount = 0;

    // ===== VALIDATION RULES =====
    protected $rules = [
        'reference_no' => 'required|unique:waybills,reference_no',
        'waybill_no' => 'required|unique:waybills,waybill_no',
        'loaded_at' => 'nullable|date',
        'vehicle_no' => 'nullable|string|max:255',
        'vehicle_owner' => 'nullable|string|max:255',
        'delivery_to' => 'nullable|string|max:255',
        'remarks' => 'nullable|string',
        'remover_name' => 'nullable|string|max:255',
        'items.*.product_description' => 'required|string',
        'items.*.batch_no' => 'required|string',
        'items.*.no_of_cartons' => 'required|integer|min:0',
        'items.*.quantity_loaded' => 'required|integer|min:1',
    ];

    protected $validationAttributes = [
        'reference_no' => 'Reference Number',
        'waybill_no' => 'Waybill Number',
        'items.*.product_description' => 'Product Description',
        'items.*.batch_no' => 'Batch Number',
        'items.*.no_of_cartons' => 'Number of Cartons',
        'items.*.quantity_loaded' => 'Quantity Loaded',
    ];

    // ===== LIFECYCLE HOOKS =====

    public function mount()
    {
        // Auto-generate numbers based on sequence
        $this->generateDocumentNumbers();

        // Start with one empty item
        $this->addItem();
    }

    // ===== AUTO-GENERATE NUMBERS =====

    protected function generateDocumentNumbers()
    {
        // Get the next sequence number
        $nextId = Waybill::max('id') + 1;

        // Format: EB-2026-000001 (Reference No)
        $this->reference_no = 'EB-' . date('Y') . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        // Format: WB-2026-000001 (Waybill No)
        $this->waybill_no = 'WB-' . date('Y') . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    // ===== ITEM MANAGEMENT =====

    public function addItem()
    {
        $this->items[] = [
            'product_description' => '',
            'batch_no' => '',
            'no_of_cartons' => 1,
            'quantity_loaded' => 1,
        ];
        $this->itemCount++;
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->itemCount--;
    }

    // ===== SAVE =====
public function save()
{
    $validated = $this->validate();

    $preparedBy = Auth::id();

    if (!$preparedBy) {
        session()->flash('error', 'You must be logged in to create a waybill.');
        return;
    }

    $waybill = DB::transaction(function () use ($validated, $preparedBy) {

        $waybill = Waybill::create([
            'reference_no' => $validated['reference_no'],
            'waybill_no' => $validated['waybill_no'],
            'gatepass_no' => null,
            'loaded_at' => $validated['loaded_at'],
            'status' => 'draft',
            'prepared_by' => $preparedBy,
            'vehicle_no' => $validated['vehicle_no'],
            'vehicle_owner' => $validated['vehicle_owner'],
            'delivery_to' => $validated['delivery_to'],
            'remarks' => $validated['remarks'],
            'remover_name' => $validated['remover_name'],
        ]);

        foreach ($validated['items'] ?? [] as $item) {
            $waybill->items()->create($item);
        }

        return $waybill;
    });

    session()->flash('message', 'Waybill created successfully!');

    return redirect()->route('waybills.index');
}


    // ========================

    // ===== RENDER =====

    public function render()
    {
        $products = [
            'Glucose Classic', 'Glucose Bulk', 'Dequadin 100', 'Dequadin 250',
            'Algafen Suspension', 'Algafen 400 Tab', 'Algafen 200 Tab',
            'Globak syrup', 'Plexitone syrup', 'Piriton Expectorant Child Syrup',
            'Piriton Expectorant Adult Syrup', 'Piriton Syrup', 'Evacid 100ml',
            'Evacid 200ml', 'Cotrim', 'Metronidazole', 'Ravimal x 6', 'Ravimal x 12',
            'Ravimal x 18', 'Ravimal X 24', 'Ravimal Adult', 'Cofta Syrup',
            'Cofta Tablet', 'Neurogab', 'Prostagel', 'Manxtra 10', 'Manxtra 20'
        ];

        return view('livewire.waybill.create-waybill', [
            'products' => $products,
        ]);
    }
}
