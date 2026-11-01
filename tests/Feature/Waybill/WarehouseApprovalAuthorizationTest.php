<?php

use App\Livewire\Waybill\WarehouseApprovals;
use App\Models\User;
use App\Models\Waybill;
use App\Models\WaybillApproval;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

function approvalTestWaybill(string $status): Waybill
{
    $preparer = User::factory()->create(['role' => 'dist_officer']);

    return Waybill::create([
        'reference_no' => 'EB-TEST-' . uniqid(),
        'waybill_no'   => 'WB-TEST-' . uniqid(),
        'status'       => $status,
        'prepared_by'  => $preparer->id,
    ]);
}

it('forbids every non-warehouse role from loading the page', function (string $role) {
    $user = User::factory()->create(['role' => $role]);

    Livewire::actingAs($user)->test(WarehouseApprovals::class)->assertForbidden();
})->with(['dist_officer', 'auditor', 'supply_chain_manager', 'admin']);

it('forbids actions when the acting user is not a warehouse manager', function () {
    $manager = User::factory()->create(['role' => 'warehouse_manager']);
    $auditor = User::factory()->create(['role' => 'auditor']);
    $waybill = approvalTestWaybill('pending_warehouse');

    $component = Livewire::actingAs($manager)->test(WarehouseApprovals::class)
        ->call('openApprovalModal', $waybill->id);

    $this->actingAs($auditor);

    $component->call('approveAsWarehouse')->assertForbidden();

    expect($waybill->fresh()->status)->toBe('pending_warehouse');
});

it('lets a warehouse manager approve a pending_warehouse waybill', function () {
    Mail::fake();

    $manager = User::factory()->create(['role' => 'warehouse_manager']);
    $waybill = approvalTestWaybill('pending_warehouse');

    Livewire::actingAs($manager)->test(WarehouseApprovals::class)
        ->call('openApprovalModal', $waybill->id)
        ->call('approveAsWarehouse');

    expect($waybill->fresh()->status)->toBe('pending_auditor')
        ->and($waybill->fresh()->warehouse_approver_name)->toBe($manager->name)
        ->and(WaybillApproval::where('waybill_id', $waybill->id)->count())->toBe(1);
});

it('refuses to approve a waybill that moved on after the modal was opened', function () {
    Mail::fake();

    $manager = User::factory()->create(['role' => 'warehouse_manager']);
    $waybill = approvalTestWaybill('pending_warehouse');

    $component = Livewire::actingAs($manager)->test(WarehouseApprovals::class)
        ->call('openApprovalModal', $waybill->id);

    Waybill::whereKey($waybill->id)->update(['status' => 'pending_auditor']);

    $component->call('approveAsWarehouse');

    expect($waybill->fresh()->status)->toBe('pending_auditor')
        ->and($waybill->fresh()->warehouse_approver_name)->toBeNull()
        ->and(WaybillApproval::where('waybill_id', $waybill->id)->count())->toBe(0);
});

it('forbids a non-warehouse manager from rejecting a waybill', function () {
    $manager = User::factory()->create(['role' => 'warehouse_manager']);
    $auditor = User::factory()->create(['role' => 'auditor']);
    $waybill = approvalTestWaybill('pending_warehouse');

    $component = Livewire::actingAs($manager)->test(WarehouseApprovals::class)
        ->call('openRejectModal', $waybill->id)
        ->set('rejectionReason', 'Invalid details');

    $this->actingAs($auditor);

    $component->call('rejectWaybill')->assertForbidden();

    expect($waybill->fresh()->status)->toBe('pending_warehouse')
        ->and(WaybillApproval::where('waybill_id', $waybill->id)->count())->toBe(0);
});


it('refuses to reject a waybill that moved on after the rejection modal was opened', function () {
    $manager = User::factory()->create(['role' => 'warehouse_manager']);
    $waybill = approvalTestWaybill('pending_warehouse');

    $component = Livewire::actingAs($manager)->test(WarehouseApprovals::class)
        ->call('openRejectModal', $waybill->id)
        ->set('rejectionReason', 'Invalid details');

    Waybill::whereKey($waybill->id)->update(['status' => 'pending_auditor']);

    $component->call('rejectWaybill');

    expect($waybill->fresh()->status)->toBe('pending_auditor')
        ->and(WaybillApproval::where('waybill_id', $waybill->id)->count())->toBe(0);
});
