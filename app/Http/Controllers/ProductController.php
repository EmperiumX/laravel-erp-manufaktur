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
        return view('products.create');
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
            'price' => 'required|numeric|min:0'
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

        // 3. Simpan satu harga tunggal untuk semua kategori di tabel product_prices guna kompatibilitas
        $categories = ['Mitra', 'Agen', 'Distributor', 'Reseller', 'End User', 'Maklon'];
        foreach ($categories as $category) {
            $product->prices()->create([
                'category' => $category,
                'price' => $request->price
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Data Produk & Harga berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        $price = $product->prices()->where('category', 'End User')->first()->price ?? 0;
        return view('products.edit', compact('product', 'price'));
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
            'price' => 'required|numeric|min:0'
        ]);

        // 2. Update Data Produk Utama
        $product->update([
            'sku' => $request->sku,
            'name' => $request->name,
            'weight' => $request->weight,
            'weight_unit' => $request->weight_unit,
            'packaging' => $request->packaging,
        ]);

        // 3. Update semua kategori harga dengan nilai yang sama
        $categories = ['Mitra', 'Agen', 'Distributor', 'Reseller', 'End User', 'Maklon'];
        foreach ($categories as $category) {
            $product->prices()->updateOrCreate(
                ['category' => $category],
                ['price' => $request->price]
            );
        }

        return redirect()->route('products.index')->with('success', 'Data Produk & Harga berhasil diperbarui!');
    }
    public function destroy(Product $product)
    {
        try {
            // Karena di migration file product_prices kita menggunakan onDelete('cascade'), 
            // maka saat produk dihapus, semua harganya akan otomatis terhapus!
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Data Produk berhasil dihapus!');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('products.index')->with('error', 'Data Produk tidak dapat dihapus karena masih digunakan di data transaksi lain!');
            }
            return redirect()->route('products.index')->with('error', 'Gagal menghapus data produk: ' . $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return redirect()->route('products.index')->with('error', 'Tidak ada data produk yang terpilih.');
        }

        $deletedCount = 0;
        $failedNames = [];

        foreach ($ids as $id) {
            $product = Product::find($id);
            if ($product) {
                try {
                    $product->delete();
                    $deletedCount++;
                } catch (\Illuminate\Database\QueryException $e) {
                    $failedNames[] = $product->name;
                }
            }
        }

        if (count($failedNames) > 0) {
            $msg = "{$deletedCount} produk berhasil dihapus.";
            $msgError = " Gagal menghapus produk berikut karena masih digunakan di data transaksi lain: " . implode(', ', $failedNames);
            return redirect()->route('products.index')->with('success', $msg)->with('error', $msgError);
        }

        return redirect()->route('products.index')->with('success', "{$deletedCount} produk berhasil dihapus.");
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