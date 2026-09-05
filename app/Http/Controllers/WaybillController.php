<?php

namespace App\Http\Controllers;

use App\Models\Waybill;
use Barryvdh\DomPDF\Facade\Pdf;

class WaybillController extends Controller
{
    /**
     * PREVIEW: Allowed at EVERY stage for every actor.
     * Loads the same view but passes a flag to show a watermark/draft badge.
     */
    public function preview(Waybill $waybill)
    {
        // Load required relationships
        $waybill->load(['items', 'preparer']);

        // Pass 'is_preview' => true to your blade file so you can add a watermark
        // or hide sensitive official info (like final signatures).
        return view('pdf.waybill', [
            'waybill' => $waybill,
            'is_preview' => true,
        ]);
    }

    /**
     * PRINT/GENERATE PDF: ONLY allowed once the 3-step chain is complete.
     */
    public function generatePdf(Waybill $waybill)
    {
        // 🔒 SECURITY CHECK: Block if not fully approved (Warehouse + Audit + Supply Chain)
        abort_unless($waybill->isFullyApproved(), 403, 'This document is locked. Printing is disabled until the approval chain is complete.');

        // Load required relationships
        $waybill->load(['items', 'preparer']);

        $pdf = Pdf::loadView('pdf.waybill', [
            'waybill' => $waybill,
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'          => 'dejavu sans',
            'isHtml5ParserEnabled' => true,
        ]);

        return $pdf->stream('waybill-' . $waybill->reference_no . '.pdf');
    }
}
