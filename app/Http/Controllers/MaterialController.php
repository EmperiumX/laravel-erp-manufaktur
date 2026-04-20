<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::all();
        return view('materials.index', compact('materials'));
    }

    public function create()
    {
        return view('materials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Bahan Pokok,Bahan Penolong,Packaging',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0' // Harus berupa angka
        ]);

        Material::create($request->all());
        return redirect()->route('materials.index')->with('success', 'Data Bahan Baku berhasil ditambahkan!');
    }

    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Bahan Pokok,Bahan Penolong,Packaging',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0'
        ]);

        $material->update($request->all());
        return redirect()->route('materials.index')->with('success', 'Data Bahan Baku berhasil diperbarui!');
    }

    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('materials.index')->with('success', 'Data Bahan Baku berhasil dihapus!');
    }
}