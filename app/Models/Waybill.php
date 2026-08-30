<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Waybill extends Model
{
    protected $fillable = [
        'reference_no',
        'waybill_no',
        'gatepass_no',
        'loaded_at',
        'status',
        'prepared_by',
        'vehicle_no',
        'vehicle_owner',
        'delivery_to',
        'remarks',
        'remover_name',
        'remover_signature_path',
        'remover_signature_data',
        'remover_signed_at',
        'warehouse_approver_name',
        'warehouse_approved_at',
        'auditor_approver_name',
        'auditor_approved_at',
        'supply_chain_approver_name',
        'supply_chain_approved_at',
        'final_approver_name',
        'final_approved_at',
    ];

    protected $casts = [
        'loaded_at' => 'datetime',
        'warehouse_approved_at' => 'datetime',
        'auditor_approved_at' => 'datetime',
        'supply_chain_approved_at' => 'datetime',
        'final_approved_at' => 'datetime',
        'remover_signed_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    public function items(): HasMany
    {
        return $this->hasMany(WaybillItem::class);
    }

    public function preparer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(WaybillApproval::class);
    }

    // ===== APPROVAL METHODS =====

    /**
     * Check if Warehouse Manager has approved
     */
    public function isWarehouseApproved(): bool
    {
        return !is_null($this->warehouse_approved_at);
    }

    /**
     * Check if Audit has approved
     */
    public function isAuditApproved(): bool
    {
        return !is_null($this->auditor_approved_at);
    }

    /**
     * Check if Supply Chain has approved
     */
    public function isSupplyChainApproved(): bool
    {
        return !is_null($this->supply_chain_approved_at);
    }

    /**
     * Check if all approvals are complete
     */
    public function isFullyApproved(): bool
    {
        return $this->isWarehouseApproved()
            && $this->isAuditApproved()
            && $this->isSupplyChainApproved();
    }

    /**
     * Get the current approval stage
     */
    public function getCurrentApprovalStage(): string
    {
        if ($this->isFullyApproved()) {
            return 'approved';
        }

        if ($this->isSupplyChainApproved()) {
            return 'pending_final';
        }

        if ($this->isAuditApproved()) {
            return 'pending_supply_chain';
        }

        if ($this->isWarehouseApproved()) {
            return 'pending_auditor';
        }

        return 'pending_warehouse';
    }

    /**
     * Get the next approver role
     */
    public function getNextApproverRole(): ?string
    {
        if ($this->isFullyApproved()) {
            return null;
        }

        if (!$this->isWarehouseApproved()) {
            return 'Warehouse Manager';
        }

        if (!$this->isAuditApproved()) {
            return 'Auditor';
        }

        if (!$this->isSupplyChainApproved()) {
            return 'Supply Chain Manager';
        }

        return null;
    }

    /**
     * Get approval progress as percentage
     */
    public function getApprovalProgress(): int
    {
        $steps = 3;
        $completed = 0;

        if ($this->isWarehouseApproved()) $completed++;
        if ($this->isAuditApproved()) $completed++;
        if ($this->isSupplyChainApproved()) $completed++;

        return round(($completed / $steps) * 100);
    }

    /**
     * Get approval status for each stage
     */
    public function getApprovalStatus(): array
    {
        return [
            'warehouse_manager' => [
                'approved' => $this->isWarehouseApproved(),
                'name' => $this->warehouse_approver_name,
                'date' => $this->warehouse_approved_at,
            ],
            'auditor' => [
                'approved' => $this->isAuditApproved(),
                'name' => $this->auditor_approver_name,
                'date' => $this->auditor_approved_at,
            ],
            'supply_chain_manager' => [
                'approved' => $this->isSupplyChainApproved(),
                'name' => $this->supply_chain_approver_name,
                'date' => $this->supply_chain_approved_at,
            ],
        ];
    }

    // ===== HELPER METHODS =====

    public function hasRemoverSignature(): bool
    {
        return !empty($this->remover_signature_path) || !empty($this->remover_signature_data);
    }

    public function getRemoverSignatureUrl(): ?string
    {
        if ($this->remover_signature_path) {
            return asset('storage/' . $this->remover_signature_path);
        }
        return null;
    }

    public function getTotalCartons(): int
    {
        return $this->items->sum('no_of_cartons');
    }

    public function getTotalQuantity(): int
    {
        return $this->items->sum('quantity_loaded');
    }

    public function getItemsCount(): int
    {
        return $this->items->count();
    }

    public function getApprovalByStage(string $stage): ?WaybillApproval
    {
        return $this->approvals()->where('stage', $stage)->first();
    }




}
