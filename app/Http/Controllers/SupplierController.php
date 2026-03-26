<?php


namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{

    public function index()
    {
        return Inertia::render('Suppliers/Index', [
            'suppliers' => Supplier::orderBy('name', 'asc')->get()
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
