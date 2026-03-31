<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class ManualPurchaseOrderController extends Controller
{
    // 1. Tampilkan Form Buat PO Manual
    public function create()
    {
        return inertia('PurchaseOrders/ManualCreate', [
            'suppliers' => Supplier::all(),
            // Load relasi supplier di product biar filter di Vue lancar
            'products' => Product::with('supplier')->get(),
        ]);
    }

    // 2. Simpan Data PO Manual
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric',
        ]);

        // Buat Header PO
        $po = PurchaseOrder::create([
            'po_number' => 'PO-MAN-' . strtoupper(bin2hex(random_bytes(3))),
            'supplier_id' => $request->supplier_id,
            'order_date' => now(),
            'status' => 'draft',
            'notes' => $request->notes,
        ]);

        // Buat Detail Item PO
        foreach ($request->items as $item) {
            $po->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ]);
        }

        // Balik ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'PO Manual Berhasil disimpan!');
    }

    // 3. Fungsi Download Utama (Sinkron dengan Blade)
    public function download(PurchaseOrder $po)
    {
        // Load relasi agar nama produk dan supplier muncul
        $po->load(['supplier', 'items.product']);

        // SESUAIKAN DENGAN VARIABEL DI BLADE LU
        $data = [
            'po_number' => $po->po_number,
            'date' => $po->order_date ? \Carbon\Carbon::parse($po->order_date)->format('d M Y') : now()->format('d M Y'),
            'supplier' => $po->supplier,
            'items' => $po->items,
            'notes' => $po->notes,
        ];

        $pdf = Pdf::loadView('pdf.purchase_order_manual', $data);

        // Pake stream() kalau mau liat dulu, download() kalau mau langsung narik file
        return $pdf->download('PO_MANUAL_' . $po->po_number . '.pdf');
    }

    // 4. Ambil PO Terakhir untuk Auto-Download
    public function downloadLatest()
    {
        // Ambil PO terakhir yang baru saja disimpan
        $po = PurchaseOrder::latest()->first();

        if (!$po) {
            return redirect()->back()->with('error', 'Belum ada data PO untuk didownload.');
        }

        return $this->download($po);
    }
}