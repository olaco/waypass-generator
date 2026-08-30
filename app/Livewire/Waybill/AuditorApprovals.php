<?php

namespace App\Livewire\Waybill;

use App\Models\Waybill;
use App\Models\WaybillApproval;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class AuditorApprovals extends Component
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

    /**
     * Reset pagination when search updates
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Open approval modal
     */
    public function openApprovalModal($waybillId)
    {
        $this->selectedWaybill = Waybill::with(['items', 'preparer', 'approvals.approver'])->find($waybillId);
        $this->showApprovalModal = true;
    }

    /**
     * Close approval modal
     */
    public function closeApprovalModal()
    {
        $this->showApprovalModal = false;
        $this->selectedWaybill = null;
    }

    /**
     * Approve as Auditor
     */
    public function approveAsAuditor()
    {
        // Validate that a waybill is selected
        if (!$this->selectedWaybill) {
            session()->flash('error', 'No waybill selected.');
            return;
        }

        $waybill = $this->selectedWaybill;

        // Check if status is pending_auditor
        if ($waybill->status !== 'pending_auditor') {
            session()->flash('error', 'This waybill is not pending auditor approval.');
            $this->closeApprovalModal();
            return;
        }

        // Check if already approved
        if ($waybill->isAuditApproved()) {
            session()->flash('error', 'This waybill has already been approved by Auditor.');
            $this->closeApprovalModal();
            return;
        }

        // Update waybill
        $waybill->update([
            'auditor_approver_name' => Auth::user()->name,
            'auditor_approved_at' => now(),
            'status' => 'pending_supply_chain',
        ]);

        // Create approval record
        WaybillApproval::create([
            'waybill_id' => $waybill->id,
            'approver_id' => Auth::id(),
            'stage' => 'auditor',
            'status' => 'approved',
            'remarks' => 'Approved by Auditor',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'approved_at' => now(),
        ]);

        session()->flash('message', '✅ Waybill approved by Auditor and forwarded to Supply Chain Manager.');
        $this->closeApprovalModal();
        $this->dispatch('refreshComponent');
    }

    /**
     * Open rejection modal
     */
    public function openRejectModal($waybillId)
    {
        $this->waybillToReject = Waybill::find($waybillId);
        $this->showRejectModal = true;
        $this->rejectionReason = '';
    }

    /**
     * Close rejection modal
     */
    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->waybillToReject = null;
        $this->rejectionReason = '';
    }

    /**
     * Reject waybill
     */
    public function rejectWaybill()
    {
        $this->validate();

        if (!$this->waybillToReject) {
            session()->flash('error', 'No waybill selected.');
            return;
        }

        $waybill = $this->waybillToReject;

        // Check if status is pending_auditor
        if ($waybill->status !== 'pending_auditor') {
            session()->flash('error', 'This waybill is not pending auditor approval.');
            $this->closeRejectModal();
            return;
        }

        // Update waybill status to rejected
        $waybill->update([
            'status' => 'rejected',
        ]);

        // Create rejection record
        WaybillApproval::create([
            'waybill_id' => $waybill->id,
            'approver_id' => Auth::id(),
            'stage' => 'auditor',
            'status' => 'rejected',
            'remarks' => $this->rejectionReason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'approved_at' => now(),
        ]);

        session()->flash('message', '❌ Waybill rejected by Auditor.');
        $this->closeRejectModal();
        $this->dispatch('refreshComponent');
    }

    public function render()
    {
        $waybills = Waybill::with(['items', 'preparer', 'approvals.approver'])
            ->where('status', 'pending_auditor')
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('reference_no', 'like', '%' . $this->search . '%')
                      ->orWhere('waybill_no', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        $counts = [
            'pending' => Waybill::where('status', 'pending_auditor')->count(),
            'total' => Waybill::count(),
        ];

        return view('livewire.waybill.auditor-approvals', [
            'waybills' => $waybills,
            'counts' => $counts,
        ]);
    }
}
