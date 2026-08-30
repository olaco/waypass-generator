<?php

namespace App\Livewire\Waybill;

use App\Models\Waybill;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class WaybillDetails extends Component
{
    public Waybill $waybill;
    public $isDistOfficer = false;
    public $isWarehouseManager = false;
    public $isAuditor = false;
    public $isSupplyChainManager = false;
    public $isAdmin = false;

    public function mount(Waybill $waybill)
    {
        $this->waybill = $waybill->load(['items', 'preparer', 'approvals.approver']);

        // Set role flags
        $user = Auth::user();
        $this->isDistOfficer = $user->role === 'dist_officer';
        $this->isWarehouseManager = $user->role === 'warehouse_manager';
        $this->isAuditor = $user->role === 'auditor';
        $this->isSupplyChainManager = $user->role === 'supply_chain_manager';
        $this->isAdmin = $user->role === 'admin';
    }

    /**
     * Submit the waybill for approval (Distribution Officer only)
     */
    public function submitForApproval()
    {
        // Only Distribution Officer can submit
        if (!$this->isDistOfficer) {
            session()->flash('error', 'You are not authorized to submit waybills.');
            return;
        }

        // Only draft waybills can be submitted
        if ($this->waybill->status !== 'draft') {
            session()->flash('error', 'This waybill has already been submitted.');
            return;
        }

        // Check if it has items
        if ($this->waybill->items->count() === 0) {
            session()->flash('error', 'Cannot submit a waybill with no items.');
            return;
        }

        $this->waybill->update([
            'status' => 'pending_warehouse',
        ]);

        session()->flash('message', '✅ Waybill submitted for approval successfully!');
        $this->waybill->refresh();
    }

    public function render()
    {
        return view('livewire.waybill.waybill-details');
    }
}
