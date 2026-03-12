<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');
        $lowStockCount = Product::whereColumn('stock', '<=', 'min_stock')->count();
        $totalAssetValue = Product::select(DB::raw('SUM(stock * price) as total'))->first()->total ?? 0;
        $days = collect(range(6, 0))->map(function ($i) {
            return now()->subDays($i)->format('Y-m-d');
        });

        $stockIn = [];
        $stockOut = [];

        foreach ($days as $date) {
            $stockIn[] = StockMovement::where('type', 'in')->whereDate('created_at', $date)->sum('quantity');
            $stockOut[] = StockMovement::where('type', 'out')->whereDate('created_at', $date)->sum('quantity');
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_products' => $totalProducts,
                'low_stock_count' => $lowStockCount,
                'total_asset_value' => (float) $totalAssetValue,
                'recent_movements' => StockMovement::with('product')->latest()->limit(5)->get(),
            ],
            'chartData' => [
                'labels' => $days->map(fn($d) => date('d M', strtotime($d))),
                'in' => $stockIn,
                'out' => $stockOut,
            ]
        ]);
    }
}

