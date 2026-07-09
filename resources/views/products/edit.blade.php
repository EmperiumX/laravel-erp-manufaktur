<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Produk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('products.update', $product->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- BAGIAN KIRI: INFORMASI PRODUK -->
                            <div class="bg-gray-50 p-4 rounded-lg border">
                                <h3 class="font-bold text-lg mb-4 border-b pb-2">Informasi Produk</h3>
                                
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Produk <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="{{ $product->name }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">SKU / Kode Produk</label>
                                    <input type="text" name="sku" value="{{ $product->sku }}" class="w-full border-gray-300 rounded-md shadow-sm">
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Berat</label>
                                        <input type="number" name="weight" value="{{ $product->weight }}" class="w-full border-gray-300 rounded-md shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Satuan Berat</label>
                                        <input type="text" name="weight_unit" value="{{ $product->weight_unit }}" class="w-full border-gray-300 rounded-md shadow-sm">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Kemasan</label>
                                    <input type="text" name="packaging" value="{{ $product->packaging }}" class="w-full border-gray-300 rounded-md shadow-sm">
                                </div>

                                <!-- Rincian HPP -->
                                <div class="bg-gray-100/50 p-3.5 rounded-lg border border-gray-200/80 mb-4">
                                    <h4 class="font-semibold text-sm text-gray-800 mb-3">Rincian HPP (Harga Pokok Produksi)</h4>
                                    
                                    <div class="mb-3">
                                        <label class="block text-gray-700 text-xs font-bold mb-1">HPP Bahan Baku / BOM (Rp)</label>
                                        <div class="flex gap-2">
                                            <input type="number" step="0.01" name="hpp_bahan_baku" id="hppBahanBakuInput" value="{{ $hppBahanBaku }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                            @if(isset($bomHpp) && $bomHpp > 0)
                                                <button type="button" id="btnUseBomHpp" data-bom-hpp="{{ $bomHpp }}" class="bg-indigo-600 text-white px-3 py-2 rounded text-xs hover:bg-indigo-700 whitespace-nowrap">
                                                    Ambil dari BOM (Rp {{ number_format($bomHpp, 0, ',', '.') }})
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <label class="block text-gray-700 text-xs font-bold mb-1">Biaya Tenaga Kerja (Rp)</label>
                                            <input type="number" step="0.01" name="labor_cost" id="laborCostInput" value="{{ $product->labor_cost ?? 2656 }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 text-xs font-bold mb-1">Biaya Overhead (Rp)</label>
                                            <input type="number" step="0.01" name="overhead_cost" id="overheadCostInput" value="{{ $product->overhead_cost ?? 576 }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="block text-gray-700 text-xs font-bold mb-1">Biaya Lain-lain (Rp)</label>
                                        <input type="number" step="0.01" name="other_cost" id="otherCostInput" value="{{ $product->other_cost ?? 0 }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Contoh: 1000">
                                    </div>
                                    
                                    <div class="mt-2 border-t pt-2.5">
                                        <label class="block text-gray-700 text-xs font-bold mb-1">Total HPP Akhir (Rp)</label>
                                        <input type="text" id="totalHppInput" class="w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-800 font-bold text-sm" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- BAGIAN KANAN: HARGA JUAL -->
                            <div class="bg-green-50 p-4 rounded-lg border">
                                <h3 class="font-bold text-lg mb-4 border-b pb-2 text-green-800">Harga Jual (Rp)</h3>
                                
                                <div class="mb-3">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Harga Jual Satuan <span class="text-red-500">*</span></label>
                                    <input type="number" name="price" value="{{ (int)$price }}" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="Contoh: 15000">
                                </div>
                                <p class="text-xs text-gray-500 italic mt-4">*Harga ini akan digunakan sebagai harga default untuk seluruh kategori mitra penjualan.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-6 border-t pt-4">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-bold text-lg">Update Produk & Harga</button>
                            <a href="{{ route('products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            function calculateTotalHpp() {
                var bahanBaku = parseFloat($('#hppBahanBakuInput').val()) || 0;
                var labor = parseFloat($('#laborCostInput').val()) || 0;
                var overhead = parseFloat($('#overheadCostInput').val()) || 0;
                var other = parseFloat($('#otherCostInput').val()) || 0;
                
                var total = bahanBaku + labor + overhead + other;
                $('#totalHppInput').val(total.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }
            
            $('#hppBahanBakuInput, #laborCostInput, #overheadCostInput, #otherCostInput').on('input', function() {
                calculateTotalHpp();
            });
            
            $('#btnUseBomHpp').click(function() {
                var bomHpp = $(this).data('bom-hpp');
                $('#hppBahanBakuInput').val(bomHpp);
                calculateTotalHpp();
            });
            
            calculateTotalHpp(); // Initial run
        });
    </script>
</x-app-layout>