<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function store(Request $request)
    {
        $attr = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        \App\Models\StockMovement::create($attr);
        $product = \App\Models\Product::find($request->product_id);

        if ($request->type === 'in') {
            $product->increment('stock', $request->quantity);
            $pesan = "Stok {$product->name} berhasil ditambahin!";
        } else {

            if ($product->stock < $request->quantity) {
                return redirect()->back()->with('error', 'Stok nggak cukup bro!');
            }
            $product->decrement('stock', $request->quantity);
            $pesan = "Stok {$product->name} berhasil dikurangin!";
        }

        return redirect()->back()->with('success', $pesan);
    }

    public function index()
    {
        return inertia('StockMovements/Index', [
            'movements' => \App\Models\StockMovement::with('product')
                ->latest()
                ->paginate(10)
        ]);
    }
}

