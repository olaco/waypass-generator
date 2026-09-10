<?php

namespace App\Actions;

use App\Mail\WaybillStageMail;
use App\Models\User;
use App\Models\Waybill;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWaybillStageNotification
{
    /**
     * Dispatch stage notification emails based on current waybill status.
     */
    public function execute(Waybill $waybill, string $actionType = 'pending_approval'): void
    {
        $recipients = collect();

        if ($actionType === 'pending_approval') {
            // Map approval stages to exact user roles in your DB
            $roleMap = [
                'pending_warehouse'    => 'warehouse_manager',
                'pending'              => 'warehouse_manager',
                'submitted'            => 'warehouse_manager',
                'pending_auditor'      => 'auditor',
                'pending_audit'        => 'auditor',
                'pending_supply_chain' => 'supply_chain_manager',
            ];

            $targetRole = $roleMap[$waybill->status] ?? 'warehouse_manager';
            $recipients = User::where('role', $targetRole)->get();

        } elseif ($actionType === 'fully_approved') {
            // Updated 'distribution_officer' to 'dist_officer' to match DB schema
            $recipients = User::where('id', $waybill->prepared_by)
                ->orWhere('role', 'dist_officer')
                ->get();
        }

        if ($recipients->isEmpty()) {
            Log::warning("WayPass Mailer Warning: No recipient found for role matching status '{$waybill->status}' on Waybill #{$waybill->waybill_no}");
            return;
        }

        foreach ($recipients as $recipient) {
            Mail::to($recipient->email)->send(new WaybillStageMail($waybill, $actionType));
        }
    }
}
