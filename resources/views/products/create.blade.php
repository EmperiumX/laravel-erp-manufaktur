<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Produk Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- BAGIAN KIRI: INFORMASI PRODUK -->
                            <div class="bg-gray-50 p-4 rounded-lg border">
                                <h3 class="font-bold text-lg mb-4 border-b pb-2">Informasi Produk</h3>
                                
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Produk <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="Contoh: Bandeng Retort">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">SKU / Kode Produk</label>
                                    <input type="text" name="sku" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Opsional">
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Berat</label>
                                        <input type="number" name="weight" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Cth: 250">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Satuan Berat</label>
                                        <input type="text" name="weight_unit" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Cth: Gram">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Kemasan</label>
                                    <input type="text" name="packaging" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Cth: Pack, Dus">
                                </div>
                            </div>

                            <!-- BAGIAN KANAN: HARGA JUAL -->
                            <div class="bg-green-50 p-4 rounded-lg border">
                                <h3 class="font-bold text-lg mb-4 border-b pb-2 text-green-800">Harga Jual (Rp)</h3>
                                
                                <div class="mb-3">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Harga Jual Satuan <span class="text-red-500">*</span></label>
                                    <input type="number" name="price" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="Contoh: 15000">
                                </div>
                                <p class="text-xs text-gray-500 italic mt-4">*Harga ini akan digunakan sebagai harga default untuk seluruh kategori mitra penjualan.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-6 border-t pt-4">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-bold text-lg">Simpan Produk & Harga</button>
                            <a href="{{ route('products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>