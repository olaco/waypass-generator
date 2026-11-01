<?php

use App\Mail\WaybillStageMail;
use App\Models\User;
use App\Models\Waybill;
use Illuminate\Support\Facades\Mail;



beforeEach(function () {
    Mail::fake();
});

it('queues a notification for warehouse managers when a waybill enters pending warehouse', function () {
    $preparer = User::factory()->create([
        'role' => 'dist_officer',
    ]);

    $warehouseManager = User::factory()->create([
        'role' => 'warehouse_manager',
    ]);

    $waybill = Waybill::create([
        'reference_no' => 'REF-TEST-001',
        'waybill_no' => 'WB-TEST-001',
        'prepared_by' => $preparer->id,
        'status' => 'draft',
    ]);

    $waybill->update([
        'status' => 'pending_warehouse',
    ]);

    Mail::assertQueued(
    WaybillStageMail::class,
    function (WaybillStageMail $mail) use ($warehouseManager) {
        return $mail->hasTo($warehouseManager->email)
            && $mail->actionType === 'pending_approval'
            && $mail->stageStatus === 'pending_warehouse';
    }
);

    Mail::assertQueued(WaybillStageMail::class, 1);
    });

it('does not queue a notification when a waybill field changes without a status change', function () {
    $preparer = User::factory()->create([
        'role' => 'dist_officer',
    ]);

    User::factory()->create([
        'role' => 'warehouse_manager',
    ]);

    $waybill = Waybill::create([
        'reference_no' => 'REF-TEST-002',
        'waybill_no' => 'WB-TEST-002',
        'prepared_by' => $preparer->id,
        'status' => 'pending_warehouse',
    ]);

    $waybill->update([
        'remarks' => 'Updated test remarks',
    ]);

    Mail::assertNothingQueued();
});


it('notifies the correct role at the auditor and supply chain stages', function () {
    $preparer = User::factory()->create([
        'role' => 'dist_officer',
    ]);

    $auditor = User::factory()->create([
        'role' => 'auditor',
    ]);

    $supplyChainManager = User::factory()->create([
        'role' => 'supply_chain_manager',
    ]);

    $waybill = Waybill::create([
        'reference_no' => 'REF-TEST-003',
        'waybill_no' => 'WB-TEST-003',
        'prepared_by' => $preparer->id,
        'status' => 'pending_warehouse',
    ]);

    $waybill->update([
        'status' => 'pending_auditor',
    ]);

    $waybill->update([
        'status' => 'pending_supply_chain',
    ]);

    Mail::assertQueued(
        WaybillStageMail::class,
        fn (WaybillStageMail $mail) =>
            $mail->hasTo($auditor->email)
            && $mail->stageStatus === 'pending_auditor'
    );

    Mail::assertQueued(
        WaybillStageMail::class,
        fn (WaybillStageMail $mail) =>
            $mail->hasTo($supplyChainManager->email)
            && $mail->stageStatus === 'pending_supply_chain'
    );

    Mail::assertQueued(WaybillStageMail::class, 2);
});


it('notifies the preparer and distribution officers when a waybill is fully approved', function () {
    $preparer = User::factory()->create([
        'role' => 'dist_officer',
    ]);

    $anotherDistributionOfficer = User::factory()->create([
        'role' => 'dist_officer',
    ]);

    $warehouseManager = User::factory()->create([
        'role' => 'warehouse_manager',
    ]);

    $waybill = Waybill::create([
        'reference_no' => 'REF-TEST-004',
        'waybill_no' => 'WB-TEST-004',
        'prepared_by' => $preparer->id,
        'status' => 'pending_supply_chain',
    ]);

    $waybill->update([
        'status' => 'approved',
    ]);

    Mail::assertQueued(
        WaybillStageMail::class,
        fn (WaybillStageMail $mail) =>
            $mail->hasTo($preparer->email)
            && $mail->actionType === 'fully_approved'
            && $mail->stageStatus === 'approved'
    );

    Mail::assertQueued(
        WaybillStageMail::class,
        fn (WaybillStageMail $mail) =>
            $mail->hasTo($anotherDistributionOfficer->email)
            && $mail->actionType === 'fully_approved'
    );

    Mail::assertNotQueued(
        WaybillStageMail::class,
        fn (WaybillStageMail $mail) =>
            $mail->hasTo($warehouseManager->email)
    );

    Mail::assertQueued(WaybillStageMail::class, 2);
});


it('skips recipients with invalid email addresses', function () {
    $preparer = User::factory()->create([
        'role' => 'dist_officer',
    ]);

    User::factory()->create([
        'role' => 'warehouse_manager',
        'email' => 'not-a-valid-email',
    ]);

    $waybill = Waybill::create([
        'reference_no' => 'REF-TEST-005',
        'waybill_no' => 'WB-TEST-005',
        'prepared_by' => $preparer->id,
        'status' => 'draft',
    ]);

    $waybill->update([
        'status' => 'pending_warehouse',
    ]);

    Mail::assertNothingQueued();
});


it('does not queue notifications for unsupported waybill statuses', function () {
    $preparer = User::factory()->create([
        'role' => 'dist_officer',
    ]);

    User::factory()->create([
        'role' => 'warehouse_manager',
    ]);

    $waybill = Waybill::create([
        'reference_no' => 'REF-TEST-006',
        'waybill_no' => 'WB-TEST-006',
        'prepared_by' => $preparer->id,
        'status' => 'draft',
    ]);

    $waybill->update([
        'status' => 'unexpected_status',
    ]);

    Mail::assertNothingQueued();
});

