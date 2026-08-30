<?php

namespace App\Livewire\Waybill;

use App\Models\Waybill;
use App\Models\WaybillApproval;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class WarehouseApprovals extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedWaybill = null;
    public $showApprovalModal = false;
    public $rejectionReason = '';
    public $showRejectModal = false;
    public $waybillToReject = null;

    protected $rules = [
        'rejectionReason' => 'required|string|min:5',
    ];

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openApprovalModal($waybillId)
    {
        $this->selectedWaybill = Waybill::with(['items', 'preparer', 'approvals.approver'])->find($waybillId);
        $this->showApprovalModal = true;
    }

    public function closeApprovalModal()
    {
        $this->showApprovalModal = false;
        $this->selectedWaybill = null;
    }

    public function approveAsWarehouse()
    {
        if (!$this->selectedWaybill) {
            session()->flash('error', 'No waybill selected.');
            return;
        }

        $waybill = $this->selectedWaybill;

        if ($waybill->status !== 'pending_warehouse') {
            session()->flash('error', 'This waybill is not pending warehouse approval.');
            $this->closeApprovalModal();
            return;
        }

        if ($waybill->isWarehouseApproved()) {
            session()->flash('error', 'This waybill has already been approved by Warehouse Manager.');
            $this->closeApprovalModal();
            return;
        }

        $waybill->update([
            'warehouse_approver_name' => Auth::user()->name,
            'warehouse_approved_at' => now(),
            'status' => 'pending_auditor',
        ]);

        WaybillApproval::create([
            'waybill_id' => $waybill->id,
            'approver_id' => Auth::id(),
            'stage' => 'warehouse_manager',
            'status' => 'approved',
            'remarks' => 'Approved by Warehouse Manager',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'approved_at' => now(),
        ]);

        session()->flash('message', '✅ Waybill approved by Warehouse Manager and forwarded to Auditor.');
        $this->closeApprovalModal();
        $this->dispatch('refreshComponent');
    }

    public function openRejectModal($waybillId)
    {
        $this->waybillToReject = Waybill::find($waybillId);
        $this->showRejectModal = true;
        $this->rejectionReason = '';
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->waybillToReject = null;
        $this->rejectionReason = '';
    }

    public function rejectWaybill()
    {
        $this->validate();

        if (!$this->waybillToReject) {
            session()->flash('error', 'No waybill selected.');
            return;
        }

        $waybill = $this->waybillToReject;

        if ($waybill->status !== 'pending_warehouse') {
            session()->flash('error', 'This waybill is not pending warehouse approval.');
            $this->closeRejectModal();
            return;
        }

        $waybill->update([
            'status' => 'rejected',
        ]);

        WaybillApproval::create([
            'waybill_id' => $waybill->id,
            'approver_id' => Auth::id(),
            'stage' => 'warehouse_manager',
            'status' => 'rejected',
            'remarks' => $this->rejectionReason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'approved_at' => now(),
        ]);

        session()->flash('message', '❌ Waybill rejected by Warehouse Manager.');
        $this->closeRejectModal();
        $this->dispatch('refreshComponent');
    }

    public function render()
    {
        $waybills = Waybill::with(['items', 'preparer', 'approvals.approver'])
            ->where('status', 'pending_warehouse')
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('reference_no', 'like', '%' . $this->search . '%')
                      ->orWhere('waybill_no', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        $counts = [
            'pending' => Waybill::where('status', 'pending_warehouse')->count(),
            'total' => Waybill::count(),
        ];

        return view('livewire.waybill.warehouse-approvals', [
            'waybills' => $waybills,
            'counts' => $counts,
        ]);
    }
}
