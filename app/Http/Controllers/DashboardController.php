<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request; // Tambahin ini biar gak error
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request) // Pastikan ada Request $request
    {
        // 1. Ambil Input Filter (Default ke bulan/tahun sekarang)
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // 2. Tentukan Range Tanggal
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Kalau pilih bulan sekarang, batasi grafik sampai hari ini aja biar gak melompong kedepan
        if ($startDate->isCurrentMonth()) {
            $endDate = Carbon::now();
        }

        // 3. Siapkan Data Grafik (Harian dalam 1 bulan)
        $daysLabels = [];
        $stockIn = [];
        $stockOut = [];

        $current = $startDate->copy();
        while ($current <= $endDate) {
            $date = $current->format('Y-m-d');
            $daysLabels[] = $current->format('d M');

            $stockIn[] = (int) StockMovement::where('type', 'in')
                ->whereDate('created_at', $date)
                ->sum('quantity');

            $stockOut[] = (int) StockMovement::where('type', 'out')
                ->whereDate('created_at', $date)
                ->sum('quantity');

            $current->addDay();
        }

        // 4. Ambil 5 Produk Terlaris berdasarkan range tanggal
        $topProducts = StockMovement::query()
            ->where('type', 'out')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->select('product_id', DB::raw('SUM(quantity) as total_out'))
            ->groupBy('product_id')
            ->with('product:id,name')
            ->orderByDesc('total_out')
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'name' => $item->product->name ?? 'Dihapus',
                'total' => (int) $item->total_out
            ]);

        // 5. Statistik Utama (Gua satuin biar ringkas)
        $stats = [
            'total_products' => Product::count(),
            'low_stock_count' => Product::whereColumn('stock', '<=', 'min_stock')->count(),
            'total_asset_value' => (int) Product::selectRaw('SUM(stock * price) as total')->value('total') ?? 0,
            'low_stock_list' => Product::whereColumn('stock', '<=', 'min_stock')
                ->orderBy('stock', 'asc')
                ->take(5)
                ->get(),
            'recent_movements' => StockMovement::with('product')
                ->latest()
                ->take(5)
                ->get(),
        ];

        // 6. Return ke Vue
        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'topProducts' => $topProducts,
            'chartData' => [
                'labels' => $daysLabels,
                'in' => $stockIn,
                'out' => $stockOut,
            ],
            'filters' => [
                'month' => (int) $month,
                'year' => (int) $year,
            ]
        ]);
    }
}