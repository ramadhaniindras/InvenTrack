<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PurchaseOrderController extends Controller
{
    public function generate(Supplier $supplier)
    {
        $items = \App\Models\Product::where('supplier_id', $supplier->id)
            ->whereColumn('stock', '<=', 'min_stock')
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Gak ada barang yang kritis dari supplier ini, Bro!');
        }

        $data = [
            'date' => now()->format('d M Y'),
            'po_number' => 'PO-' . strtoupper(\Illuminate\Support\Str::random(5)), // PASTIKAN ADA INI
            'supplier' => $supplier,
            'items' => $items,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.purchase_order', $data);

        return $pdf->download('PO_' . str_replace(' ', '_', $supplier->name) . '.pdf');
    }
}