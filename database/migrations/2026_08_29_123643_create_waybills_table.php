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
    ];

    protected $casts = [
        'loaded_at' => 'datetime',
    ];

    // ⚠️ THIS IS CRITICAL - ADD THIS METHOD
    public function items(): HasMany
    {
        return $this->hasMany(WaybillItem::class);
    }

    public function preparer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }
}
