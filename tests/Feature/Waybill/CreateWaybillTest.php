<?php

use App\Livewire\Waybill\CreateWaybill;
use App\Models\User;
use App\Models\Waybill;
use Livewire\Livewire;

function createWaybillItem(): array
{
    return [
        'product_description' => 'Plexitone syrup',
        'batch_no' => 'B-001',
        'no_of_cartons' => 0,
        'quantity_loaded' => 5,
    ];
}

it('creates a draft waybill with its items and allows zero cartons', function () {
    $officer = User::factory()->create(['role' => 'dist_officer']);

    Livewire::actingAs($officer)->test(CreateWaybill::class)
        ->set('reference_no', 'EB-TEST-000001')
        ->set('waybill_no', 'WB-TEST-000001')
        ->set('items', [createWaybillItem()])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('waybills.index'));

    $waybill = Waybill::where('reference_no', 'EB-TEST-000001')->firstOrFail();

    expect($waybill->status)->toBe('draft')
        ->and($waybill->prepared_by)->toEqual($officer->id)
        ->and($waybill->items()->count())->toBe(1);
});

it('still creates a waybill when no items have been added', function () {
    $officer = User::factory()->create(['role' => 'dist_officer']);

    Livewire::actingAs($officer)->test(CreateWaybill::class)
        ->set('reference_no', 'EB-TEST-000002')
        ->set('waybill_no', 'WB-TEST-000002')
        ->set('items', [])
        ->call('save')
        ->assertHasNoErrors();

    expect(Waybill::where('reference_no', 'EB-TEST-000002')->exists())->toBeTrue();
});

it('saves nothing if an item cannot be created', function () {
    $officer = User::factory()->create(['role' => 'dist_officer']);

    $itemModel = (new Waybill)->items()->getRelated();
    $itemModel::creating(fn () => throw new RuntimeException('item write failed'));

    $component = Livewire::actingAs($officer)->test(CreateWaybill::class)
        ->set('reference_no', 'EB-TEST-000003')
        ->set('waybill_no', 'WB-TEST-000003')
        ->set('items', [createWaybillItem()]);

    expect(fn () => $component->call('save'))->toThrow(RuntimeException::class);

    expect(Waybill::where('reference_no', 'EB-TEST-000003')->exists())->toBeFalse();
});
