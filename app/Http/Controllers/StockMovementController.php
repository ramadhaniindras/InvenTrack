<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Tambahin ini bro

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['product', 'user'])->latest();

        if ($request->search) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }

        // Pastiin path ini SAMA PERSIS dengan folder di resources/js/Pages
        return Inertia::render('StockMovements/Index', [
            'history' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $attr = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $attr['user_id'] = Auth::id(); // Pake Facade biar lebih mantap

        $product = Product::findOrFail($request->product_id);

        if ($request->type === 'out' && $product->stock < $request->quantity) {
            return redirect()->back()->with('error', 'Stok nggak cukup bro!');
        }

        DB::transaction(function () use ($attr, $product) {
            StockMovement::create($attr);

            if ($attr['type'] === 'in') {
                $product->increment('stock', $attr['quantity']);
            } else {
                $product->decrement('stock', $attr['quantity']);
            }
        });

        $pesan = $attr['type'] === 'in'
            ? "Stok {$product->name} berhasil ditambahin!"
            : "Stok {$product->name} berhasil dikurangin!";

        return redirect()->back()->with('success', $pesan);
    }
}