<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Form Input Retur Barang') }}
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
                    
                    <form action="{{ route('returns.store') }}" method="POST" id="returnForm">
                        @csrf
                        
                        <!-- HEADER -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 bg-red-50 p-4 rounded-lg border border-red-200">
                            
                            <!-- TOKO -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Toko Pengirim (Retur) <span class="text-red-500">*</span>
                                </label>

                                <select name="store_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">-- Pilih Toko --</option>

                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}">
                                            {{ $store->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <!-- TANGGAL -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Tanggal Retur <span class="text-red-500">*</span>
                                </label>

                                <input type="date"
                                    name="return_date"
                                    value="{{ date('Y-m-d') }}"
                                    class="w-full border-gray-300 rounded-md shadow-sm"
                                    required>
                            </div>

                            <!-- CATATAN -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Alasan Retur / Catatan
                                </label>

                                <input type="text"
                                    name="notes"
                                    placeholder="Contoh: Barang rusak / expired"
                                    class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                        </div>

                        <!-- TABEL RETUR -->
                        <h3 class="font-bold text-lg mb-4 text-red-700 border-b pb-2">
                            Daftar Barang yang Dikembalikan
                        </h3>
                        
                        <div class="overflow-x-auto w-full">

                            <table class="w-full table-auto border-collapse border border-gray-300 text-sm mb-4" id="itemTable">
                                
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border px-4 py-2 w-2/5">Produk Jadi</th>
                                        <th class="border px-4 py-2 w-1/5">Kuantitas (Pcs/Pack)</th>
                                        <th class="border px-4 py-2 w-1/5">Kondisi Barang</th>
                                        <th class="border px-4 py-2 text-center w-1/5">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody id="return-items-tbody">

                                    <tr class="return-row">

                                        <!-- PRODUK -->
                                        <td class="border px-2 py-2">
                                            <select name="products[]" class="w-full border-gray-300 rounded-md" required>

                                                <option value="">-- Pilih Produk --</option>

                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}">
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </td>

                                        <!-- QTY -->
                                        <td class="border px-2 py-2">
                                            <input type="number"
                                                name="quantities[]"
                                                class="w-full border-gray-300 rounded-md text-center"
                                                required
                                                min="1"
                                                placeholder="Qty">
                                        </td>

                                        <!-- KONDISI -->
                                        <td class="border px-2 py-2">
                                            <select name="conditions[]" class="w-full border-gray-300 rounded-md" required>
                                                <option value="Bagus">
                                                    Bagus (Bisa dijual lagi)
                                                </option>

                                                <option value="Rusak/Basi">
                                                    Rusak/Basi (Harus dibuang)
                                                </option>
                                            </select>
                                        </td>

                                        <!-- HAPUS -->
                                        <td class="border px-2 py-2 text-center">
                                            <button type="button" class="edit-row text-blue-600 hover:text-blue-800 font-bold mr-2">Edit</button>
                                            <button type="button" class="text-red-500 hover:text-red-700 font-bold remove-row-btn" disabled>Hapus</button>
                                        </td>

                                    </tr>

                                </tbody>

                                <tfoot>

                                    <tr class="bg-red-50">
                                        <td colspan="3" class="border px-4 py-3 text-right"></td>

                                        <td class="border px-2 py-2 text-center">
                                            <button type="button"
                                                id="addRowBtn"
                                                class="bg-green-500 text-white px-3 py-1 rounded-md text-sm hover:bg-green-600 font-bold">
                                                + Baris
                                            </button>
                                        </td>
                                    </tr>

                                </tfoot>

                            </table>

                        </div>

                        <!-- BUTTON -->
                        <div class="flex items-center justify-end gap-4 mt-8 border-t pt-4">

                            <a href="{{ route('returns.index') }}"
                                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                                Batal
                            </a>

                            <button type="submit"
                                class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 font-bold text-lg"
                                onclick="return confirm('Proses ini akan MENAMBAH kembali stok barang jadi di gudang. Lanjutkan?');">

                                Simpan Data Retur

                            </button>

                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>

        $(document).ready(function(){

            const productOptions = `
                <option value="">-- Pilih Produk --</option>

                @foreach($products as $product)
                    <option value="{{ $product->id }}">
                        {{ $product->name }}
                    </option>
                @endforeach
            `;

            // TAMBAH BARIS
            $('#addRowBtn').click(function(){

                let newRow = `
                    <tr class="return-row">

                        <td class="border px-2 py-2">
                            <select name="products[]" class="w-full border-gray-300 rounded-md" required>
                                ${productOptions}
                            </select>
                        </td>

                        <td class="border px-2 py-2">
                            <input type="number"
                                name="quantities[]"
                                class="w-full border-gray-300 rounded-md text-center"
                                required
                                min="1"
                                placeholder="Qty">
                        </td>

                        <td class="border px-2 py-2">
                            <select name="conditions[]" class="w-full border-gray-300 rounded-md" required>
                                <option value="Bagus">Bagus (Bisa dijual lagi)</option>
                                <option value="Rusak/Basi">Rusak/Basi (Harus dibuang)</option>
                            </select>
                        </td>

                        <td class="border px-2 py-2 text-center">
                            <button type="button" class="edit-row text-blue-600 hover:text-blue-800 font-bold mr-2">Edit</button>
                            <button type="button" class="text-red-500 hover:text-red-700 font-bold remove-row-btn">Hapus</button>
                        </td>

                    </tr>
                `;

                $('#return-items-tbody').append(newRow);

                updateRemoveButtons();

            });

            // EDIT BARIS
            $(document).on('click', '.edit-row', function() {
                var select = $(this).closest('tr').find('select')[0];
                if (select && select.tomselect) {
                    select.tomselect.focus();
                    select.tomselect.open();
                } else if (select) {
                    select.focus();
                }
            });

            // HAPUS BARIS
            $(document).on('click','.remove-row-btn',function(){

                $(this).closest('tr').remove();

                updateRemoveButtons();

            });

            // AKTIFKAN / NONAKTIFKAN TOMBOL HAPUS
            function updateRemoveButtons(){

                let rows = $('.return-row').length;

                if(rows === 1){
                    $('.remove-row-btn').prop('disabled', true);
                }else{
                    $('.remove-row-btn').prop('disabled', false);
                }

            }

        });

    </script>

</x-app-layout>