<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

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
    $query = StockMovement::with(['product', 'user']);
    $period = $request->query('period', 'all');
    $title = "Laporan Transaksi";

    // Logic baru: Kalau ada filter bulan & tahun manual
    if ($request->has('month') && $request->has('year')) {
        $month = $request->month;
        $year = $request->year;
        $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
        
        $monthName = Carbon::create()->month($month)->translatedFormat('F');
        $title = "Laporan Periode $monthName $year";
    } 
    // Logic lama: Periode instan
    elseif ($period == 'daily') {
        $query->whereDate('created_at', Carbon::today());
        $title = "Laporan Harian";
    } elseif ($period == 'weekly') {
        $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        $title = "Laporan Mingguan";
    } elseif ($period == 'monthly') {
        $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        $title = "Laporan Bulanan";
    } elseif ($period == 'yearly') {
        $query->whereYear('created_at', Carbon::now()->year);
        $title = "Laporan Tahunan";
    }

    $data = $query->latest()->get();

    $pdf = Pdf::loadView('pdf.reports', [
        'data' => $data,
        'title' => $title,
        'date' => Carbon::now()->format('d M Y')
    ]);

    return $pdf->download('Laporan_InvenTrack_' . now()->format('YmdHis') . '.pdf');
}
}
