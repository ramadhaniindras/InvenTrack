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
        // 1. Siapkan Label Hari (7 Hari Terakhir)
        $days = collect(range(6, 0))->map(function ($i) {
            return Carbon::now()->subDays($i)->format('Y-m-d');
        });

        $stockIn = [];
        $stockOut = [];

        // 2. Ambil Data In & Out per Hari untuk Chart
        foreach ($days as $date) {
            $stockIn[] = (int) StockMovement::where('type', 'in')
                ->whereDate('created_at', $date)
                ->sum('quantity');
            $stockOut[] = (int) StockMovement::where('type', 'out')
                ->whereDate('created_at', $date)
                ->sum('quantity');
        }

        // 3. Ambil 5 Produk Terlaris (Top Selling)
        $topProducts = StockMovement::query()
            ->where('type', 'out')
            ->select('product_id', DB::raw('SUM(quantity) as total_out'))
            ->groupBy('product_id')
            ->with('product:id,name')
            ->orderByDesc('total_out')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'Produk Dihapus',
                    'total' => (int) $item->total_out,
                ];
            });

        // 4. Statistik Utama
        $stats = [
            'total_products' => Product::count(),
            'low_stock_count' => Product::whereColumn('stock', '<=', 'min_stock')->count(),
            'total_asset_value' => Product::selectRaw('SUM(stock * price) as total')->value('total') ?? 0,
            
            'low_stock_list' => Product::whereColumn('stock', '<=', 'min_stock')
                ->orderBy('stock', 'asc')
                ->take(5)
                ->get(),

            'recent_movements' => StockMovement::with(['product', 'user'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        // 5. RETURN KE INERTIA (Pastikan Sejajar)
        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'topProducts' => $topProducts, // SEJAJAR BRO, BIAR VUE BISA BACA
            'chartData' => [
                'labels' => $days->map(fn($d) => Carbon::parse($d)->format('d M')),
                'in' => $stockIn,
                'out' => $stockOut,
            ]
        ]);
    }
}