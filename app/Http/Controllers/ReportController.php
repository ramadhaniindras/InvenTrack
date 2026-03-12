<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
}