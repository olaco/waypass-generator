<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gatepass - {{ $waybill->waybill_no }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .signatures { margin-top: 40px; width: 100%; }
        .sig-box { width: 30%; float: left; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>OFFICIAL WAYPASS / GATEPASS</h2>
        <p><strong>Gatepass No:</strong> {{ $waybill->waybill_no }}</p>
        <p><strong>Date:</strong> {{ $waybill->updated_at->format('Y-m-d H:i') }}</p>
    </div>

    <table class="table">
        <tr>
        <th>Delivery Destination</th>
        <td>{{ $waybill->delivery_to }}</td>
    </tr>
    <tr>
        <th>Status</th>
        <td>{{ strtoupper($waybill->status) }}</td>
    </tr>
    <!-- Added Checked By and Date Row -->
    <tr>
        <th>Checked By</th>
        <td>
            {{ $waybill->remover_name ?? $waybill->auditor_approver_name ?? 'N/A' }}
            &nbsp;|&nbsp;
            <strong>Date:</strong> {{ $waybill->remover_signed_at ? \Carbon\Carbon::parse($waybill->remover_signed_at)->format('Y-m-d H:i') : now()->format('Y-m-d H:i') }}
        </td>
    </tr>
    </table>

    <div class="signatures">
        <div class="sig-box">
            <p>____________________</p>
            <p><strong>Warehouse Manager</strong><br>{{ $waybill->warehouse_approver_name }}</p>
        </div>
        <div class="sig-box">
            <p>____________________</p>
            <p><strong>Auditor</strong><br>{{ $waybill->auditor_approver_name }}</p>
        </div>
        <div class="sig-box">
            <p>____________________</p>
            <p><strong>Supply Chain Head</strong><br>{{ $waybill->supply_chain_approver_name }}</p>
        </div>
    </div>
</body>
</html>

