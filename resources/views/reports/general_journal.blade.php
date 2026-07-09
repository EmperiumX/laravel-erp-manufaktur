<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Jurnal Umum & Mitra') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Mutasi transaksi terintegrasi seluruh unit bisnis</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('reports.create-journal') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm font-bold shadow transition flex items-center gap-2">
                    <i class="ri-add-line"></i> Jurnal Manual (Voucher)
                </a>
                <button onclick="window.print()" class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 text-sm font-bold shadow transition flex items-center gap-2">
                    <i class="ri-printer-line"></i> Cetak Laporan
                </button>
                <a href="{{ route('reports.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm transition">Kembali</a>
            </div>
        </div>
    </x-slot>

    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            /* Hide everything by default */
            body > * {
                display: none !important;
            }
            /* Show print area only */
            #printArea, #printArea * {
                display: block !important;
            }
            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
            /* Adjust table for printing */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            th, td {
                border: 1px solid #ddd !important;
                padding: 8px !important;
            }
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filter Periode & Entitas -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6 no-print">
                <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <i class="ri-filter-3-line text-lg text-indigo-500"></i>
                    Filter Pencarian & Jurnal per Mitra/Supplier
                </h3>
                <form method="GET" action="{{ route('reports.general-journal') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Toko / Mitra</label>
                        <select name="store_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">-- Semua Toko/Mitra --</option>
                            @foreach($stores as $st)
                                <option value="{{ $st->id }}" {{ $storeId == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Supplier</label>
                        <select name="supplier_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">-- Semua Supplier --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ $supplierId == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-4 flex justify-end gap-3 mt-2">
                        <a href="{{ route('reports.general-journal') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm font-bold">
                            Reset Filter
                        </a>
                        <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded hover:bg-indigo-700 text-sm font-bold">
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Ringkasan Jurnal -->
            <div id="printArea" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Print-only Header -->
                    <div class="hidden print:block text-center mb-6">
                        <h1 class="text-2xl font-bold uppercase">CV New Citra Indonesia</h1>
                        <h2 class="text-xl font-bold uppercase mt-1">Laporan Jurnal Umum & Transaksi</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </p>
                        @if($storeId || $supplierId)
                            <p class="text-xs font-semibold text-indigo-700 mt-1">
                                Filter: 
                                @if($storeId) Toko/Mitra: {{ $stores->find($storeId)->name ?? '-' }} @endif
                                @if($supplierId) Supplier: {{ $suppliers->find($supplierId)->name ?? '-' }} @endif
                            </p>
                        @endif
                        <hr class="my-4 border-t-2 border-gray-800">
                    </div>

                    <div class="overflow-x-auto w-full">
                        <table id="journalTable" class="w-full table-auto border-collapse border border-gray-200 text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="border px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                                    <th class="border px-4 py-3 text-left font-semibold text-gray-600">Jenis Transaksi</th>
                                    <th class="border px-4 py-3 text-left font-semibold text-gray-600">Referensi</th>
                                    <th class="border px-4 py-3 text-left font-semibold text-gray-600">Pihak Terkait</th>
                                    <th class="border px-4 py-3 text-left font-semibold text-gray-600">Deskripsi</th>
                                    <th class="border px-4 py-3 text-right font-semibold text-gray-600">Jumlah</th>
                                    <th class="border px-4 py-3 text-center font-semibold text-gray-600 no-print">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $tx)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="border px-4 py-3 text-gray-800">
                                        {{ \Carbon\Carbon::parse($tx['date'])->format('d/m/Y') }}
                                    </td>
                                    <td class="border px-4 py-3">
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full 
                                            {{ str_contains($tx['type'], 'Penjualan') || $tx['type'] == 'Pembayaran Masuk' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ str_contains($tx['type'], 'Konsinyasi') ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ str_contains($tx['type'], 'Purchase') || $tx['type'] == 'Pembayaran Keluar' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $tx['type'] == 'Jurnal Voucher' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $tx['type'] == 'Faktur Penjualan' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                            {{ $tx['type'] == 'Faktur Pembelian' ? 'bg-rose-100 text-rose-800' : '' }}
                                            {{ $tx['type'] == 'Kas Masuk (Lain-lain)' || $tx['type'] == 'Kas Keluar / Biaya' ? 'bg-amber-100 text-amber-800' : '' }}
                                        ">
                                            {{ $tx['type'] }}
                                        </span>
                                    </td>
                                    <td class="border px-4 py-3 font-mono font-bold text-gray-700 text-xs">
                                        {{ $tx['reference'] }}
                                    </td>
                                    <td class="border px-4 py-3 text-gray-800 font-medium">
                                        {{ $tx['party'] }}
                                    </td>
                                    <td class="border px-4 py-3 text-gray-600">
                                        {{ $tx['description'] }}
                                    </td>
                                    <td class="border px-4 py-3 text-right font-bold text-slate-800">
                                        Rp {{ number_format($tx['amount'], 0, ',', '.') }}
                                    </td>
                                    <td class="border px-4 py-3 text-center no-print">
                                        @if($tx['details_url'])
                                            <a href="{{ $tx['details_url'] }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-bold hover:underline">
                                                <i class="ri-eye-line"></i> Detail
                                            </a>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTables via CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" no-print></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" no-print>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" no-print></script>
    <script no-print>
        $(document).ready(function() {
            $('#journalTable').DataTable({
                "order": [[ 0, "desc" ]], // Urutan default kolom tanggal desc
                "pageLength": 25,
                "language": {
                    "search": "Cari data:",
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Tidak ada data yang ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data tersedia",
                    "infoFiltered": "(disaring dari _MAX_ total data)",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });
        });
    </script>
</x-app-layout>
