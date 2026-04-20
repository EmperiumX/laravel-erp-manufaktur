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
        // 1. Hapus data dari database
        $supplier->delete();

        // 2. Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('suppliers.index')->with('success', 'Data Supplier berhasil dihapus!');
    }
    
    // ... biarkan fungsi lainnya kosong untuk saat ini
}