<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaybillApproval extends Model
{
    protected $fillable = [
        'waybill_id',
        'approver_id',
        'stage',
        'status',
        'signature_path',
        'signature_data',
        'remarks',
        'ip_address',
        'user_agent',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function waybill(): BelongsTo
    {
        return $this->belongsTo(Waybill::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function getStageLabel(): string
    {
        return match($this->stage) {
            'warehouse_manager' => 'Warehouse Manager',
            'auditor' => 'Auditor',
            'supply_chain_manager' => 'Supply Chain Manager',
            default => ucfirst(str_replace('_', ' ', $this->stage)),
        };
    }

    public function getStatusBadge(): string
    {
        return match($this->status) {
            'approved' => '✅ Approved',
            'rejected' => '❌ Rejected',
            'pending' => '⏳ Pending',
            default => '📋 ' . ucfirst($this->status),
        };
    }
}
