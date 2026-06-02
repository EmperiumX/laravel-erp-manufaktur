<?php

namespace App\Http\Controllers;

use App\Models\Supplier; // <-- PASTIKAN IMPORT MODEL INI
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        // Ambil semua data supplier dari database
        $suppliers = Supplier::all(); 
        
        // Tampilkan halaman view sambil membawa variabel $suppliers
        return view('suppliers.index', compact('suppliers'));
    }
    public function create()
    {
        // Menampilkan halaman form tambah data
        return view('suppliers.create');
    }
    public function store(Request $request)
    {
        // 1. Validasi data (Nama wajib diisi, maksimal 255 karakter)
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        // 2. Simpan ke database menggunakan Mass Assignment
        // (Ini bisa berjalan karena kita sudah menambahkan $fillable di Model Supplier sebelumnya)
        Supplier::create($request->all());

        // 3. Redirect (kembalikan) user ke halaman index beserta pesan sukses
        return redirect()->route('suppliers.index')->with('success', 'Data Supplier berhasil ditambahkan!');
    }
    public function edit(Supplier $supplier)
    {
        // $supplier sudah otomatis dicari oleh Laravel dari database berdasarkan ID di URL
        return view('suppliers.edit', compact('supplier'));
    }
    public function update(Request $request, Supplier $supplier)
    {
        // 1. Validasi inputan baru
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        // 2. Timpa data lama dengan data baru
        $supplier->update($request->all());

        // 3. Kembalikan ke halaman index dengan pesan sukses
        return redirect()->route('suppliers.index')->with('success', 'Data Supplier berhasil diperbarui!');
    }
    public function destroy(Supplier $supplier)
    {
        try {
            // 1. Hapus data dari database
            $supplier->delete();

            // 2. Redirect kembali ke halaman index dengan pesan sukses
            return redirect()->route('suppliers.index')->with('success', 'Data Supplier berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('suppliers.index')->with('error', 'Data Supplier tidak dapat dihapus karena masih digunakan di data transaksi lain!');
            }
            return redirect()->route('suppliers.index')->with('error', 'Gagal menghapus data supplier: ' . $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return redirect()->route('suppliers.index')->with('error', 'Tidak ada data supplier yang terpilih.');
        }

        $deletedCount = 0;
        $failedNames = [];

        foreach ($ids as $id) {
            $supplier = Supplier::find($id);
            if ($supplier) {
                try {
                    $supplier->delete();
                    $deletedCount++;
                } catch (\Illuminate\Database\QueryException $e) {
                    $failedNames[] = $supplier->name;
                }
            }
        }

        if (count($failedNames) > 0) {
            $msg = "{$deletedCount} supplier berhasil dihapus.";
            $msgError = " Gagal menghapus supplier berikut karena masih digunakan di data transaksi lain: " . implode(', ', $failedNames);
            return redirect()->route('suppliers.index')->with('success', $msg)->with('error', $msgError);
        }

        return redirect()->route('suppliers.index')->with('success', "{$deletedCount} supplier berhasil dihapus.");
    }
    
    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SupplierExport, 'suppliers.xlsx');
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

        $import = new \App\Imports\SupplierImport($startRow, $startColumn);
        \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
        
        $imported = $import->getImportedCount();
        $skipped = $import->getSkippedCount();
        
        return redirect()->route('suppliers.index')->with('success', "Import selesai! {$imported} data berhasil diimport, {$skipped} baris dilewati.");
    }

    /**
     * Download template Excel untuk import supplier
     */
    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SupplierTemplateExport, 'template_import_supplier.xlsx');
    }
}