<?php

namespace App\Livewire\Waybill;

use App\Models\Waybill;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class DistOfficerWaybills extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;
    public $showSubmitModal = false;
    public $selectedWaybill = null;
    public $showDeleteModal = false;
    public $waybillToDelete = null;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openSubmitModal($waybillId)
    {
        $this->selectedWaybill = Waybill::with(['items'])->find($waybillId);
        $this->showSubmitModal = true;
    }

    public function closeSubmitModal()
    {
        $this->showSubmitModal = false;
        $this->selectedWaybill = null;
    }

    public function submitForApproval()
    {
        if (!$this->selectedWaybill) {
            session()->flash('error', 'No waybill selected.');
            return;
        }

        $waybill = $this->selectedWaybill;

        if ($waybill->items->count() === 0) {
            session()->flash('error', 'Cannot submit a waybill with no items.');
            $this->closeSubmitModal();
            return;
        }

        if ($waybill->status !== 'draft') {
            session()->flash('error', 'This waybill has already been submitted.');
            $this->closeSubmitModal();
            return;
        }

        $waybill->update([
            'status' => 'pending_warehouse',
        ]);

        session()->flash('message', '✅ Waybill submitted for approval successfully!');
        $this->closeSubmitModal();
        $this->dispatch('refreshComponent');
    }

    public function openDeleteModal($waybillId)
    {
        $this->waybillToDelete = Waybill::find($waybillId);
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->waybillToDelete = null;
    }

    public function deleteWaybill()
    {
        if (!$this->waybillToDelete) {
            session()->flash('error', 'No waybill selected.');
            return;
        }

        $waybill = $this->waybillToDelete;

        if ($waybill->status !== 'draft') {
            session()->flash('error', 'Only draft waybills can be deleted.');
            $this->closeDeleteModal();
            return;
        }

        $waybill->items()->delete();
        $waybill->delete();

        session()->flash('message', '🗑️ Waybill deleted successfully.');
        $this->closeDeleteModal();
        $this->dispatch('refreshComponent');
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'pending_warehouse', 'pending_auditor', 'pending_supply_chain', 'pending_final' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected', 'returned' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabel($status)
    {
        return ucfirst(str_replace('_', ' ', $status));
    }

    public function getApprovalProgress($waybill)
    {
        $completed = 0;
        if ($waybill->isWarehouseApproved()) $completed++;
        if ($waybill->isAuditApproved()) $completed++;
        if ($waybill->isSupplyChainApproved()) $completed++;
        $total = 3;
        return round(($completed / $total) * 100);
    }

    /**
     * Check if waybill is fully approved
     */
    public function isFullyApproved($waybill)
    {
        return $waybill->status === 'approved' && $waybill->isFullyApproved();
    }

    public function render()
    {
        $query = Waybill::with(['items', 'preparer'])
            ->where('prepared_by', Auth::id());

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('reference_no', 'like', '%' . $this->search . '%')
                  ->orWhere('waybill_no', 'like', '%' . $this->search . '%');
            });
        }

        $waybills = $query->latest()->paginate($this->perPage);

        $counts = [
            'total' => Waybill::where('prepared_by', Auth::id())->count(),
            'draft' => Waybill::where('prepared_by', Auth::id())->where('status', 'draft')->count(),
            'pending' => Waybill::where('prepared_by', Auth::id())
                ->whereIn('status', ['pending_warehouse', 'pending_auditor', 'pending_supply_chain', 'pending_final'])
                ->count(),
            'approved' => Waybill::where('prepared_by', Auth::id())->where('status', 'approved')->count(),
            'rejected' => Waybill::where('prepared_by', Auth::id())->where('status', 'rejected')->count(),
        ];

        return view('livewire.waybill.dist-officer-waybills', [
            'waybills' => $waybills,
            'counts' => $counts,
        ]);
    }
}
