<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\StockMovement;
use App\Models\Product; // Tambahin ini buat ngitung aset
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if ($request->user()->role !== 'admin') {
                    return redirect('/dashboard')->with('error', 'Akses ditolak! Lu bukan admin.');
                }
                return $next($request);
            }),
        ];
    }

    public function index()
    {
        return Inertia::render('Reports/Index');
    }

    public function export(Request $request)
    {
        $period = $request->query('period');
        $filter = $request->query('filter');

        if (!$filter) {
            $filter = date('Y-m-d');
        }

        $query = StockMovement::query()->with(['product', 'user']);

        // Logic Filter Periode
        if ($period == 'daily') {
            $query->whereDate('created_at', $filter);
            $title = "Laporan Harian - " . Carbon::parse($filter)->translatedFormat('d F Y');
        } elseif ($period == 'weekly') {
            $startDate = Carbon::parse($filter)->startOfDay();
            $endDate = Carbon::parse($filter)->addDays(6)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
            $title = "Laporan Mingguan " . $startDate->format('d-m-Y') . " sd " . $endDate->format('d-m-Y');
        } elseif ($period == 'monthly') {
            $date = Carbon::parse($filter);
            $query->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month);
            $title = "Laporan Bulanan - " . $date->translatedFormat('F Y');
        } elseif ($period == 'yearly') {
            $query->whereYear('created_at', $filter);
            $title = "Laporan Tahunan - " . $filter;
        }

        $result = $query->orderBy('created_at', 'desc')->get();

        // Hitung Total Aset (biar variabel $totalAsset nggak undefined)
        // Karena ini report transaksi, kita set 0 atau itung dari modal Product
        $totalAsset = \App\Models\Product::sum(DB::raw('stock * price'));

        $pdf = Pdf::loadView('pdf.reports', [
            'data' => $result,      // Ini buat @forelse($data as $index => $p)
            'products' => $result,      // Ini buat jaga-jaga kalau lu pake nama $products
            'title' => $title,
            'date' => $filter,
            'totalAsset' => $totalAsset   // Ini biar footer tabel nggak error
        ]);

        $safeFileName = str_replace(['/', '\\'], '-', $title);
        return $pdf->download($safeFileName . ".pdf");
    }
}