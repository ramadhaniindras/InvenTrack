<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        if ($startDate->isCurrentMonth()) {
            $endDate = Carbon::now();
        }

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

        $stats = [
            'total_products' => Product::count(),
            'total_suppliers' => Supplier::count(),
            'low_stock_count' => Product::whereColumn('stock', '<=', 'min_stock')->count(),
            'total_asset_value' => (int) Product::selectRaw('SUM(stock * price) as total')->value('total') ?? 0,
            'low_stock_list' => Product::with('supplier')
                ->whereColumn('stock', '<=', 'min_stock')
                ->orderBy('stock', 'asc')
                ->take(5)
                ->get(),
            'recent_movements' => StockMovement::with('product')
                ->latest()
                ->take(5)
                ->get(),
        ];

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

    public function downloadPO(Product $product)
    {
        $product->load('supplier');

        $items = [$product];

        $data = [
            'date' => now()->format('d M Y'),
            'po_number' => 'PO-' . strtoupper(Str::random(5)),
            'supplier' => $product->supplier,
            'items' => $items, 
        ];

        $pdf = Pdf::loadView('pdf.purchase_order', $data);

        $fileName = 'PO_' . str_replace(' ', '_', $product->name) . '.pdf';

        return $pdf->download($fileName);
    }
}