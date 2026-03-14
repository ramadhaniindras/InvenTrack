<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        return Inertia::render('StockMovements/Index', [
            'history' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search'])
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {

            $product = Product::lockForUpdate()->findOrFail($request->product_id);

            if ($request->type === 'out' && $product->stock < $request->quantity) {
                return back()->with('error', "Stok kurang! Sisa cuma {$product->stock}");
            }

            if ($request->type === 'in') {
                $product->increment('stock', $request->quantity);
            } else {
                $product->decrement('stock', $request->quantity);
            }

            StockMovement::create([
                'product_id' => $request->product_id,
                'user_id' => auth()->id(),
                'type' => $request->type,
                'quantity' => $request->quantity,
                'reference' => $request->reference,
                'notes' => $request->notes,
            ]);

            return back()->with('success', 'Stok berhasil diperbarui!');
        });
    }


    public function adjust(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'actual_stock' => 'required|integer|min:0',
            'reason' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($request) {
            $product = Product::lockForUpdate()->findOrFail($request->product_id);
            $diff = $request->actual_stock - $product->stock;

            if ($diff == 0)
                return back();

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => $diff > 0 ? 'in' : 'out',
                'quantity' => abs($diff),
                'reference' => 'ADJUSTMENT',
                'notes' => $request->reason ?? 'Penyesuaian stok manual',
            ]);

            $product->update(['stock' => $request->actual_stock]);

            return back()->with('success', 'Stok berhasil disesuaikan!');
        });
    }
}