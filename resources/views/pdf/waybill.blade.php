<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Waybill - {{ $waybill->reference_no }}</title>

    @php
        // Status color palette
        $statusKey = strtolower(str_replace(' ', '_', $waybill->status ?? 'pending'));
        $statusPalette = [
            'draft'             => ['bg' => '#f2f2f2', 'text' => '#555555', 'border' => '#8c8c8c'],
            'pending'           => ['bg' => '#fff6e0', 'text' => '#8a5b00', 'border' => '#d99a00'],
            'pending_warehouse' => ['bg' => '#fff6e0', 'text' => '#8a5b00', 'border' => '#d99a00'],
            'pending_auditor'   => ['bg' => '#fff6e0', 'text' => '#8a5b00', 'border' => '#d99a00'],
            'pending_supply_chain' => ['bg' => '#fff6e0', 'text' => '#8a5b00', 'border' => '#d99a00'],
            'pending_final'     => ['bg' => '#fff6e0', 'text' => '#8a5b00', 'border' => '#d99a00'],
            'approved'          => ['bg' => '#e8f6ec', 'text' => '#1e7a34', 'border' => '#2fa84f'],
            'rejected'          => ['bg' => '#fbeae8', 'text' => '#a3271b', 'border' => '#c0392b'],
            'returned'          => ['bg' => '#fbeae8', 'text' => '#a3271b', 'border' => '#c0392b'],
        ];
        $palette = $statusPalette[$statusKey] ?? $statusPalette['pending'];

        // Watermark text
        $watermarkText = strtoupper(str_replace('_', ' ', $waybill->status ?? 'PENDING'));
    @endphp

    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 10mm 8mm 10mm;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            background: #fff;
            color: #1a1a1a;
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 8px;
            line-height: 1.3;
            position: relative;
        }

        /* =========================================================
           WATERMARK - Background Status
           ========================================================= */
        #watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 72pt;
            font-weight: 700;
            color: rgba(0, 0, 0, 0.04);
            text-transform: uppercase;
            letter-spacing: 8px;
            pointer-events: none;
            z-index: -1000;
            font-family: 'Helvetica', 'Arial', sans-serif;
            white-space: nowrap;
        }

        #brand-strip {
            height: 3px;
            width: 100%;
            background: #1a2b4c;
            margin-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: -1px;
        }

        th, td {
            border: 1px solid #2b2b2b;
            padding: 3px 5px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .no-border { border: none !important; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .lbl {
            display: block;
            font-size: 6px;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 1px;
        }

        .val {
            display: block;
            font-size: 8px;
            font-weight: bold;
            color: #000;
        }

        .header-title {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #1a2b4c;
        }

        .doc-type {
            font-size: 11px;
            font-weight: 700;
            text-align: center;
            background: #f0f0f0;
            padding: 4px 8px;
            letter-spacing: 1px;
            border-bottom: 2px solid {{ $palette['border'] }};
        }

        .status-badge {
            display: inline-block;
            font-size: 7.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 2px 6px;
            border-radius: 3px;
            background: {{ $palette['bg'] }};
            color: {{ $palette['text'] }};
            border: 1px solid {{ $palette['border'] }};
        }

        .th-bg {
            background: #edf0f4;
            font-size: 7px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 3px 5px;
            letter-spacing: 0.5px;
        }

        .items-table td { padding: 2.5px 4px; }

        .signatory-table {
            page-break-inside: avoid;
            margin-top: -1px;
        }

        .signatory-cell {
            padding: 5px 6px;
            vertical-align: top;
        }

        .designation-header {
            border-bottom: 1px dashed #aaa;
            padding-bottom: 3px;
            margin-bottom: 4px;
            font-size: 7px;
            line-height: 1.4;
        }

        .signature-bottom {
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 3px;
            font-size: 7px;
        }

        .timestamp-text {
            font-size: 6px;
            color: #666;
            margin-top: 2px;
        }

        .footer-note {
            font-size: 6px;
            color: #666;
            text-align: center;
            margin-top: 6px;
            padding-top: 4px;
            border-top: 1px solid #ddd;
            page-break-inside: avoid;
        }

        .approval-status {
            display: inline-block;
            font-size: 6px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 0px 6px;
            border-radius: 2px;
        }
        .approval-status.approved {
            background: #e8f6ec;
            color: #1e7a34;
        }
        .approval-status.pending {
            background: #fff6e0;
            color: #8a5b00;
        }
        .approval-status.rejected {
            background: #fbeae8;
            color: #a3271b;
        }

        .auth-chain-table {
            margin-top: -1px;
            page-break-inside: avoid;
        }
        .auth-chain-cell {
            width: 33.33%;
            padding: 6px;
            vertical-align: top;
        }
        .auth-chain-cell + .auth-chain-cell {
            border-left: 1px solid #2b2b2b;
        }

        @media print {
            body { margin: 0; padding: 0; }
            #watermark {
                color: rgba(0, 0, 0, 0.04);
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    {{-- =========================================================
    WATERMARK - Background Status Text
    ========================================================= --}}
    <div id="watermark">{{ $watermarkText }}</div>

    <div id="brand-strip"></div>

    {{-- HEADER BLOCK --}}
    <table style="margin-top: 0;">
        <tr>
            <td style="width: 50%; padding: 4px 6px;">
                <div class="header-title uppercase">EVANS BAROQUE LTD</div>
                <div style="font-size: 7px; color: #333; margin-top: 2px;">
                    Agbara Industrial Estate, Ogun State, Nigeria<br>
                    Tel: +234-800-EVANS-BAROQUE | Email: info@evansbaroque.com
                </div>
            </td>

            <td style="width: 50%; padding: 0;">
                <div class="doc-type">WAYBILL / DISPATCH NOTE</div>
                <table style="border: none;">
                    <tr>
                        <td class="no-border" style="width: 50%; border-right: 1px solid #2b2b2b !important; border-bottom: 1px solid #2b2b2b !important;">
                            <span class="lbl">Waybill No.</span>
                            <span class="val">{{ $waybill->waybill_no }}</span>
                        </td>
                        <td class="no-border" style="border-bottom: 1px solid #2b2b2b !important;">
                            <span class="lbl">Date</span>
                            <span class="val">{{ now()->format('d-M-Y') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="no-border" style="border-right: 1px solid #2b2b2b !important;">
                            <span class="lbl">Reference No.</span>
                            <span class="val">{{ $waybill->reference_no }}</span>
                        </td>
                        <td class="no-border">
                            <span class="lbl">Status</span>
                            <span class="status-badge">{{ str_replace('_', ' ', $waybill->status) }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- DISPATCH & TRANSPORT DETAILS --}}
        <tr>
            <td style="width: 50%; padding: 4px 6px;">
                <span class="lbl">Consignee (Ship / Deliver To)</span>
                <div class="val">{{ $waybill->delivery_to ?? 'Internal Transfer / Customer Destination' }}</div>
                <div style="font-size: 7px; color: #333; margin-top: 1px;">
                    Destination Address: Ogun State Plant / Depot Location
                </div>
            </td>
            <td style="width: 50%; padding: 0;">
                <table style="border: none;">
                    <tr>
                        <td class="no-border" style="width: 50%; border-right: 1px solid #2b2b2b !important; border-bottom: 1px solid #2b2b2b !important;">
                            <span class="lbl">Vehicle No.</span>
                            <span class="val">{{ $waybill->vehicle_no ?? '—' }}</span>
                        </td>
                        <td class="no-border" style="border-bottom: 1px solid #2b2b2b !important;">
                            <span class="lbl">Gate Pass No.</span>
                            <span class="val">{{ $waybill->gatepass_no ?? '—' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="no-border" style="border-right: 1px solid #2b2b2b !important;">
                            <span class="lbl">Vehicle Owner / Transport</span>
                            <span class="val">{{ $waybill->vehicle_owner ?? 'Road Transport' }}</span>
                        </td>
                        <td class="no-border">
                            <span class="lbl">Loaded At / Time</span>
                            <span class="val">{{ $waybill->loaded_at ? $waybill->loaded_at->format('d-M-Y H:i') : '—' }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- PRODUCT / MATERIAL DETAILS TABLE --}}
    <table class="items-table">
        <thead>
            <tr class="th-bg">
                <th style="width: 5%;" class="text-center">S/N</th>
                <th style="width: 45%;" class="text-left">Description of Goods</th>
                <th style="width: 20%;" class="text-center">Batch Number</th>
                <th style="width: 15%;" class="text-right">Cartons</th>
                <th style="width: 15%;" class="text-right">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @forelse($waybill->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $item->product_description }}</strong></td>
                    <td class="text-center">{{ $item->batch_no }}</td>
                    <td class="text-right">{{ number_format($item->no_of_cartons) }}</td>
                    <td class="text-right font-bold">{{ number_format($item->quantity_loaded) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 10px; color: #777;">
                        No items loaded on this waybill document.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="th-bg">
                <td colspan="3" class="text-right font-bold">TOTAL DISPATCH:</td>
                <td class="text-right font-bold">{{ number_format($waybill->items->sum('no_of_cartons')) }}</td>
                <td class="text-right font-bold">{{ number_format($waybill->items->sum('quantity_loaded')) }}</td>
            </tr>
        </tfoot>
    </table>

    @if($waybill->remarks)
    <table>
        <tr>
            <td style="padding: 3px 5px; background: #fafafa; border-left: 3px solid #1a2b4c;">
                <span class="lbl">Remarks / Special Instructions</span>
                <div style="font-size: 7.5px;">{{ $waybill->remarks }}</div>
            </td>
        </tr>
    </table>
    @endif

    {{-- TOP ROW: DRIVER / CARRIER, CONSIGNEE / RECEIVER, PREPARED BY --}}
    <table class="signatory-table">
        <thead>
            <tr class="th-bg">
                <th style="width: 33%;" class="text-center">DRIVER / CARRIER</th>
                <th style="width: 33%;" class="text-center">CONSIGNEE / RECEIVER</th>
                <th style="width: 34%;" class="text-center">PREPARED BY</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="signatory-cell">
                    <div class="designation-header">
                        <strong>CARRIER DETAILS</strong><br>
                        DRIVER: {{ $waybill->driver_name ?? '____________________' }}
                    </div>
                    <div class="signature-bottom">
                        SIGN & DATE: ________________________
                    </div>
                </td>

                <td class="signatory-cell">
                    <div class="designation-header">
                        <strong>RECEIVING ENTITY</strong><br>
                        NAME: ______________________________<br>
                        COMPANY: ___________________________
                    </div>
                    <div class="signature-bottom">
                        SIGN & DATE: ________________________
                    </div>
                </td>

                <td class="signatory-cell">
                    <div class="designation-header">
                        <strong>PREPARED BY</strong><br>
                        NAME: {{ $waybill->preparer->name ?? 'SYSTEM ADMIN' }}
                    </div>
                    <div class="signature-bottom">
                        SIGNATURE: __________________________
                        <div class="timestamp-text">
                            GEN TIMESTAMP: {{ now()->format('d-M-Y H:i:s') }}
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- =========================================================
    AUTHORIZATION CHAIN
    ========================================================= --}}
    @php
        // Get approval statuses directly from the waybill model
        $warehouseApproved = $waybill->isWarehouseApproved();
        $auditApproved = $waybill->isAuditApproved();
        $supplyChainApproved = $waybill->isSupplyChainApproved();

        $warehouseName = $waybill->warehouse_approver_name ?? 'PENDING';
        $auditName = $waybill->auditor_approver_name ?? 'PENDING';
        $supplyChainName = $waybill->supply_chain_approver_name ?? 'PENDING';

        $warehouseDate = $waybill->warehouse_approved_at ? $waybill->warehouse_approved_at->format('d-M-Y H:i') : '—';
        $auditDate = $waybill->auditor_approved_at ? $waybill->auditor_approved_at->format('d-M-Y H:i') : '—';
        $supplyChainDate = $waybill->supply_chain_approved_at ? $waybill->supply_chain_approved_at->format('d-M-Y H:i') : '—';

        $warehouseStatus = $warehouseApproved ? 'APPROVED' : 'PENDING';
        $auditStatus = $auditApproved ? 'APPROVED' : 'PENDING';
        $supplyChainStatus = $supplyChainApproved ? 'APPROVED' : 'PENDING';
    @endphp

    <table class="signatory-table auth-chain-table">
        <thead>
            <tr class="th-bg">
                <th colspan="3" class="text-center">AUTHORIZATION CHAIN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="auth-chain-cell">
                    <span class="lbl">Warehouse Manager</span>
                    <span class="val">{{ $warehouseName }}</span>
                    <div class="timestamp-text">
                        STATUS: <span class="approval-status {{ $warehouseApproved ? 'approved' : 'pending' }}">
                            {{ $warehouseStatus }}
                        </span>
                    </div>
                    <div class="timestamp-text">
                        TIME: {{ $warehouseDate }}
                    </div>
                    <div class="signature-bottom" style="margin-top: 8px;">
                        SIGNATURE: ______________________
                    </div>
                </td>
                <td class="auth-chain-cell">
                    <span class="lbl">Audit</span>
                    <span class="val">{{ $auditName }}</span>
                    <div class="timestamp-text">
                        STATUS: <span class="approval-status {{ $auditApproved ? 'approved' : 'pending' }}">
                            {{ $auditStatus }}
                        </span>
                    </div>
                    <div class="timestamp-text">
                        TIME: {{ $auditDate }}
                    </div>
                    <div class="signature-bottom" style="margin-top: 8px;">
                        SIGNATURE: ______________________
                    </div>
                </td>
                <td class="auth-chain-cell">
                    <span class="lbl">Supply Chain</span>
                    <span class="val">{{ $supplyChainName }}</span>
                    <div class="timestamp-text">
                        STATUS: <span class="approval-status {{ $supplyChainApproved ? 'approved' : 'pending' }}">
                            {{ $supplyChainStatus }}
                        </span>
                    </div>
                    <div class="timestamp-text">
                        TIME: {{ $supplyChainDate }}
                    </div>
                    <div class="signature-bottom" style="margin-top: 8px;">
                        SIGNATURE: ______________________
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- =========================================================
    GATE PASS - Show only when fully approved
    ========================================================= --}}
    @if($waybill->gatepass_no)
    <table>
        <tr>
            <td style="background: #e8f6ec; padding: 4px 8px; border: 1px solid #2fa84f; text-align: center;">
                <span style="font-size: 8px; font-weight: bold; color: #1e7a34;">
                    ✅ GATE PASS: {{ $waybill->gatepass_no }}
                </span>
            </td>
        </tr>
    </table>
    @endif

    {{-- FOOTER --}}
    <div class="footer-note">
        This document was generated electronically | <strong>WayPass System</strong> | Ref: {{ $waybill->reference_no }}
        <br>
        <span style="font-size: 5.5px; color: #888;">
            All signatures confirm receipt and acceptance of goods in good condition.
        </span>
    </div>

</body>
</html>
