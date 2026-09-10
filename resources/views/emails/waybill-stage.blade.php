@component('mail::message')
# High-Priority Notification

@if($actionType === 'pending_approval')
Waybill **{{ $waybill->waybill_no }}** requires your immediate review and approval.

- **Reference No:** {{ $waybill->reference_no }}
- **Delivery Destination:** {{ $waybill->delivery_to }}

@component('mail::button', ['url' => route('waybills.show', $waybill)])
Review & Approve
@endcomponent

@else
Waybill **{{ $waybill->waybill_no }}** has been **FULLY APPROVED** across all departments.

- **Warehouse Approved:** {{ $waybill->warehouse_approver_name }}
- **Auditor Approved:** {{ $waybill->auditor_approver_name }}
- **Supply Chain Approved:** {{ $waybill->supply_chain_approver_name }}

@component('mail::button', ['url' => route('waybills.show', $waybill)])
View Final Document
@endcomponent
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
