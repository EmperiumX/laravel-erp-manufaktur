<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Produk & Resep: ') }} <span class="text-blue-600">{{ $product->name }}</span>
            </h2>
            <a href="{{ route('products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Notifikasi Sukses / Error -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- BAGIAN ATAS: INFO & HARGA -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Info Produk -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <h3 class="font-bold text-lg mb-4 border-b pb-2">Informasi Produk</h3>
                    <table class="w-full text-sm">
                        <tr><td class="py-2 text-gray-600 w-1/3">SKU</td><td class="font-semibold">: {{ $product->sku ?? '-' }}</td></tr>
                        <tr><td class="py-2 text-gray-600">Nama Produk</td><td class="font-semibold">: {{ $product->name }}</td></tr>
                        <tr><td class="py-2 text-gray-600">Berat</td><td class="font-semibold">: {{ $product->weight }} {{ $product->weight_unit }}</td></tr>
                        <tr><td class="py-2 text-gray-600">Kemasan</td><td class="font-semibold">: {{ $product->packaging }}</td></tr>
                    </table>
                </div>

                <!-- Struktur Harga -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <h3 class="font-bold text-lg mb-4 border-b pb-2 text-green-700">Struktur Harga Jual</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        @foreach($product->prices as $price)
                            <div class="flex justify-between border-b pb-1">
                                <span class="text-gray-600">{{ $price->category }}</span>
                                <span class="font-bold text-green-600">Rp {{ number_format($price->price, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- BAGIAN BAWAH: BILL OF MATERIALS (BOM) -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="font-bold text-lg mb-4 border-b pb-2 text-blue-700">Bill of Materials (BOM) / Resep Produksi</h3>
                
                <!-- Form Tambah Bahan Baku ke Resep -->
                <div class="bg-blue-50 p-4 rounded mb-6 border border-blue-100">
                    <form action="{{ route('boms.store', $product->id) }}" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
                        @csrf
                        <div class="w-full md:w-1/2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Bahan Baku <span class="text-red-500">*</span></label>
                            <select name="material_id" id="materialSelect" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Cari Bahan Baku...</option>
                                @foreach($materials as $material)
                                    <!-- Kita simpan harga satuan di data-attribute untuk referensi -->
                                    <option value="{{ $material->id }}" data-unit="{{ $material->unit }}">
                                        {{ $material->name }} (Rp {{ number_format($material->unit_price, 2, ',', '.') }} / {{ $material->unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full md:w-1/4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Kuantitas <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <input type="number" step="0.0001" name="quantity" class="w-full border-gray-300 rounded-l-md shadow-sm" required placeholder="Cth: 0.5">
                                <span id="unitDisplay" class="bg-gray-200 border border-gray-300 text-gray-600 px-3 py-2 rounded-r-md text-sm font-bold">Unit</span>
                            </div>
                        </div>
                        <div class="w-full md:w-1/4">
                            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah ke Resep</button>
                        </div>
                    </form>
                </div>

                <!-- Tabel BOM & Kalkulasi HPP -->
                <table class="w-full table-auto border-collapse border border-gray-300 text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2">No</th>
                            <th class="border px-4 py-2">Bahan Baku & Kemasan</th>
                            <th class="border px-4 py-2 text-center">Kuantitas</th>
                            <th class="border px-4 py-2 text-right">Harga Satuan (Rp)</th>
                            <th class="border px-4 py-2 text-right">Subtotal (Rp)</th>
                            <th class="border px-4 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalHpp = 0; @endphp
                        @forelse($product->boms as $index => $bom)
                            @php 
                                // Kalkulasi subtotal (kuantitas * harga material)
                                $subtotal = $bom->quantity * $bom->material->unit_price;
                                $totalHpp += $subtotal;
                            @endphp
                            <tr>
                                <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                                <td class="border px-4 py-2">
                                    <span class="font-bold">{{ $bom->material->name }}</span>
                                    <br><span class="text-xs text-gray-500">{{ $bom->material->type }}</span>
                                </td>
                                <td class="border px-4 py-2 text-center font-bold text-blue-600">
                                    {{ rtrim(rtrim(number_format($bom->quantity, 4, ',', '.'), '0'), ',') }} {{ $bom->material->unit }}
                                </td>
                                <td class="border px-4 py-2 text-right">{{ number_format($bom->material->unit_price, 2, ',', '.') }}</td>
                                <td class="border px-4 py-2 text-right font-bold">{{ number_format($subtotal, 2, ',', '.') }}</td>
                                <td class="border px-4 py-2 text-center">
                                    <form action="{{ route('boms.destroy', $bom->id) }}" method="POST" onsubmit="return confirm('Hapus bahan ini dari resep?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-bold">X</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="border px-4 py-4 text-center text-gray-500 italic">Belum ada bahan baku dalam resep ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <!-- Menampilkan Total HPP -->
                    @if($product->boms->count() > 0)
                    <tfoot>
                        <tr class="bg-yellow-100">
                            <td colspan="4" class="border px-4 py-3 text-right font-bold text-lg">Total HPP (Harga Pokok Produksi) :</td>
                            <td class="border px-4 py-3 text-right font-bold text-lg text-red-600">Rp {{ number_format($totalHpp, 2, ',', '.') }}</td>
                            <td class="border px-4 py-3"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>

            </div>

        </div>
    </div>

    <!-- Integrasi Select2 dan Script JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inisialisasi fitur pencarian Select2
            $('#materialSelect').select2({
                placeholder: "Ketik nama bahan baku...",
                allowClear: true
            });

            // Ubah text satuan (Unit) di sebelah kolom kuantitas saat bahan baku dipilih
            $('#materialSelect').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var unit = selectedOption.data('unit');
                
                if(unit) {
                    $('#unitDisplay').text(unit);
                } else {
                    $('#unitDisplay').text('Unit');
                }
            });
        });
    </script>
</x-app-layout>