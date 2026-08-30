<?php

namespace App\Livewire\Waybill;

use App\Models\Waybill;
use Livewire\Component;
use Livewire\WithPagination;

class WaybillApprovals extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    /**
     * Reset pagination when search query updates
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Get the pending approver for a waybill
     */
    public function getPendingApprover($waybill)
    {
        if (!$waybill->isWarehouseApproved()) {
            return 'Warehouse Manager';
        }

        if (!$waybill->isAuditApproved()) {
            return 'Auditor';
        }

        if (!$waybill->isSupplyChainApproved()) {
            return 'Supply Chain Manager';
        }

        return null;
    }

    /**
     * Get the approval stage label
     */
    public function getStageLabel($waybill)
    {
        if (!$waybill->isWarehouseApproved()) {
            return 'Pending Warehouse Manager';
        }

        if (!$waybill->isAuditApproved()) {
            return 'Pending Auditor';
        }

        if (!$waybill->isSupplyChainApproved()) {
            return 'Pending Supply Chain';
        }

        return 'Pending Final';
    }

    /**
     * Get completed approvals count
     */
    public function getCompletedCount($waybill)
    {
        $count = 0;
        if ($waybill->isWarehouseApproved()) $count++;
        if ($waybill->isAuditApproved()) $count++;
        if ($waybill->isSupplyChainApproved()) $count++;
        return $count;
    }

    /**
     * Get the status badge class for a waybill
     */
    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'pending_warehouse', 'pending_auditor', 'pending_supply_chain', 'pending_final' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'draft' => 'bg-gray-100 text-gray-800',
            'rejected', 'returned' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the status label
     */
    public function getStatusLabel($status)
    {
        return ucfirst(str_replace('_', ' ', $status));
    }

    /**
     * Get the approval progress percentage
     */
    public function getProgressPercentage($waybill)
    {
        $completed = $this->getCompletedCount($waybill);
        $total = 3;
        return round(($completed / $total) * 100);
    }

    /**
     * Check if a specific stage is approved
     */
    public function isStageApproved($waybill, $stage)
    {
        return match($stage) {
            'warehouse' => $waybill->isWarehouseApproved(),
            'audit' => $waybill->isAuditApproved(),
            'supply_chain' => $waybill->isSupplyChainApproved(),
            default => false,
        };
    }

    /**
     * Get the stage icon
     */
    public function getStageIcon($stage)
    {
        return match($stage) {
            'warehouse' => '🏢',
            'audit' => '📋',
            'supply_chain' => '🔗',
            default => '📄',
        };
    }

    /**
     * Get the stage label text
     */
    public function getStageLabelText($stage)
    {
        return match($stage) {
            'warehouse' => 'Warehouse Manager',
            'audit' => 'Auditor',
            'supply_chain' => 'Supply Chain Manager',
            default => ucfirst(str_replace('_', ' ', $stage)),
        };
    }

    /**
     * Get the next approver role with pending status
     */
    public function getNextApproverWithStatus($waybill)
    {
        if (!$waybill->isWarehouseApproved()) {
            return [
                'role' => 'Warehouse Manager',
                'status' => 'pending',
                'icon' => '⏳',
                'label' => 'Awaiting Warehouse Manager'
            ];
        }

        if (!$waybill->isAuditApproved()) {
            return [
                'role' => 'Auditor',
                'status' => 'pending',
                'icon' => '⏳',
                'label' => 'Awaiting Auditor'
            ];
        }

        if (!$waybill->isSupplyChainApproved()) {
            return [
                'role' => 'Supply Chain Manager',
                'status' => 'pending',
                'icon' => '⏳',
                'label' => 'Awaiting Supply Chain'
            ];
        }

        return null;
    }

    public function render()
    {
        $waybills = Waybill::with(['items', 'preparer', 'approvals.approver'])
            ->whereIn('status', ['pending_warehouse', 'pending_auditor', 'pending_supply_chain', 'pending_final'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('reference_no', 'like', '%' . $this->search . '%')
                      ->orWhere('waybill_no', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.waybill.waybill-approvals', [
            'waybills' => $waybills,
        ]);
    }
}

