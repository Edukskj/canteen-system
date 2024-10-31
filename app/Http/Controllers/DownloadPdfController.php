<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DownloadPdfController extends Controller
{
    public function download(Order $record) {

        $record = [
            $record
        ];

        $pdf = Pdf::loadView('pdf/pdf', ['data' => $record]);

        return $pdf->setPaper('a4')->stream('nome-padrao'.'.pdf');
    }
}
