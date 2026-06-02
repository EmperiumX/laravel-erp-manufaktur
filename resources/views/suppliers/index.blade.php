<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Supplier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Menampilkan Flash Message Success -->
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    
                    @error('file')
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ $message }}</span>
                        </div>
                    @enderror

                    <div class="mb-4 flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
                        <div class="flex gap-2 flex-wrap">
                            <a href="{{ route('suppliers.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                <i class="ri-add-line mr-1"></i> Tambah Supplier
                            </a>
                            <button type="submit" form="bulkDeleteForm" id="bulkDeleteBtn" class="inline-block px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                <i class="ri-delete-bin-line mr-1"></i> Hapus Terpilih
                            </button>
                            <a href="{{ route('suppliers.export') }}" class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                <i class="ri-file-excel-line mr-1"></i> Export Excel
                            </a>
                            <a href="{{ route('suppliers.template') }}" class="inline-block px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">
                                <i class="ri-download-2-line mr-1"></i> Download Template
                            </a>
                        </div>
                        <form action="{{ route('suppliers.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg bg-gray-50">
                            @csrf
                            <input type="file" name="file" required class="border border-gray-300 rounded p-1 text-sm bg-white" accept=".xlsx,.xls,.csv">
                            <input type="hidden" name="start_row" value="2">
                            <input type="hidden" name="start_column" value="1">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">
                                <i class="ri-upload-2-line mr-1"></i> Import
                            </button>
                        </form>
                    </div>

                    <div class="overflow-x-auto w-full">
                        <!-- Tabel Data -->
                        <table id="supplierTable" class="w-full table-auto border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-2 py-2 text-center w-12">
                                        <input type="checkbox" id="checkAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </th>
                                    <th class="border border-gray-300 px-4 py-2">No</th>
                                    <th class="border border-gray-300 px-4 py-2">Nama Supplier</th>
                                    <th class="border border-gray-300 px-4 py-2">Kontak Person</th>
                                    <th class="border border-gray-300 px-4 py-2">No. HP</th>
                                    <th class="border border-gray-300 px-4 py-2">Alamat</th>
                                    <th class="border border-gray-300 px-4 py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($suppliers as $index => $supplier)
                                <tr class="cursor-pointer hover:bg-blue-50 transition-colors">
                                    <td class="border border-gray-300 px-2 py-2 text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $supplier->id }}" class="supplier-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="border border-gray-300 px-4 py-2 font-bold">{{ $supplier->name }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $supplier->contact_person }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $supplier->phone_number }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $supplier->address }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <a href="{{ route('suppliers.edit', $supplier->id) }}" class="text-yellow-600 hover:underline">Edit</a> |
                                        <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data supplier ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline ml-2">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Form Hapus Masal (di luar tabel untuk menghindari nested form) -->
                    <form action="{{ route('suppliers.bulk-destroy') }}" method="POST" id="bulkDeleteForm" style="display:none;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data supplier yang terpilih?');">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan jQuery & DataTables CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <!-- Inisialisasi DataTables & Logika Checkbox -->
    <script>
        $(document).ready(function() {
            var table = $('#supplierTable').DataTable({
                "columnDefs": [
                    { "orderable": false, "targets": [0, 6] }
                ]
            });

            var lastChecked = null;

            // Handle checkAll checkbox
            $('#checkAll').on('change', function() {
                var rows = table.rows({ 'search': 'applied' }).nodes();
                $('input[type="checkbox"].supplier-checkbox', rows).prop('checked', this.checked);
                toggleBulkDeleteButton();
            });

            // Handle individual checkbox change
            $('#supplierTable tbody').on('change', 'input[type="checkbox"].supplier-checkbox', function() {
                lastChecked = this;
                var rowsCount = table.rows({ 'search': 'applied' }).nodes().length;
                var checkedCount = 0;
                var rows = table.rows({ 'search': 'applied' }).nodes();
                
                $('input[type="checkbox"].supplier-checkbox', rows).each(function() {
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
            $('#supplierTable tbody').on('click', 'tr', function(e) {
                if ($(e.target).is('a, button, input, select, textarea, i') || $(e.target).closest('a, button, input, select').length) {
                    return;
                }

                var checkbox = $(this).find('input[type="checkbox"].supplier-checkbox');
                if (!checkbox.length) return;

                var isChecked = checkbox.prop('checked');
                
                if (e.shiftKey && lastChecked) {
                    var rows = table.rows({ 'search': 'applied' }).nodes();
                    var checkboxes = $('input[type="checkbox"].supplier-checkbox', rows);
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

            // Fungsi untuk mengaktifkan/menonaktifkan tombol hapus terpilih
            function toggleBulkDeleteButton() {
                var checkedCount = 0;
                var rows = table.rows().nodes();
                $('input[type="checkbox"].supplier-checkbox', rows).each(function() {
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

                // Bersihkan input hidden ids[] lama agar tidak duplikat
                $(form).find('input[name="ids[]"]').remove();

                $('input[type="checkbox"].supplier-checkbox', rows).each(function() {
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