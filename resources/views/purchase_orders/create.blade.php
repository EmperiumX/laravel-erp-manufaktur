<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Purchase Order (PO) Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('purchase-orders.store') }}" method="POST" id="poForm">
                        @csrf
                        
                        <!-- BAGIAN ATAS: INFO SUPPLIER & TANGGAL -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 bg-gray-50 p-4 rounded-lg border">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Supplier <span class="text-red-500">*</span></label>
                                <select name="supplier_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Order <span class="text-red-500">*</span></label>
                                <!-- Otomatis terisi tanggal hari ini -->
                                <input type="date" name="order_date" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Catatan Tambahan (Opsional)</label>
                                <input type="text" name="notes" placeholder="Contoh: Tolong kirim pagi hari" class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>

                        <!-- BAGIAN BAWAH: TABEL ITEM BARANG -->
                        <h3 class="font-bold text-lg mb-4 text-blue-700">Daftar Barang yang Dipesan</h3>
                        <table class="w-full table-auto border-collapse border border-gray-300 text-sm mb-4" id="itemTable">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2 w-2/5">Bahan Baku / Material</th>
                                    <th class="border px-4 py-2 w-1/5">Kuantitas</th>
                                    <th class="border px-4 py-2 w-1/5">Harga Beli Satuan (Rp)</th>
                                    <th class="border px-4 py-2 w-1/5">Subtotal (Rp)</th>
                                    <th class="border px-4 py-2">Hapus</th>
                                </tr>
                            </thead>
                            <tbody id="po-items-tbody">
                                <!-- Baris Pertama (Default) -->
                                <tr class="item-row">
                                    <td class="border px-2 py-2">
                                        <!-- Perhatikan atribut name pake kurung siku[] yang menandakan ini array -->
                                        <select name="materials[]" class="w-full border-gray-300 rounded material-select" required>
                                            <option value="">-- Pilih Barang --</option>
                                            @foreach($materials as $material)
                                                <!-- Simpan harga default di data-price agar bisa diambil JS -->
                                                <option value="{{ $material->id }}" data-price="{{ $material->unit_price }}">
                                                    {{ $material->name }} ({{ $material->unit }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border px-2 py-2">
                                        <input type="number" step="0.0001" name="quantities[]" class="w-full border-gray-300 rounded text-center qty-input" required min="0.01">
                                    </td>
                                    <td class="border px-2 py-2">
                                        <input type="number" step="0.01" name="unit_prices[]" class="w-full border-gray-300 rounded text-right price-input" required min="0">
                                    </td>
                                    <td class="border px-4 py-2 text-right font-bold text-gray-700">
                                        Rp <span class="subtotal-display">0</span>
                                    </td>
                                    <td class="border px-2 py-2 text-center">
                                        <button type="button" class="text-red-500 hover:text-red-700 font-bold remove-row-btn" disabled>X</button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="bg-blue-50">
                                    <td colspan="3" class="border px-4 py-3 text-right font-bold text-lg">GRAND TOTAL :</td>
                                    <td class="border px-4 py-3 text-right font-bold text-lg text-blue-700">
                                        Rp <span id="grandTotalDisplay">0</span>
                                    </td>
                                    <td class="border px-2 py-2 text-center">
                                        <button type="button" id="addRowBtn" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 font-bold">+ Baris</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="flex items-center justify-end gap-4 mt-8 border-t pt-4">
                            <a href="{{ route('purchase-orders.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-bold text-lg">Simpan & Buat PO</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            
            // Simpan HTML opsi material ke dalam variabel JS agar mudah diclone
            const materialOptions = `
                <option value="">-- Pilih Barang --</option>
                @foreach($materials as $material)
                    <option value="{{ $material->id }}" data-price="{{ $material->unit_price }}">
                        {{ $material->name }} ({{ $material->unit }})
                    </option>
                @endforeach
            `;

            // Fungsi untuk format angka ke Rupiah
            function formatRupiah(angka) {
                return parseFloat(angka).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
            }

            // Fungsi untuk menghitung ulang semua total
            function calculateTotal() {
                let grandTotal = 0;
                
                // Looping setiap baris
                $('.item-row').each(function() {
                    let qty = parseFloat($(this).find('.qty-input').val()) || 0;
                    let price = parseFloat($(this).find('.price-input').val()) || 0;
                    let subtotal = qty * price;
                    
                    // Tampilkan subtotal per baris
                    $(this).find('.subtotal-display').text(formatRupiah(subtotal));
                    
                    grandTotal += subtotal;
                });

                // Tampilkan Grand Total
                $('#grandTotalDisplay').text(formatRupiah(grandTotal));
            }

            // EVENT 1: Saat dropdown barang dipilih, otomatis tarik harga ke input harga
            $(document).on('change', '.material-select', function() {
                let selectedOption = $(this).find('option:selected');
                let price = selectedOption.data('price'); // Ambil dari attribute data-price
                
                // Cari input harga di baris yang sama (closest tr), lalu isi nilainya
                let tr = $(this).closest('tr');
                tr.find('.price-input').val(price);
                
                // Hitung ulang setelah harga terisi
                calculateTotal();
            });

            // EVENT 2: Saat qty atau harga diketik/diubah, hitung ulang
            $(document).on('input', '.qty-input, .price-input', function() {
                calculateTotal();
            });

            // EVENT 3: Tambah Baris Baru
            $('#addRowBtn').click(function() {
                let newRow = `
                    <tr class="item-row">
                        <td class="border px-2 py-2">
                            <select name="materials[]" class="w-full border-gray-300 rounded material-select" required>
                                ${materialOptions}
                            </select>
                        </td>
                        <td class="border px-2 py-2">
                            <input type="number" step="0.0001" name="quantities[]" class="w-full border-gray-300 rounded text-center qty-input" required min="0.01">
                        </td>
                        <td class="border px-2 py-2">
                            <input type="number" step="0.01" name="unit_prices[]" class="w-full border-gray-300 rounded text-right price-input" required min="0">
                        </td>
                        <td class="border px-4 py-2 text-right font-bold text-gray-700">
                            Rp <span class="subtotal-display">0</span>
                        </td>
                        <td class="border px-2 py-2 text-center">
                            <button type="button" class="text-red-500 hover:text-red-700 font-bold remove-row-btn">X</button>
                        </td>
                    </tr>
                `;
                
                $('#po-items-tbody').append(newRow);
                
                // Aktifkan tombol hapus di baris pertama jika baris > 1
                $('.remove-row-btn').prop('disabled', false);
            });

            // EVENT 4: Hapus Baris
            $(document).on('click', '.remove-row-btn', function() {
                // Cegah penghapusan jika hanya tersisa 1 baris
                if($('.item-row').length > 1) {
                    $(this).closest('tr').remove();
                    calculateTotal();
                }
                
                // Disable tombol hapus jika sisa 1 baris
                if($('.item-row').length === 1) {
                    $('.remove-row-btn').prop('disabled', true);
                }
            });

        });
    </script>
</x-app-layout>