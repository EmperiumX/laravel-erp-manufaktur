<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPrice; // Import model harga
use Illuminate\Http\Request;
use App\Models\Material; 

class ProductController extends Controller
{
    public function index()
    {
        // Ambil data produk beserta relasi harganya menggunakan 'with' (Eager Loading agar cepat)
        $products = Product::with('prices')->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        // Kita kirimkan daftar kategori ke view agar mudah dibuatkan form input harganya
        $categories =['Mitra', 'Agen', 'Distributor', 'Reseller', 'End User', 'Maklon'];
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Data Produk Utama
        $request->validate([
            'sku' => 'nullable|string|unique:products,sku',
            'name' => 'required|string|max:255',
            'weight' => 'nullable|integer',
            'weight_unit' => 'nullable|string|max:50',
            'packaging' => 'nullable|string|max:100',
            // Validasi untuk array prices
            'prices' => 'required|array',
            'prices.*' => 'nullable|numeric|min:0'
        ]);

        // 2. Simpan Data ke tabel products
        $product = Product::create([
            'sku' => $request->sku,
            'name' => $request->name,
            'weight' => $request->weight,
            'weight_unit' => $request->weight_unit,
            'packaging' => $request->packaging,
            'hpp' => 0 // Sementara HPP 0 dulu, nanti kita buat fitur hitung HPP otomatis dari BOM
        ]);

        // 3. Looping dan simpan Data ke tabel product_prices
        foreach ($request->prices as $category => $price) {
            if ($price != null) { // Jika harganya diisi
                $product->prices()->create([
                    'category' => $category,
                    'price' => $price
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Data Produk & Harga berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        $categories =['Mitra', 'Agen', 'Distributor', 'Reseller', 'End User', 'Maklon'];
        // Kita tidak perlu memuat relasi prices secara manual di sini karena Laravel 
        // secara otomatis bisa memanggilnya di View nanti menggunakan $product->prices
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        // 1. Validasi
        $request->validate([
            'sku' => 'nullable|string|unique:products,sku,' . $product->id, // Abaikan SKU milik produk ini sendiri
            'name' => 'required|string|max:255',
            'weight' => 'nullable|integer',
            'weight_unit' => 'nullable|string|max:50',
            'packaging' => 'nullable|string|max:100',
            'prices' => 'required|array',
            'prices.*' => 'nullable|numeric|min:0'
        ]);

        // 2. Update Data Produk Utama
        $product->update([
            'sku' => $request->sku,
            'name' => $request->name,
            'weight' => $request->weight,
            'weight_unit' => $request->weight_unit,
            'packaging' => $request->packaging,
        ]);

        // 3. Update atau Create Harga
        foreach ($request->prices as $category => $price) {
            if ($price != null) {
                // updateOrCreate( [Kondisi Pencarian], [Data yang diupdate/disimpan] )
                $product->prices()->updateOrCreate(
                    ['category' => $category],
                    ['price' => $price]
                );
            } else {
                // Jika user mengosongkan inputan harga saat edit, hapus harga tersebut dari database
                $product->prices()->where('category', $category)->delete();
            }
        }

        return redirect()->route('products.index')->with('success', 'Data Produk & Harga berhasil diperbarui!');
    }
    public function destroy(Product $product)
    {
        // Karena di migration file product_prices kita menggunakan onDelete('cascade'), 
        // maka saat produk dihapus, semua harganya akan otomatis terhapus!
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Data Produk berhasil dihapus!');
    }
    public function show(Product $product)
    {
        // Eager Loading: Ambil produk beserta relasi harganya, dan relasi BOM beserta detail materialnya
        $product->load(['prices', 'boms.material']); 
        
        // Ambil semua material untuk ditampilkan di Dropdown pilihan bahan baku
        $materials = Material::all();

        return view('products.show', compact('product', 'materials'));
    }
    
    // ... biarkan edit, update, destroy kosong dulu
}