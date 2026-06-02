<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $type === 'sales' ? 'Buat Invoice Penjualan (Piutang)' : 'Buat Invoice Pembelian (Hutang)' }}
            </h2>
            <a href="{{ route('invoices.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">{{ session('error') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">

                        @if($purchaseOrder)
                            <input type="hidden" name="purchase_order_id" value="{{ $purchaseOrder->id }}">
                        @endif
                        @if($consignment)
                            <input type="hidden" name="consignment_shipment_id" value="{{ $consignment->id }}">
                        @endif
                        @if($directSale)
                            <input type="hidden" name="direct_sale_id" value="{{ $directSale->id }}">
                        @endif

                        <!-- Info Header -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 bg-gray-50 p-4 rounded-lg border">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    {{ $type === 'sales' ? 'Pilih Toko/Mitra' : 'Pilih Supplier' }} <span class="text-red-500">*</span>
                                </label>
                                @if($type === 'sales')
                                    <select name="store_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                        <option value="">-- Pilih Toko/Mitra --</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}" {{ ($consignment && $consignment->store_id == $store->id) || ($directSale && $directSale->store_id == $store->id) ? 'selected' : '' }}>
                                                {{ $store->name }} ({{ $store->category }})
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <select name="supplier_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ ($purchaseOrder && $purchaseOrder->supplier_id == $supplier->id) ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Invoice <span class="text-red-500">*</span></label>
                                <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Jatuh Tempo <span class="text-red-500">*</span></label>
                                <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">PPN / Pajak (Rp)</label>
                                <input type="number" step="0.01" name="tax_amount" value="0" class="w-full border-gray-300 rounded-md shadow-sm" id="taxInput">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Diskon (Rp)</label>
                                <input type="number" step="0.01" name="discount_amount" value="0" class="w-full border-gray-300 rounded-md shadow-sm" id="discountInput">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Catatan</label>
                                <input type="text" name="notes" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Opsional">
                            </div>
                        </div>

                        <!-- Item Details -->
                        <h3 class="font-bold text-lg mb-4 text-blue-700">Detail Item</h3>
                        <table class="w-full table-auto border-collapse border border-gray-300 text-sm mb-4" id="itemTable">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2 w-2/5">Deskripsi</th>
                                    <th class="border px-4 py-2 w-1/6">Qty</th>
                                    <th class="border px-4 py-2 w-1/6">Satuan</th>
                                    <th class="border px-4 py-2 w-1/5">Harga Satuan (Rp)</th>
                                    <th class="border px-4 py-2 w-1/5">Subtotal</th>
                                    <th class="border px-4 py-2">Hapus</th>
                                </tr>
                            </thead>
                            <tbody id="inv-items-tbody">
                                @if($purchaseOrder)
                                    @foreach($purchaseOrder->items as $idx => $item)
                                    <tr class="item-row">
                                        <td class="border px-2 py-2">
                                            <input type="text" name="items[{{ $idx }}][description]" value="{{ $item->material->name }}" class="w-full border-gray-300 rounded" required>
                                            <input type="hidden" name="items[{{ $idx }}][material_id]" value="{{ $item->material_id }}">
                                        </td>
                                        <td class="border px-2 py-2"><input type="number" step="1" name="items[{{ $idx }}][quantity]" value="{{ $item->quantity }}" class="w-full border-gray-300 rounded text-center qty-input" required min="1"></td>
                                        <td class="border px-2 py-2"><input type="text" name="items[{{ $idx }}][unit]" value="{{ $item->material->unit ?? 'pcs' }}" class="w-full border-gray-300 rounded text-center"></td>
                                        <td class="border px-2 py-2"><input type="number" step="0.01" name="items[{{ $idx }}][unit_price]" value="{{ $item->unit_price }}" class="w-full border-gray-300 rounded text-right price-input" required min="0"></td>
                                        <td class="border px-4 py-2 text-right font-bold">Rp <span class="subtotal-display">{{ number_format($item->subtotal, 0, ',', '.') }}</span></td>
                                        <td class="border px-2 py-2 text-center"><button type="button" class="text-red-500 hover:text-red-700 font-bold remove-row-btn">X</button></td>
                                    </tr>
                                    @endforeach
                                @elseif($consignment)
                                    @foreach($consignment->items as $idx => $item)
                                    <tr class="item-row">
                                        <td class="border px-2 py-2">
                                            <input type="text" name="items[{{ $idx }}][description]" value="{{ $item->product->name }}" class="w-full border-gray-300 rounded" required>
                                            <input type="hidden" name="items[{{ $idx }}][product_id]" value="{{ $item->product_id }}">
                                        </td>
                                        <td class="border px-2 py-2"><input type="number" step="1" name="items[{{ $idx }}][quantity]" value="{{ $item->quantity }}" class="w-full border-gray-300 rounded text-center qty-input" required min="1"></td>
                                        <td class="border px-2 py-2"><input type="text" name="items[{{ $idx }}][unit]" value="pcs" class="w-full border-gray-300 rounded text-center"></td>
                                        <td class="border px-2 py-2"><input type="number" step="0.01" name="items[{{ $idx }}][unit_price]" value="{{ $item->unit_price }}" class="w-full border-gray-300 rounded text-right price-input" required min="0"></td>
                                        <td class="border px-4 py-2 text-right font-bold">Rp <span class="subtotal-display">{{ number_format($item->subtotal, 0, ',', '.') }}</span></td>
                                        <td class="border px-2 py-2 text-center"><button type="button" class="text-red-500 hover:text-red-700 font-bold remove-row-btn">X</button></td>
                                    </tr>
                                    @endforeach
                                @else
                                    <!-- Default empty row -->
                                    <tr class="item-row">
                                        <td class="border px-2 py-2"><input type="text" name="items[0][description]" class="w-full border-gray-300 rounded" required placeholder="Nama barang/jasa"></td>
                                        <td class="border px-2 py-2"><input type="number" step="1" name="items[0][quantity]" class="w-full border-gray-300 rounded text-center qty-input" required min="1"></td>
                                        <td class="border px-2 py-2"><input type="text" name="items[0][unit]" value="pcs" class="w-full border-gray-300 rounded text-center"></td>
                                        <td class="border px-2 py-2"><input type="number" step="0.01" name="items[0][unit_price]" class="w-full border-gray-300 rounded text-right price-input" required min="0"></td>
                                        <td class="border px-4 py-2 text-right font-bold">Rp <span class="subtotal-display">0</span></td>
                                        <td class="border px-2 py-2 text-center"><button type="button" class="text-red-500 hover:text-red-700 font-bold remove-row-btn" disabled>X</button></td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr class="bg-blue-50">
                                    <td colspan="4" class="border px-4 py-3 text-right font-bold text-lg">GRAND TOTAL :</td>
                                    <td class="border px-4 py-3 text-right font-bold text-lg text-blue-700">Rp <span id="grandTotalDisplay">0</span></td>
                                    <td class="border px-2 py-2 text-center">
                                        <button type="button" id="addRowBtn" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 font-bold">+ Baris</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="flex items-center justify-end gap-4 mt-8 border-t pt-4">
                            <a href="{{ route('invoices.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-bold text-lg">Simpan Invoice</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            let rowIndex = {{ ($purchaseOrder ? count($purchaseOrder->items) : ($consignment ? count($consignment->items) : 1)) }};

            function formatRupiah(angka) {
                return parseFloat(angka).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
            }

            function calculateTotal() {
                let subtotal = 0;
                $('.item-row').each(function() {
                    let qty = parseFloat($(this).find('.qty-input').val()) || 0;
                    let price = parseFloat($(this).find('.price-input').val()) || 0;
                    let itemSubtotal = qty * price;
                    $(this).find('.subtotal-display').text(formatRupiah(itemSubtotal));
                    subtotal += itemSubtotal;
                });
                let tax = parseFloat($('#taxInput').val()) || 0;
                let discount = parseFloat($('#discountInput').val()) || 0;
                let grandTotal = subtotal + tax - discount;
                $('#grandTotalDisplay').text(formatRupiah(grandTotal));
            }

            $(document).on('input', '.qty-input, .price-input, #taxInput, #discountInput', function() { calculateTotal(); });

            $('#addRowBtn').click(function() {
                let newRow = `<tr class="item-row">
                    <td class="border px-2 py-2"><input type="text" name="items[${rowIndex}][description]" class="w-full border-gray-300 rounded" required placeholder="Nama barang/jasa"></td>
                    <td class="border px-2 py-2"><input type="number" step="1" name="items[${rowIndex}][quantity]" class="w-full border-gray-300 rounded text-center qty-input" required min="1"></td>
                    <td class="border px-2 py-2"><input type="text" name="items[${rowIndex}][unit]" value="pcs" class="w-full border-gray-300 rounded text-center"></td>
                    <td class="border px-2 py-2"><input type="number" step="0.01" name="items[${rowIndex}][unit_price]" class="w-full border-gray-300 rounded text-right price-input" required min="0"></td>
                    <td class="border px-4 py-2 text-right font-bold">Rp <span class="subtotal-display">0</span></td>
                    <td class="border px-2 py-2 text-center"><button type="button" class="text-red-500 hover:text-red-700 font-bold remove-row-btn">X</button></td>
                </tr>`;
                $('#inv-items-tbody').append(newRow);
                rowIndex++;
                $('.remove-row-btn').prop('disabled', false);
            });

            $(document).on('click', '.remove-row-btn', function() {
                if($('.item-row').length > 1) {
                    $(this).closest('tr').remove();
                    calculateTotal();
                }
                if($('.item-row').length === 1) { $('.remove-row-btn').prop('disabled', true); }
            });

            calculateTotal();
        });
    </script>
</x-app-layout>
