<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk
     */
    public function index(Request $request)
    {
        $query = Product::with('category');
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return Inertia::render('Products/Index', [
            'products' => $query->latest()->get(),
            'categories' => Category::all(),
            'filters' => $request->only(['category_id'])
        ]);
    }

    /**
     * Menyimpan produk baru
     */
    public function store(Request $request)
    {
        $attr = $request->validate([
            'sku' => 'required|unique:products,sku',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        Product::create($attr);
        return redirect()->route('products.index')->with('success', 'Barang baru berhasil ditambah!');
    }

    public function update(Request $request, Product $product)
    {
        $attr = $request->validate([
            'sku' => 'required|unique:products,sku,' . $product->id,
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|numeric',
            'min_stock' => 'required|numeric',
            'price' => 'required|numeric',
        ]);

        $product->update($request->all());
        return redirect()->back()->with('success', 'Data barang berhasil diupdate!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success', 'Barang udah dihapus dari list.');
    }

    public function exportPdf()
    {
        $products = Product::with('category')->get();
        $totalAsset = $products->sum(fn($p) => $p->stock * $p->price);

        $pdf = Pdf::loadView('pdf.products', [
            'products' => $products,
            'totalAsset' => $totalAsset,
            'date' => now()->format('d F Y')
        ]);
        return $pdf->download('laporan-stok-barang.pdf');
    }
}


