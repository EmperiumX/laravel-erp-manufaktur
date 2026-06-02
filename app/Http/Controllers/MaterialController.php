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
        try {
            $material->delete();
            return redirect()->route('materials.index')->with('success', 'Data Bahan Baku berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('materials.index')->with('error', 'Data Bahan Baku tidak dapat dihapus karena masih digunakan di resep produk (BOM) atau transaksi PO/Penerimaan!');
            }
            return redirect()->route('materials.index')->with('error', 'Gagal menghapus data bahan baku: ' . $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return redirect()->route('materials.index')->with('error', 'Tidak ada data bahan baku yang terpilih.');
        }

        $deletedCount = 0;
        $failedNames = [];

        foreach ($ids as $id) {
            $material = Material::find($id);
            if ($material) {
                try {
                    $material->delete();
                    $deletedCount++;
                } catch (\Illuminate\Database\QueryException $e) {
                    $failedNames[] = $material->name;
                }
            }
        }

        if (count($failedNames) > 0) {
            $msg = "{$deletedCount} bahan baku berhasil dihapus.";
            $msgError = " Gagal menghapus bahan baku berikut karena masih digunakan di resep/transaksi: " . implode(', ', $failedNames);
            return redirect()->route('materials.index')->with('success', $msg)->with('error', $msgError);
        }

        return redirect()->route('materials.index')->with('success', "{$deletedCount} bahan baku berhasil dihapus.");
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MaterialExport, 'materials.xlsx');
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MaterialTemplateExport, 'template_import_bahan_baku.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
            'start_row' => 'nullable|integer|min:1',
            'start_column' => 'nullable|integer|min:0',
        ]);
        
        $startRow = $request->input('start_row', 2);
        $startColumn = $request->input('start_column', 1);

        $import = new \App\Imports\MaterialImport($startRow, $startColumn);
        \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
        
        $imported = $import->getImportedCount();
        $skipped = $import->getSkippedCount();
        
        return redirect()->route('materials.index')->with('success', "Import selesai! {$imported} data berhasil diimport, {$skipped} baris dilewati.");
    }
}