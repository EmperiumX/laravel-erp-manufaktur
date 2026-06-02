<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::all();
        return view('stores.index', compact('stores'));
    }

    public function create()
    {
        return view('stores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Mitra,Agen,Distributor,Reseller,End User,Maklon',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        Store::create($request->all());
        return redirect()->route('stores.index')->with('success', 'Data Toko berhasil ditambahkan!');
    }

    public function edit(Store $store)
    {
        return view('stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Mitra,Agen,Distributor,Reseller,End User,Maklon',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        $store->update($request->all());
        return redirect()->route('stores.index')->with('success', 'Data Toko berhasil diperbarui!');
    }

    public function destroy(Store $store)
    {
        try {
            $store->delete();
            return redirect()->route('stores.index')->with('success', 'Data Toko berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('stores.index')->with('error', 'Data Toko tidak dapat dihapus karena masih digunakan di data transaksi/konsinyasi lain!');
            }
            return redirect()->route('stores.index')->with('error', 'Gagal menghapus data toko: ' . $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return redirect()->route('stores.index')->with('error', 'Tidak ada data toko yang terpilih.');
        }

        $deletedCount = 0;
        $failedNames = [];

        foreach ($ids as $id) {
            $store = Store::find($id);
            if ($store) {
                try {
                    $store->delete();
                    $deletedCount++;
                } catch (\Illuminate\Database\QueryException $e) {
                    $failedNames[] = $store->name;
                }
            }
        }

        if (count($failedNames) > 0) {
            $msg = "{$deletedCount} toko berhasil dihapus.";
            $msgError = " Gagal menghapus toko berikut karena masih digunakan di data transaksi lain: " . implode(', ', $failedNames);
            return redirect()->route('stores.index')->with('success', $msg)->with('error', $msgError);
        }

        return redirect()->route('stores.index')->with('success', "{$deletedCount} toko berhasil dihapus.");
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\StoreExport, 'stores.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
            'start_row' => 'nullable|integer|min:1',
            'start_column' => 'nullable|integer|min:0',
        ]);
        
        // Ambil konfigurasi fleksibel dari request, default: baris 2, kolom 2 (index 1)
        $startRow = $request->input('start_row', 2);
        $startColumn = $request->input('start_column', 1);

        $import = new \App\Imports\StoreImport($startRow, $startColumn);
        \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
        
        $imported = $import->getImportedCount();
        $skipped = $import->getSkippedCount();
        
        return redirect()->route('stores.index')->with('success', "Import selesai! {$imported} data berhasil diimport, {$skipped} baris dilewati.");
    }

    /**
     * Download template Excel untuk import toko/mitra
     */
    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\StoreTemplateExport, 'template_import_toko.xlsx');
    }
}