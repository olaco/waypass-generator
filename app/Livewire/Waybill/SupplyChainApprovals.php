<?php

namespace App\Livewire\Waybill;

use App\Models\Waybill;
use App\Models\WaybillApproval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class SupplyChainApprovals extends Component
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

    public function mount(): void
    {
        $this->authorizeSupplyChainStage();
    }

    private function authorizeSupplyChainStage(): void
    {
        abort_unless(
            auth()->user()?->hasRole('supply_chain_manager'),
            403
        );
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openApprovalModal($waybillId)
    {
        $this->authorizeSupplyChainStage();
        $this->selectedWaybill = Waybill::with(['items', 'preparer'])->find($waybillId);
        $this->showApprovalModal = true;
    }

    public function closeApprovalModal()
    {
        $this->showApprovalModal = false;
        $this->selectedWaybill = null;
    }

    public function approveAsSupplyChain()
    {
        $this->authorizeSupplyChainStage();
        if (! $this->selectedWaybill) {
            session()->flash('error', 'No waybill selected.');

            return;
        }

        $waybill = Waybill::findOrFail($this->selectedWaybill->id);

        if ($waybill->isSupplyChainApproved()) {
            session()->flash('error', 'This waybill has already been approved by Supply Chain Manager.');
            $this->closeApprovalModal();

            return;
        }

        if ($waybill->status !== 'pending_supply_chain') {
            session()->flash('error', 'This waybill is not pending supply chain approval.');
            $this->closeApprovalModal();

            return;
        }

        $gatepassNo = DB::transaction(function () use ($waybill) {
            // Generate Gate Pass
            $gatepassNo = 'GP-'.date('Y').'-'.str_pad(Waybill::count() + 1, 6, '0', STR_PAD_LEFT);

            $waybill->update([
                'supply_chain_approver_name' => Auth::user()->name,
                'supply_chain_approved_at' => now(),
                'status' => 'approved',
                'gatepass_no' => $gatepassNo,
            ]);

            WaybillApproval::create([
                'waybill_id' => $waybill->id,
                'approver_id' => Auth::id(),
                'stage' => 'supply_chain_manager',
                'status' => 'approved',
                'remarks' => 'Approved by Supply Chain Manager',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'approved_at' => now(),
            ]);

            return $gatepassNo;
        });

        session()->flash('message', '✅ Waybill fully approved! Gate Pass: '.$gatepassNo);
        $this->closeApprovalModal();
        $this->dispatch('refreshComponent');
    }

    public function openRejectModal($waybillId)
    {
        $this->authorizeSupplyChainStage();
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
        $this->authorizeSupplyChainStage();
        $this->validate();

        if (! $this->waybillToReject) {
            session()->flash('error', 'No waybill selected.');

            return;
        }

        $waybill = Waybill::findOrFail($this->waybillToReject->id);
        if ($waybill->status !== 'pending_supply_chain') {
            session()->flash('error', 'This waybill is not pending supply chain approval.');
            $this->closeRejectModal();

            return;
        }

        DB::transaction(function () use ($waybill) {
            $waybill->update([
                'status' => 'rejected',
            ]);

            WaybillApproval::create([
                'waybill_id' => $waybill->id,
                'approver_id' => Auth::id(),
                'stage' => 'supply_chain_manager',
                'status' => 'rejected',
                'remarks' => $this->rejectionReason,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'approved_at' => now(),
            ]);
        });

        session()->flash('message', '❌ Waybill rejected by Supply Chain Manager.');
        $this->closeRejectModal();
        $this->dispatch('refreshComponent');
    }

    public function render()
    {
        $waybills = Waybill::with(['items', 'preparer', 'approvals.approver'])
            ->where('status', 'pending_supply_chain')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('reference_no', 'like', '%'.$this->search.'%')
                        ->orWhere('waybill_no', 'like', '%'.$this->search.'%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        $counts = [
            'pending' => Waybill::where('status', 'pending_supply_chain')->count(),
            'total' => Waybill::count(),
        ];

        return view('livewire.waybill.supply-chain-approvals', [
            'waybills' => $waybills,
            'counts' => $counts,
        ]);
    }
}
