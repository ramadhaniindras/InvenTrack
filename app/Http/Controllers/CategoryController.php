<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class CategoryController extends Controller implements HasMiddleware
{
    /**
     * Satpam Modern Laravel 12
     */
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if ($request->user()->role !== 'admin') {
                    // Pakai 'error' biar dibaca SweetAlert nantinya
                    return redirect('/dashboard')->with('error', 'Akses ditolak! Khusus Admin.');
                }
                return $next($request);
            }),
        ];
    }

    /**
     * Tampilkan List Kategori
     */
    public function index()
    {
        return Inertia::render('Categories/Index', [
            'categories' => Category::select('id', 'name')->withCount('products')->latest()->get()
        ]);
    }

    /**
     * Simpan Kategori Baru
     */
    public function store(Request $request)
    {
        $attr = $request->validate([
            'name' => 'required|unique:categories,name'
        ]);

        Category::create($attr);

        return redirect()->back()->with('success', 'Kategori berhasil ditambah!');
    }

    public function update(Request $request, Category $category)
    {
        $attr = $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id
        ]);

        $category->update($attr);

        return redirect()->back()->with('success', 'Kategori diperbarui!');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return redirect()->back()->with('error', 'Gagal! Masih ada barang di kategori ini.');
        }
        $category->delete();

        return redirect()->back()->with('success', 'Kategori ' . $category->name . ' resmi dihapus! 🗑️');
    }
}

