<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil data 7 hari terakhir buat Chart
        $days = collect(range(6, 0))->map(function ($i) {
            return Carbon::now()->subDays($i)->format('Y-m-d');
        });

        $stockIn = [];
        $stockOut = [];

        foreach ($days as $date) {
            $stockIn[] = StockMovement::where('type', 'in')->whereDate('created_at', $date)->sum('quantity');
            $stockOut[] = StockMovement::where('type', 'out')->whereDate('created_at', $date)->sum('quantity');
        }

        // 2. Hitung Statistik Utama
        $stats = [
            'total_products' => Product::count(),
            'low_stock_count' => Product::where('stock', '<=', 10)->count(),
            // Pastiin kolomnya 'price', kalau di DB lu 'harga', ganti tulisan price di bawah
            'total_asset_value' => Product::selectRaw('SUM(stock * price) as total')->value('total') ?? 0,

            // 3. List barang stok rendah (buat widget Alert)
            'low_stock_list' => Product::where('stock', '<=', 10)
                ->orderBy('stock', 'asc')
                ->take(5)
                ->get(),

            // 4. 5 Pergerakan stok terakhir (buat tabel bawah)
            'recent_movements' => StockMovement::with(['product', 'user'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        // 5. Kirim data ke Vue
        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'chartData' => [
                'labels' => $days->map(fn($d) => Carbon::parse($d)->format('d M')),
                'in' => $stockIn,
                'out' => $stockOut,
            ]
        ]);
    }
}