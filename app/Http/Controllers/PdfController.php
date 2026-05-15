<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function generate(Sale $sale)
    {
        $sale->load(['client', 'seller', 'items.product', 'installments']);
        
        $pdf = Pdf::loadView('sales.pdf', compact('sale'));
        
        return $pdf->download("resumo_venda_{$sale->id}.pdf");
    }
}
