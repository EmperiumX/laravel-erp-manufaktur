<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Master Produk (Barang Jadi)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
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

                    <div class="mb-4 flex gap-2">
                        <a href="{{ route('products.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            + Tambah Produk Baru
                        </a>
                        <button type="submit" form="bulkDeleteForm" id="bulkDeleteBtn" class="inline-block px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            <i class="ri-delete-bin-line mr-1"></i> Hapus Terpilih
                        </button>
                    </div>

                    <div class="overflow-x-auto w-full">
                        <table id="productTable" class="w-full table-auto border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-2 py-2 text-center w-12">
                                        <input type="checkbox" id="checkAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </th>
                                    <th class="border px-4 py-2">SKU</th>
                                    <th class="border px-4 py-2">Nama Produk</th>
                                    <th class="border px-4 py-2">Berat & Kemasan</th>
                                    <th class="border px-4 py-2">Harga Jual</th>
                                    <th class="border px-4 py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                <tr class="cursor-pointer hover:bg-blue-50 transition-colors">
                                    <td class="border border-gray-300 px-2 py-2 text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $product->id }}" class="product-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="border px-4 py-2 text-center">{{ $product->sku ?? '-' }}</td>
                                    <td class="border px-4 py-2 font-bold">{{ $product->name }}</td>
                                    <td class="border px-4 py-2 text-center">{{ $product->weight }} {{ $product->weight_unit }} / {{ $product->packaging }}</td>
                                    <td class="border px-4 py-2 text-right text-green-600 font-bold">
                                        @php
                                            $priceItem = $product->prices->first();
                                        @endphp
                                        {{ $priceItem ? 'Rp ' . number_format($priceItem->price, 0, ',', '.') : 'Belum di-set' }}
                                    </td>
                                    <td class="border px-4 py-2 text-center">
                                        <!-- Tombol Detail & Resep -->
                                        <a href="{{ route('products.show', $product->id) }}" class="text-blue-600 hover:underline font-bold">Detail & Resep</a> | 
                                        
                                        <a href="{{ route('products.edit', $product->id) }}" class="text-yellow-600 hover:underline">Edit</a> | 
                                        
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus produk ini beserta semua harga jualnya?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline ml-1">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Form Hapus Masal (di luar tabel untuk menghindari nested form) -->
                    <form action="{{ route('products.bulk-destroy') }}" method="POST" id="bulkDeleteForm" style="display:none;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data produk yang terpilih beserta semua harga jualnya?');">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#productTable').DataTable({
                "columnDefs": [
                    { "orderable": false, "targets": [0, 5] }
                ]
            });

            var lastChecked = null;

            // Handle checkAll checkbox
            $('#checkAll').on('change', function() {
                var rows = table.rows({ 'search': 'applied' }).nodes();
                $('input[type="checkbox"].product-checkbox', rows).prop('checked', this.checked);
                toggleBulkDeleteButton();
            });

            // Handle individual checkbox change
            $('#productTable tbody').on('change', 'input[type="checkbox"].product-checkbox', function() {
                lastChecked = this;
                var rowsCount = table.rows({ 'search': 'applied' }).nodes().length;
                var checkedCount = 0;
                var rows = table.rows({ 'search': 'applied' }).nodes();
                
                $('input[type="checkbox"].product-checkbox', rows).each(function() {
                    if (this.checked) {
                        checkedCount++;
                    }
                });

                var el = $('#checkAll').get(0);
                if (checkedCount === 0) {
                    el.checked = false;
                    el.indeterminate = false;
                } else if (checkedCount === rowsCount) {
                    el.checked = true;
                    el.indeterminate = false;
                } else {
                    el.checked = false;
                    el.indeterminate = true;
                }
                toggleBulkDeleteButton();
            });

            // Handle row click (Ctrl + Click or standard Click to select/deselect, Shift + Click for range)
            $('#productTable tbody').on('click', 'tr', function(e) {
                if ($(e.target).is('a, button, input, select, textarea, i') || $(e.target).closest('a, button, input, select').length) {
                    return;
                }

                var checkbox = $(this).find('input[type="checkbox"].product-checkbox');
                if (!checkbox.length) return;

                var isChecked = checkbox.prop('checked');
                
                if (e.shiftKey && lastChecked) {
                    var rows = table.rows({ 'search': 'applied' }).nodes();
                    var checkboxes = $('input[type="checkbox"].product-checkbox', rows);
                    var start = checkboxes.index(checkbox);
                    var end = checkboxes.index(lastChecked);
                    
                    checkboxes.slice(Math.min(start, end), Math.max(start, end) + 1).prop('checked', true).trigger('change');
                } else if (e.ctrlKey || e.metaKey) {
                    checkbox.prop('checked', !isChecked).trigger('change');
                    lastChecked = checkbox[0];
                } else {
                    checkbox.prop('checked', !isChecked).trigger('change');
                    lastChecked = checkbox[0];
                }
                
                window.getSelection().removeAllRanges();
            });

            function toggleBulkDeleteButton() {
                var checkedCount = 0;
                var rows = table.rows().nodes();
                $('input[type="checkbox"].product-checkbox', rows).each(function() {
                    if (this.checked) {
                        checkedCount++;
                    }
                });

                if (checkedCount > 0) {
                    $('#bulkDeleteBtn').prop('disabled', false);
                } else {
                    $('#bulkDeleteBtn').prop('disabled', true);
                }
            }

            // Intercept form submit untuk menambahkan checkbox yang tercentang di halaman DataTable lain
            $('#bulkDeleteForm').on('submit', function(e) {
                var form = this;
                var rows = table.rows().nodes();

                $(form).find('input[name="ids[]"]').remove();

                $('input[type="checkbox"].product-checkbox', rows).each(function() {
                    if (this.checked) {
                        $(form).append(
                            $('<input>')
                                .attr('type', 'hidden')
                                .attr('name', 'ids[]')
                                .val(this.value)
                        );
                    }
                });
            });
        });
    </script>
</x-app-layout>