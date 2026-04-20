<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mesin Kasir - Penjualan Langsung') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <form action="{{ route('direct-sales.store') }}" method="POST" id="salesForm">
                        @csrf

                        <!-- HEADER FORM -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 bg-green-50 p-4 rounded-lg border border-green-200">

                            <!-- IDENTITAS PEMBELI -->
                            <div class="bg-white p-4 rounded border border-green-100 shadow-sm">
                                <h4 class="font-bold text-sm text-gray-600 mb-3 uppercase border-b pb-1">
                                    Identitas Pembeli (Isi Salah Satu)
                                </h4>

                                <div class="mb-3">
                                    <label class="block text-xs font-bold mb-1">Toko Terdaftar</label>

                                    <select name="store_id" class="w-full border-gray-300 rounded-md text-sm">
                                        <option value="">-- Pilih Toko --</option>

                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}">
                                                {{ $store->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold mb-1">Nama Pembeli Umum</label>

                                    <input type="text"
                                           name="customer_name"
                                           placeholder="Isi jika bukan toko..."
                                           class="w-full border-gray-300 rounded-md text-sm">
                                </div>
                            </div>

                            <!-- INFORMASI TRANSAKSI -->
                            <div class="bg-white p-4 rounded border border-green-100 shadow-sm">

                                <h4 class="font-bold text-sm text-gray-600 mb-3 uppercase border-b pb-1">
                                    Informasi Transaksi
                                </h4>

                                <div class="mb-3">
                                    <label class="block text-xs font-bold mb-1">
                                        Tanggal Transaksi
                                    </label>

                                    <input type="date"
                                           name="sale_date"
                                           value="{{ date('Y-m-d') }}"
                                           class="w-full border-gray-300 rounded-md text-sm"
                                           required>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold mb-1">
                                        Catatan
                                    </label>

                                    <input type="text"
                                           name="notes"
                                           placeholder="Opsional..."
                                           class="w-full border-gray-300 rounded-md text-sm">
                                </div>

                            </div>

                        </div>

                        <!-- TABEL KASIR -->
                        <h3 class="font-bold text-lg mb-4 text-green-700 border-b pb-2">
                            Daftar Belanja
                        </h3>

                        <div class="overflow-x-auto w-full">

                            <table class="w-full table-auto border-collapse border border-gray-300 text-sm mb-4"
                                   id="itemTable">

                                <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2 w-2/5">Produk</th>
                                    <th class="border px-4 py-2 w-1/6">Qty</th>
                                    <th class="border px-4 py-2 w-1/6">Harga (Rp)</th>
                                    <th class="border px-4 py-2 w-1/6 text-right">Subtotal</th>
                                    <th class="border px-2 py-2 text-center w-12">X</th>
                                </tr>
                                </thead>

                                <tbody id="sales-items-tbody">

                                <tr class="sales-row">

                                    <!-- PRODUK -->
                                    <td class="border px-2 py-2">

                                        <select name="products[]"
                                                class="w-full border-gray-300 rounded-md product-select text-sm"
                                                required>

                                            <option value="">-- Pilih Produk --</option>

                                            @foreach($products as $product)

                                                @php
                                                    $defaultPrice = $product->prices->first()
                                                    ? $product->prices->first()->price
                                                    : 0;
                                                @endphp

                                                <option value="{{ $product->id }}"
                                                        data-price="{{ $defaultPrice }}">

                                                    {{ $product->name }}

                                                </option>

                                            @endforeach

                                        </select>

                                    </td>

                                    <!-- QTY -->
                                    <td class="border px-2 py-2">

                                        <input type="number"
                                               name="quantities[]"
                                               class="w-full border-gray-300 rounded-md text-center qty-input text-sm"
                                               min="1"
                                               required>

                                    </td>

                                    <!-- HARGA -->
                                    <td class="border px-2 py-2">

                                        <input type="number"
                                               name="unit_prices[]"
                                               class="w-full border-gray-300 rounded-md price-input text-sm"
                                               step="0.01"
                                               required>

                                    </td>

                                    <!-- SUBTOTAL -->
                                    <td class="border px-2 py-2 text-right subtotal-cell">
                                        Rp 0
                                    </td>

                                    <!-- HAPUS -->
                                    <td class="border px-2 py-2 text-center">
                                        <button type="button"
                                                class="text-red-500 font-bold remove-row-btn"
                                                disabled>
                                            X
                                        </button>
                                    </td>

                                </tr>

                                </tbody>

                                <!-- FOOTER TOTAL -->
                                <tfoot>

                                <tr class="bg-green-50">

                                    <td colspan="3"
                                        class="border px-4 py-3 text-right font-bold">

                                        Grand Total

                                    </td>

                                    <td class="border px-4 py-3 text-right font-bold"
                                        id="grandTotal">

                                        Rp 0

                                    </td>

                                    <td class="border px-2 py-2 text-center">

                                        <button type="button"
                                                id="addRowBtn"
                                                class="bg-green-600 text-white px-3 py-1 rounded text-sm">

                                            + Baris

                                        </button>

                                    </td>

                                </tr>

                                </tfoot>

                            </table>

                        </div>

                        <!-- SUBMIT -->
                        <div class="flex justify-end gap-4 mt-6 border-t pt-4">

                            <a href="{{ route('direct-sales.index') }}"
                               class="bg-gray-500 text-white px-4 py-2 rounded">

                                Batal

                            </a>

                            <button type="submit"
                                    class="bg-green-600 text-white px-6 py-2 rounded font-bold">

                                Simpan Transaksi

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

    function formatRupiah(number){

        return 'Rp ' + Number(number).toLocaleString('id-ID');

    }


    function calculateTotal(){

        let grandTotal = 0;

        $('.sales-row').each(function(){

            let qty = parseFloat($(this).find('.qty-input').val()) || 0;

            let price = parseFloat($(this).find('.price-input').val()) || 0;

            let subtotal = qty * price;

            $(this).find('.subtotal-cell').text(formatRupiah(subtotal));

            grandTotal += subtotal;

        });

        $('#grandTotal').text(formatRupiah(grandTotal));

    }


    // AUTO HARGA PRODUK
    $(document).on('change','.product-select',function(){

        let price = $(this).find(':selected').data('price') || 0;

        let row = $(this).closest('tr');

        row.find('.price-input').val(price);

        calculateTotal();

    });


    // HITUNG TOTAL
    $(document).on('input','.qty-input, .price-input',function(){

        calculateTotal();

    });


    // TAMBAH BARIS
    $('#addRowBtn').click(function(){

        let newRow = $('.sales-row:first').clone();

        newRow.find('select').val('');

        newRow.find('input').val('');

        newRow.find('.subtotal-cell').text('Rp 0');

        newRow.find('.remove-row-btn').prop('disabled',false);

        $('#sales-items-tbody').append(newRow);

    });


    // HAPUS BARIS
    $(document).on('click','.remove-row-btn',function(){

        if($('.sales-row').length > 1){

            $(this).closest('tr').remove();

            calculateTotal();

        }

    });

});

</script>

</x-app-layout>