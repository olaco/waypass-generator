<?php

namespace App\Livewire\Waybill;

use App\Models\Waybill;
use App\Models\WaybillApproval;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ApprovalWorkflow extends Component
{
    public Waybill $waybill;
    public $isWarehouseApproved = false;
    public $isAuditApproved = false;
    public $isSupplyChainApproved = false;
    public $currentStage = '';
    public $nextApprover = '';
    public $progress = 0;
    public $approvalStatus = [];

    // Role checks
    public $isWarehouseManager = false;
    public $isAuditor = false;
    public $isSupplyChainManager = false;

    public function mount(Waybill $waybill)
    {
        $this->waybill = $waybill->load(['items', 'preparer', 'approvals.approver']);

        $user = Auth::user();
        $this->isWarehouseManager = $user->role === 'warehouse_manager';
        $this->isAuditor = $user->role === 'auditor';
        $this->isSupplyChainManager = $user->role === 'supply_chain_manager';

        $this->refreshApprovalStatus();
    }

    public function refreshApprovalStatus()
    {
        $this->waybill->refresh();
        $this->isWarehouseApproved = $this->waybill->isWarehouseApproved();
        $this->isAuditApproved = $this->waybill->isAuditApproved();
        $this->isSupplyChainApproved = $this->waybill->isSupplyChainApproved();
        $this->currentStage = $this->waybill->getCurrentApprovalStage();
        $this->nextApprover = $this->waybill->getNextApproverRole() ?? 'Complete';
        $this->progress = $this->waybill->getApprovalProgress();
        $this->approvalStatus = $this->waybill->getApprovalStatus();
    }

    public function approveWarehouse()
    {
        // Only Warehouse Manager can approve
        if (!$this->isWarehouseManager) {
            session()->flash('error', 'You are not authorized to approve as Warehouse Manager.');
            return;
        }

        if ($this->isWarehouseApproved) {
            session()->flash('error', 'Already approved by Warehouse Manager.');
            return;
        }

        if ($this->waybill->status !== 'pending_warehouse') {
            session()->flash('error', 'This waybill is not pending warehouse approval.');
            return;
        }

        $this->waybill->update([
            'warehouse_approver_name' => Auth::user()->name,
            'warehouse_approved_at' => now(),
            'status' => 'pending_auditor',
        ]);

        WaybillApproval::create([
            'waybill_id' => $this->waybill->id,
            'approver_id' => Auth::id(),
            'stage' => 'warehouse_manager',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $this->refreshApprovalStatus();
        session()->flash('message', '✅ Warehouse Manager approval completed.');
    }

    public function approveAudit()
    {
        // Only Auditor can approve
        if (!$this->isAuditor) {
            session()->flash('error', 'You are not authorized to approve as Auditor.');
            return;
        }

        if (!$this->isWarehouseApproved) {
            session()->flash('error', 'Warehouse Manager approval required first.');
            return;
        }

        if ($this->isAuditApproved) {
            session()->flash('error', 'Already approved by Audit.');
            return;
        }

        if ($this->waybill->status !== 'pending_auditor') {
            session()->flash('error', 'This waybill is not pending auditor approval.');
            return;
        }

        $this->waybill->update([
            'auditor_approver_name' => Auth::user()->name,
            'auditor_approved_at' => now(),
            'status' => 'pending_supply_chain',
        ]);

        WaybillApproval::create([
            'waybill_id' => $this->waybill->id,
            'approver_id' => Auth::id(),
            'stage' => 'auditor',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $this->refreshApprovalStatus();
        session()->flash('message', '✅ Audit approval completed.');
    }

    public function approveSupplyChain()
    {
        // Only Supply Chain Manager can approve
        if (!$this->isSupplyChainManager) {
            session()->flash('error', 'You are not authorized to approve as Supply Chain Manager.');
            return;
        }

        if (!$this->isAuditApproved) {
            session()->flash('error', 'Audit approval required first.');
            return;
        }

        if ($this->isSupplyChainApproved) {
            session()->flash('error', 'Already approved by Supply Chain.');
            return;
        }

        if ($this->waybill->status !== 'pending_supply_chain') {
            session()->flash('error', 'This waybill is not pending supply chain approval.');
            return;
        }

        $gatepassNo = 'GP-' . date('Y') . '-' . str_pad(Waybill::count() + 1, 6, '0', STR_PAD_LEFT);

        $this->waybill->update([
            'supply_chain_approver_name' => Auth::user()->name,
            'supply_chain_approved_at' => now(),
            'status' => 'approved',
            'gatepass_no' => $gatepassNo,
        ]);

        WaybillApproval::create([
            'waybill_id' => $this->waybill->id,
            'approver_id' => Auth::id(),
            'stage' => 'supply_chain_manager',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $this->refreshApprovalStatus();
        session()->flash('message', '✅ Supply Chain approval completed. Gate Pass: ' . $gatepassNo);
    }

    public function getStageIcon($stage)
    {
        return match($stage) {
            'warehouse_manager' => '🏢',
            'auditor' => '📋',
            'supply_chain_manager' => '🔗',
            default => '📄',
        };
    }

    public function getStageLabel($stage)
    {
        return match($stage) {
            'warehouse_manager' => 'Warehouse Manager',
            'auditor' => 'Audit',
            'supply_chain_manager' => 'Supply Chain Manager',
            default => ucfirst(str_replace('_', ' ', $stage)),
        };
    }

    public function render()
    {
        return view('livewire.waybill.approval-workflow');
    }
}
