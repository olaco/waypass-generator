<?php

use App\Livewire\Waybill\AuditorApprovals;
use App\Models\User;
use App\Models\Waybill;
use App\Models\WaybillApproval;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

function auditorTestWaybill(string $status): Waybill
{
    $preparer = User::factory()->create(['role' => 'dist_officer']);

    return Waybill::create([
        'reference_no' => 'EB-AUDIT-' . uniqid(),
        'waybill_no'   => 'WB-AUDIT-' . uniqid(),
        'status'       => $status,
        'prepared_by'  => $preparer->id,
    ]);
}

it('forbids every non-auditor role from loading the page', function (string $role) {
    $user = User::factory()->create(['role' => $role]);

    Livewire::actingAs($user)->test(AuditorApprovals::class)->assertForbidden();
})->with(['dist_officer', 'warehouse_manager', 'supply_chain_manager', 'admin']);

it('forbids actions when the acting user is not an auditor', function () {
    $auditor = User::factory()->create(['role' => 'auditor']);
    $warehouseManager = User::factory()->create(['role' => 'warehouse_manager']);
    $waybill = auditorTestWaybill('pending_auditor');

    $component = Livewire::actingAs($auditor)->test(AuditorApprovals::class)
        ->call('openApprovalModal', $waybill->id);

    $this->actingAs($warehouseManager);

    $component->call('approveAsAuditor')->assertForbidden();

    expect($waybill->fresh()->status)->toBe('pending_auditor');
});

it('lets an auditor approve a pending_auditor waybill', function () {
    Mail::fake();

    $auditor = User::factory()->create(['role' => 'auditor']);
    $waybill = auditorTestWaybill('pending_auditor');

    Livewire::actingAs($auditor)->test(AuditorApprovals::class)
        ->call('openApprovalModal', $waybill->id)
        ->call('approveAsAuditor');

    expect($waybill->fresh()->status)->toBe('pending_supply_chain')
        ->and($waybill->fresh()->auditor_approver_name)->toBe($auditor->name)
        ->and(WaybillApproval::where('waybill_id', $waybill->id)->count())->toBe(1);
});

it('refuses to approve a waybill that moved on after the modal was opened', function () {
    Mail::fake();

    $auditor = User::factory()->create(['role' => 'auditor']);
    $waybill = auditorTestWaybill('pending_auditor');

    $component = Livewire::actingAs($auditor)->test(AuditorApprovals::class)
        ->call('openApprovalModal', $waybill->id);

    Waybill::whereKey($waybill->id)->update(['status' => 'pending_supply_chain']);

    $component->call('approveAsAuditor');

    expect($waybill->fresh()->status)->toBe('pending_supply_chain')
        ->and($waybill->fresh()->auditor_approver_name)->toBeNull()
        ->and(WaybillApproval::where('waybill_id', $waybill->id)->count())->toBe(0);
});

it('forbids a non-auditor from rejecting a waybill', function () {
    $auditor = User::factory()->create(['role' => 'auditor']);
    $warehouseManager = User::factory()->create(['role' => 'warehouse_manager']);
    $waybill = auditorTestWaybill('pending_auditor');

    $component = Livewire::actingAs($auditor)->test(AuditorApprovals::class)
        ->call('openRejectModal', $waybill->id)
        ->set('rejectionReason', 'Invalid details');

    $this->actingAs($warehouseManager);

    $component->call('rejectWaybill')->assertForbidden();

    expect($waybill->fresh()->status)->toBe('pending_auditor')
        ->and(WaybillApproval::where('waybill_id', $waybill->id)->count())->toBe(0);
});

it('rolls back the auditor approval if the audit record cannot be saved', function () {
    Mail::fake();

    $auditor = User::factory()->create(['role' => 'auditor']);
    $preparer = User::factory()->create(['role' => 'dist_officer']);
    $waybill = Waybill::create([
        'reference_no' => 'EB-TEST-'.uniqid(),
        'waybill_no' => 'WB-TEST-'.uniqid(),
        'status' => 'pending_auditor',
        'prepared_by' => $preparer->id,
    ]);

    $component = Livewire::actingAs($auditor)->test(AuditorApprovals::class)
        ->call('openApprovalModal', $waybill->id);

    WaybillApproval::creating(fn () => throw new RuntimeException('audit write failed'));

    expect(fn () => $component->call('approveAsAuditor'))->toThrow(RuntimeException::class);

    expect($waybill->fresh()->status)->toBe('pending_auditor')
        ->and($waybill->fresh()->auditor_approver_name)->toBeNull();
});

it('forbids a non-auditor from opening the reject modal', function () {
    $auditor = User::factory()->create(['role' => 'auditor']);
    $other = User::factory()->create(['role' => 'warehouse_manager']);
    $preparer = User::factory()->create(['role' => 'dist_officer']);
    $waybill = Waybill::create([
        'reference_no' => 'EB-TEST-'.uniqid(),
        'waybill_no' => 'WB-TEST-'.uniqid(),
        'status' => 'pending_auditor',
        'prepared_by' => $preparer->id,
    ]);

    $component = Livewire::actingAs($auditor)->test(AuditorApprovals::class);

    $this->actingAs($other);

    $component->call('openRejectModal', $waybill->id)->assertForbidden();
});
