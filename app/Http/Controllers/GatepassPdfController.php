<?php

namespace App\Http\Controllers;

use App\Models\Waybill;
use Barryvdh\DomPDF\Facade\Pdf;

class GatepassPdfController extends Controller
{
    /**
     * Preview / Stream the Gatepass PDF (Available at all stages).
     */
    public function stream(Waybill $waybill)
    {
        $pdf = $this->generatePdf($waybill);

        return $pdf->stream("Gatepass-Preview-{$waybill->invoice_no}.pdf");
    }

    /**
     * Download the official Gatepass PDF (Only after approval is complete).
     */
    public function download(Waybill $waybill)
    {
        // Block download if approval is not complete
        if (! $this->isFullyApproved($waybill)) {
            abort(403, 'Official Gatepass download is locked until all approvals are completed.');
        }

        $pdf = $this->generatePdf($waybill);

        return $pdf->download("Gatepass-{$waybill->invoice_no}.pdf");
    }

    /**
     * Check if the waybill has completed the full approval workflow.
     */
    private function isFullyApproved(Waybill $waybill): bool
    {
        // Adjust status key to match your database enum ('approved', 'cleared_at_gate', etc.)
        return in_array(strtolower($waybill->status), ['approved', 'cleared_at_gate']);
    }

    /**
     * Build the PDF instance.
     */
    private function generatePdf(Waybill $waybill)
    {
        $waybill->load(['items', 'preparer']);

        return Pdf::loadView('pdf.gatepass', compact('waybill'))
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setPaper('a4', 'portrait');
    }
}
