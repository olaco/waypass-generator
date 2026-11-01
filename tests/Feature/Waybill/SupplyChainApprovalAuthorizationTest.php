<?php

use App\Livewire\Waybill\SupplyChainApprovals;
use App\Models\User;
use App\Models\Waybill;
use App\Models\WaybillApproval;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

function supplyChainTestWaybill(string $status): Waybill
{
    $preparer = User::factory()->create(['role' => 'dist_officer']);

    return Waybill::create([
        'reference_no' => 'EB-SC-' . uniqid(),
        'waybill_no'   => 'WB-SC-' . uniqid(),
        'status'       => $status,
        'prepared_by'  => $preparer->id,
    ]);
}

it('forbids every non-supply-chain role from loading the page', function (string $role) {
    $user = User::factory()->create(['role' => $role]);

    Livewire::actingAs($user)->test(SupplyChainApprovals::class)->assertForbidden();
})->with(['dist_officer', 'warehouse_manager', 'auditor', 'admin']);

it('forbids actions when the acting user is not a supply chain manager', function () {
    $supplyChainManager = User::factory()->create(['role' => 'supply_chain_manager']);
    $auditor = User::factory()->create(['role' => 'auditor']);
    $waybill = supplyChainTestWaybill('pending_supply_chain');

    $component = Livewire::actingAs($supplyChainManager)->test(SupplyChainApprovals::class)
        ->call('openApprovalModal', $waybill->id);

    $this->actingAs($auditor);

    $component->call('approveAsSupplyChain')->assertForbidden();

    expect($waybill->fresh()->status)->toBe('pending_supply_chain');
});

it('lets a supply chain manager approve a pending_supply_chain waybill', function () {
    Mail::fake();

    $supplyChainManager = User::factory()->create(['role' => 'supply_chain_manager']);
    $waybill = supplyChainTestWaybill('pending_supply_chain');

    Livewire::actingAs($supplyChainManager)->test(SupplyChainApprovals::class)
        ->call('openApprovalModal', $waybill->id)
        ->call('approveAsSupplyChain');

    expect($waybill->fresh()->status)->toBe('approved')
        ->and($waybill->fresh()->supply_chain_approver_name)->toBe($supplyChainManager->name)
        ->and(WaybillApproval::where('waybill_id', $waybill->id)->count())->toBe(1);
});

it('refuses to approve a waybill that moved on after the modal was opened', function () {
    Mail::fake();

    $supplyChainManager = User::factory()->create(['role' => 'supply_chain_manager']);
    $waybill = supplyChainTestWaybill('pending_supply_chain');

    $component = Livewire::actingAs($supplyChainManager)->test(SupplyChainApprovals::class)
        ->call('openApprovalModal', $waybill->id);

    Waybill::whereKey($waybill->id)->update(['status' => 'approved']);

    $component->call('approveAsSupplyChain');

    expect($waybill->fresh()->status)->toBe('approved')
        ->and($waybill->fresh()->supply_chain_approver_name)->toBeNull()
        ->and(WaybillApproval::where('waybill_id', $waybill->id)->count())->toBe(0);
});

it('forbids a non-supply-chain manager from rejecting a waybill', function () {
    $supplyChainManager = User::factory()->create(['role' => 'supply_chain_manager']);
    $auditor = User::factory()->create(['role' => 'auditor']);
    $waybill = supplyChainTestWaybill('pending_supply_chain');

    $component = Livewire::actingAs($supplyChainManager)->test(SupplyChainApprovals::class)
        ->call('openRejectModal', $waybill->id)
        ->set('rejectionReason', 'Invalid details');

    $this->actingAs($auditor);

    $component->call('rejectWaybill')->assertForbidden();

    expect($waybill->fresh()->status)->toBe('pending_supply_chain')
        ->and(WaybillApproval::where('waybill_id', $waybill->id)->count())->toBe(0);
});

it('refuses to reject a waybill that moved on after the rejection modal was opened', function () {
    $supplyChainManager = User::factory()->create(['role' => 'supply_chain_manager']);
    $waybill = supplyChainTestWaybill('pending_supply_chain');

    $component = Livewire::actingAs($supplyChainManager)->test(SupplyChainApprovals::class)
        ->call('openRejectModal', $waybill->id)
        ->set('rejectionReason', 'Invalid details');

    Waybill::whereKey($waybill->id)->update(['status' => 'approved']);

    $component->call('rejectWaybill');

    expect($waybill->fresh()->status)->toBe('approved')
        ->and(WaybillApproval::where('waybill_id', $waybill->id)->count())->toBe(0);
});

it('rolls back the supply chain approval if the audit record cannot be saved', function () {
    Mail::fake();

    $manager = User::factory()->create(['role' => 'supply_chain_manager']);
    $preparer = User::factory()->create(['role' => 'dist_officer']);
    $waybill = Waybill::create([
        'reference_no' => 'EB-TEST-'.uniqid(),
        'waybill_no' => 'WB-TEST-'.uniqid(),
        'status' => 'pending_supply_chain',
        'prepared_by' => $preparer->id,
    ]);

    $component = Livewire::actingAs($manager)->test(SupplyChainApprovals::class)
        ->call('openApprovalModal', $waybill->id);

    WaybillApproval::creating(fn () => throw new RuntimeException('audit write failed'));

    expect(fn () => $component->call('approveAsSupplyChain'))->toThrow(RuntimeException::class);

    expect($waybill->fresh()->status)->toBe('pending_supply_chain')
        ->and($waybill->fresh()->gatepass_no)->toBeNull();
});
