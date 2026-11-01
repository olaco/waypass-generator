<?php

namespace App\Observers;

use App\Actions\SendWaybillStageNotification;
use App\Models\Waybill;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class WaybillObserver implements ShouldHandleEventsAfterCommit
{
    public function updated(Waybill $waybill): void
    {
        if (! $waybill->wasChanged('status')) {
            return;
        }

        // Final approval: notify the creator and Distribution Officers.
        if ($waybill->status === 'approved') {
            app(SendWaybillStageNotification::class)
                ->execute($waybill, 'fully_approved');

            return;
        }

        // Notify the team responsible for the next approval stage.
        $approvalStatuses = [
            'pending_warehouse',
            'pending_auditor',
            'pending_supply_chain',
        ];

        if (in_array($waybill->status, $approvalStatuses, true)) {
            app(SendWaybillStageNotification::class)
                ->execute($waybill, 'pending_approval');
        }
    }
}
