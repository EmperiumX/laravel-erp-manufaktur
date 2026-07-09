<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4">
        <h2 class="text-2xl font-bold mb-6">Buat Surat Jalan</h2>

        <form action="{{ route('consignments.store') }}" method="POST">
            @csrf

            <!-- HEADER -->
            <div class="bg-white shadow rounded-lg p-6 mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">

                <!-- Pilih Toko -->
                <div>
                    <label class="block text-sm font-medium mb-1">Pilih Toko <span class="text-red-500">*</span></label>
                    <select id="store_id" name="store_id" required
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                        <option value="">-- Pilih Toko --</option>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}" data-category="{{ $store->category }}">
                                {{ $store->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Pengiriman <span class="text-red-500">*</span></label>
                    <input type="date" name="shipment_date" required
                        value="{{ date('Y-m-d') }}"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-sm font-medium mb-1">Catatan</label>
                    <input type="text" name="notes"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                        placeholder="Catatan tambahan...">
                </div>
            </div>

            <!-- ITEM TABLE -->
            <div class="bg-white shadow rounded-lg p-6">

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-4 py-2 text-left">Produk</th>
                                <th class="border px-4 py-2 text-left">Kuantitas</th>
                                <th class="border px-4 py-2 text-left">Harga Jual</th>
                                <th class="border px-4 py-2 text-left">Subtotal</th>
                                <th class="border px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="itemTable">
                            <tr class="item-row">
                                <!-- Produk -->
                                <td class="border px-4 py-2">
                                    <select name="products[]" required
                                        class="product-select w-full border rounded px-2 py-1">
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                data-prices='@json($product->prices->pluck("price","category"))'>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- Qty -->
                                <td class="border px-4 py-2">
                                    <input type="number" name="quantities[]" min="1" required
                                        class="qty w-full border rounded px-2 py-1" value="1">
                                </td>

                                <!-- Harga -->
                                <td class="border px-4 py-2">
                                    <input type="number" name="unit_prices[]" min="0" step="any" required
                                        class="price w-full border rounded px-2 py-1">
                                </td>

                                <!-- Subtotal -->
                                <td class="border px-4 py-2 subtotal">
                                    Rp 0
                                </td>

                                <!-- Hapus / Edit -->
                                <td class="border px-4 py-2 text-center">
                                    <button type="button"
                                        class="edit-row bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded mr-1">
                                        Edit
                                    </button>
                                    <button type="button"
                                        class="remove-row bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        </tbody>

                        <template id="row-template">
                            <tr class="item-row">
                                <!-- Produk -->
                                <td class="border px-4 py-2">
                                    <select name="products[]" required
                                        class="product-select w-full border rounded px-2 py-1">
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                data-prices='@json($product->prices->pluck("price","category"))'>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- Qty -->
                                <td class="border px-4 py-2">
                                    <input type="number" name="quantities[]" min="1" required
                                        class="qty w-full border rounded px-2 py-1" value="1">
                                </td>

                                <!-- Harga -->
                                <td class="border px-4 py-2">
                                    <input type="number" name="unit_prices[]" min="0" step="any" required
                                        class="price w-full border rounded px-2 py-1">
                                </td>

                                <!-- Subtotal -->
                                <td class="border px-4 py-2 subtotal">
                                    Rp 0
                                </td>

                                <!-- Hapus / Edit -->
                                <td class="border px-4 py-2 text-center">
                                    <button type="button"
                                        class="edit-row bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded mr-1">
                                        Edit
                                    </button>
                                    <button type="button"
                                        class="remove-row bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <tfoot>
                            <tr class="bg-gray-50">
                                <td colspan="3" class="text-right font-semibold px-4 py-2">
                                    Grand Total
                                </td>
                                <td class="px-4 py-2 font-bold" id="grandTotal">
                                    Rp 0
                                </td>
                                <td class="px-4 py-2">
                                    <button type="button"
                                        id="addRow"
                                        class="bg-blue-500 text-white px-3 py-1 rounded">
                                        + Tambah Baris
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Submit -->
                <div class="mt-6">
                    <button type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg">
                        Simpan Surat Jalan
                    </button>
                </div>

            </div>
        </form>
    </div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>

let activeCategory = null;

function formatRupiah(number){
    return 'Rp ' + number.toLocaleString('id-ID');
}

function calculateTotal(){

    let grandTotal = 0;

    $('#itemTable tr').each(function(){

        let qty = parseFloat($(this).find('.qty').val()) || 0;
        let price = parseFloat($(this).find('.price').val()) || 0;

        let subtotal = qty * price;

        $(this).find('.subtotal').text(formatRupiah(subtotal));

        grandTotal += subtotal;

    });

    $('#grandTotal').text(formatRupiah(grandTotal));

}

function updatePriceForRow(row){

    let productOption = row.find('.product-select option:selected');

    if(productOption.val() === '') return;

    if(!activeCategory){
        alert('Silakan pilih toko terlebih dahulu');
        row.find('.product-select').val('');
        return;
    }

    let prices = productOption.data('prices');

    if(prices && prices[activeCategory]){
        row.find('.price').val(prices[activeCategory]);
    }else{
        row.find('.price').val(0);
    }

    calculateTotal();

}

$('#store_id').change(function(){

    activeCategory = $(this).find(':selected').data('category');

    $('#itemTable tr').each(function(){
        updatePriceForRow($(this));
    });

});

$(document).on('change','.product-select',function(){

    let row = $(this).closest('tr');

    updatePriceForRow(row);

});

$(document).on('input','.qty, .price',function(){
    calculateTotal();
});

$('#addRow').click(function(){

    let newRow = $($('#row-template').html());

    $('#itemTable').append(newRow);

});

$(document).on('click', '.edit-row', function() {
    var select = $(this).closest('tr').find('select')[0];
    if (select && select.tomselect) {
        select.tomselect.focus();
        select.tomselect.open();
    } else if (select) {
        select.focus();
    }
});

$(document).on('click','.remove-row',function(){

    if($('#itemTable tr').length > 1){
        $(this).closest('tr').remove();
        calculateTotal();
    }

});

</script>

</x-app-layout>