<?php

namespace App\Observers;

use App\Actions\SendWaybillStageNotification;
use App\Models\Waybill;

class WaybillObserver
{
    public function created(Waybill $waybill): void
    {
        // Triggered automatically when Distribution Officer creates a new waybill
        app(SendWaybillStageNotification::class)->execute($waybill, 'pending_approval');
    }

    public function updated(Waybill $waybill): void
    {
        // Triggered when status changes across approval stages
        if ($waybill->wasChanged('status')) {
            $actionType = ($waybill->status === 'approved') ? 'fully_approved' : 'pending_approval';
            app(SendWaybillStageNotification::class)->execute($waybill, $actionType);
        }
    }
}
