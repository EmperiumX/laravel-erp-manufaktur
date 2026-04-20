<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BillOfMaterial;
use Illuminate\Http\Request;

class BillOfMaterialController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|numeric|min:0'
        ]);

        // Mencegah bahan baku yang sama dimasukkan 2x ke produk yang sama
        $exists = $product->boms()->where('material_id', $request->material_id)->exists();
        if($exists){
            return back()->with('error', 'Bahan baku ini sudah ada di dalam resep!');
        }

        // Simpan resep
        $product->boms()->create([
            'material_id' => $request->material_id,
            'quantity' => $request->quantity
        ]);

        // Fungsi back() akan mengembalikan user ke halaman sebelumnya (halaman detail produk)
        return back()->with('success', 'Bahan baku berhasil ditambahkan ke resep!');
    }

    public function destroy(BillOfMaterial $bom)
    {
        $bom->delete();
        return back()->with('success', 'Bahan baku berhasil dihapus dari resep!');
    }
}