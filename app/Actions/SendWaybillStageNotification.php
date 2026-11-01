<?php

namespace App\Actions;

use App\Mail\WaybillStageMail;
use App\Models\User;
use App\Models\Waybill;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendWaybillStageNotification
{
    /**
     * Dispatch stage notification emails based on current waybill status.
     */
    public function execute(Waybill $waybill, string $actionType = 'pending_approval'): void
    {
        $recipients = collect();

        if ($actionType === 'pending_approval') {
            $roleMap = [
                'pending_warehouse' => 'warehouse_manager',
                'pending_auditor' => 'auditor',
                'pending_supply_chain' => 'supply_chain_manager',
            ];

            if (! isset($roleMap[$waybill->status])) {
                Log::warning(
                    "WayPass: Unknown approval status '{$waybill->status}' for Waybill #{$waybill->waybill_no}. No notification sent."
                );

                return;
            }

            $targetRole = $roleMap[$waybill->status];

            $recipients = User::where('role', $targetRole)->get();

        } elseif ($actionType === 'fully_approved') {
            $recipients = User::where('id', $waybill->prepared_by)
                ->orWhere('role', 'dist_officer')
                ->get();

        } else {
            Log::warning(
                "WayPass: Unknown notification action '{$actionType}' for Waybill #{$waybill->waybill_no}."
            );

            return;
        }

        if ($recipients->isEmpty()) {
            Log::warning(
                "WayPass: No notification recipient found for status '{$waybill->status}' on Waybill #{$waybill->waybill_no}."
            );

            return;
        }

        foreach ($recipients as $recipient) {
            if (
                empty($recipient->email) ||
                ! filter_var($recipient->email, FILTER_VALIDATE_EMAIL)
            ) {
                Log::warning(
                    "WayPass: Skipping user ID {$recipient->id} because the email address is missing or invalid."
                );

                continue;
            }

            try {
                Mail::to($recipient->email)
                    ->send(new WaybillStageMail($waybill, $actionType));

                Log::info(
                    "WayPass: Notification dispatched to the mail queue for Waybill #{$waybill->waybill_no}, user ID {$recipient->id}."
                );

            } catch (Throwable $exception) {
                Log::error(
                    "WayPass: Notification email could not be dispatched for Waybill #{$waybill->waybill_no}, user ID {$recipient->id}.",
                    [
                        'recipient_email' => $recipient->email,
                        'action_type' => $actionType,
                        'exception' => $exception::class,
                        'error' => $exception->getMessage(),
                    ]
                );

                // Continue attempting delivery to the remaining recipients.
                continue;
            }
        }
    }
}
