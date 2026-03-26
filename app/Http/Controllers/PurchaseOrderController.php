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
        // Cari barang yang stoknya di bawah limit
        $items = Product::where('supplier_id', $supplier->id)
            ->whereColumn('stock', '<=', 'min_stock')
            ->get();

        if ($items->isEmpty()) {
            return redirect()->back()->with('error', 'Gak ada barang yang kritis, aman Bro!');
        }

        $poNumber = 'PO-' . date('Ymd') . '-' . rand(100, 999);

        $pdf = Pdf::loadView('pdf.purchase_order', [
            'supplier' => $supplier,
            'items' => $items,
            'poNumber' => $poNumber,
            'date' => date('d/m/Y'),
        ]);
        return $pdf->download("PO-{$supplier->name}-" . date('Ymd') . ".pdf");
    }
}