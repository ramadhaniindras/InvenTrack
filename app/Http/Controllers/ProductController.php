<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier']);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        return Inertia::render('Products/Index', [
            'products' => $query->latest()->get(),
            'categories' => Category::all(),
            'suppliers' => Supplier::all(),
            'filters' => $request->only(['category_id'])
        ]);
    }

    public function store(Request $request)
    {
        $attr = $request->validate([
            'sku' => 'required|unique:products,sku',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id', 
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
        ]);

        if ($request->hasFile('image')) {
            $attr['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($attr);

        return redirect()->route('products.index')->with('success', 'Barang mantap, foto & supplier tersimpan! 📸');
    }

    public function update(Request $request, Product $product)
    {
        $attr = $request->validate([
            'sku' => 'required|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id', 
            'stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $attr['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($attr);

        return redirect()->back()->with('success', 'Data barang & supplier berhasil diupdate! ✨');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->back()->with('success', 'Barang udah dihapus dari list. 🗑️');
    }

    public function exportPdf()
    {
        
        $products = Product::with(['category', 'supplier'])->get(); 
        $totalAsset = $products->sum(fn($p) => $p->stock * $p->price);

        $pdf = Pdf::loadView('pdf.products', [
            'products' => $products,
            'totalAsset' => $totalAsset,
            'date' => now()->format('d F Y')
        ]);

        return $pdf->download('laporan-stok-barang.pdf');
    }

    public function downloadQrCode($id)
    {
        $product = Product::findOrFail($id);
        $qrcode = QrCode::size(200)->generate($product->sku);

        return view('products.qrcode_print', compact('product', 'qrcode'));
    }
}