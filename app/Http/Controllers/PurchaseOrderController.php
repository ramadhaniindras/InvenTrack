<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller implements HasMiddleware
{
    /**
     * Satpam Modern Laravel 12
     * Memastikan hanya Admin yang bisa generate PO
     */
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if ($request->user()->role !== 'admin') {
                    // Tendang ke dashboard kalau bukan admin
                    return redirect('/dashboard')->with('error', 'Akses ditolak! Fitur PO hanya untuk Admin.');
                }
                return $next($request);
            }),
        ];
    }

    /**
     * Generate PDF Purchase Order berdasarkan Supplier
     */
    public function generate(Supplier $supplier)
    {
        // Ambil produk yang stoknya sudah mencapai/di bawah limit (min_stock)
        $items = Product::where('supplier_id', $supplier->id)
            ->whereColumn('stock', '<=', 'min_stock')
            ->get();

        // Cek kalau ternyata gak ada barang yang perlu di-restock
        if ($items->isEmpty()) {
            return back()->with('error', 'Gak ada barang yang kritis dari supplier ini');
        }

        // Data yang dikirim ke view PDF
        $data = [
            'date' => now()->format('d M Y'),
            'po_number' => 'PO-' . strtoupper(Str::random(5)),
            'supplier' => $supplier,
            'items' => $items,
        ];

        // Load view blade untuk di-render jadi PDF
        $pdf = Pdf::loadView('pdf.purchase_order', $data);

        // Download filenya dengan nama yang rapi
        $fileName = 'PO_' . str_replace(' ', '_', $supplier->name) . '_' . date('Ymd') . '.pdf';

        return $pdf->download($fileName);
    }
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // 2. Simpan Header PO
        $po = PurchaseOrder::create([
            'po_number' => 'PO-' . now()->format('YmdHis'),
            'supplier_id' => $request->supplier_id,
            'order_date' => now(),
            'status' => 'draft',
        ]);

        // 3. Simpan Detail Barang (Looping)
        foreach ($request->items as $item) {
            $po->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'] ?? 0, // Opsional kalau mau nyatet harga beli
            ]);
        }

        return redirect()->route('purchase-orders.index')->with('success', 'PO Berhasil dibuat!');
    }
}