@component('mail::message')
@php
$stages = [
    'pending_warehouse'    => ['team' => 'Warehouse', 'route' => 'waybills.warehouse'],
    'pending_auditor'      => ['team' => 'Audit', 'route' => 'waybills.auditor'],
    'pending_supply_chain' => ['team' => 'Supply Chain', 'route' => 'waybills.supply-chain'],
];
$stage = $stages[$stageStatus] ?? null;
$pendingUrl = $stage ? route($stage['route']) : route('waybills.show', $waybill);
@endphp
# High-Priority Notification

@if($actionType === 'pending_approval')
Waybill **{{ $waybill->waybill_no }}** is awaiting **{{ $stage ? $stage['team'] . ' review' : 'your review' }}** and requires your prompt attention.

- **Reference No:** {{ $waybill->reference_no ?: 'Not recorded' }}
- **Delivery Destination:** {{ $waybill->delivery_to ?: 'Not recorded' }}

@component('mail::button', ['url' => $pendingUrl])
{{ $stage ? 'Open ' . $stage['team'] . ' Approvals' : 'View Waybill' }}
@endcomponent
@elseif($actionType === 'fully_approved')
Waybill **{{ $waybill->waybill_no }}** has been **FULLY APPROVED** across all departments.

- **Warehouse Approved:** {{ $waybill->warehouse_approver_name ?: 'Not recorded' }}
- **Auditor Approved:** {{ $waybill->auditor_approver_name ?: 'Not recorded' }}
- **Supply Chain Approved:** {{ $waybill->supply_chain_approver_name ?: 'Not recorded' }}

@component('mail::button', ['url' => route('waybills.show', $waybill)])
View Final Document
@endcomponent
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
