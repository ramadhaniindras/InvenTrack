<?php


namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SupplierController extends Controller implements HasMiddleware
{
    /**
     * Satpam Modern Laravel 12 - Supplier Edition
     */
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if ($request->user()->role !== 'admin') {
                    // Mentalin balik ke dashboard
                    return redirect('/dashboard')->with('error', 'Akses ditolak! Khusus Admin.');
                }
                return $next($request);
            }),
        ];
    }

    public function index()
    {
        return Inertia::render('Suppliers/Index', [
            'suppliers' => Supplier::latest()->get()
        ]);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'contact_person' => 'nullable',
            'address' => 'nullable',
        ]);
        Supplier::create($data);
        return redirect()->back()->with('success', 'Supplier berhasil ditambah!');
    }
    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'contact_person' => 'nullable',
            'address' => 'nullable',
        ]);

        $supplier->update($data);
        return redirect()->back()->with('success', 'Data supplier berhasil diupdate!');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->back()->with('success', 'Supplier berhasil dihapus!');
    }
}
