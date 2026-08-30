<?php

namespace App\Http\Controllers;

use App\Models\Waybill;
use Barryvdh\DomPDF\Facade\Pdf;

class WaybillController extends Controller
{
    public function generatePdf(Waybill $waybill)
    {
        // Load required relationships
        $waybill->load(['items', 'preparer', 'approvals']);

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
