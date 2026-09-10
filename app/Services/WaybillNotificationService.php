<?php

namespace App\Services;

use App\Models\User;
use App\Models\Waybill;
use App\Mail\WaybillStageMail;
use Illuminate\Support\Facades\Mail;

class WaybillNotificationService
{
    /**
     * Notify exclusively the single approver next in line
     */
    public function dispatchNextApprover(Waybill $waybill): void
    {
        $nextRole = $waybill->next_approver_role; // From model accessor

        if (!$nextRole) return;

        $approverEmail = User::where('role', $nextRole)->value('email');

        if ($approverEmail) {
            Mail::to($approverEmail)->queue(new WaybillStageMail($waybill, 'pending_approval'));
        }
    }

    /**
     * Notify all parties simultaneously upon final approval
     */
    public function dispatchFinalApprovalToAll(Waybill $waybill): void
    {
        $stakeholders = ['Distribution Officer', 'Warehouse Manager', 'Auditor', 'Supply Chain Manager'];

        $emails = User::whereIn('role', $stakeholders)->pluck('email')->toArray();

        if ($waybill->preparer?->email) {
            $emails[] = $waybill->preparer->email;
        }

        $recipients = array_unique(array_filter($emails));

        if (!empty($recipients)) {
            Mail::to($recipients)->queue(new WaybillStageMail($waybill, 'fully_approved'));
        }
    }
}
