<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Penjualan Langsung (Direct Sales)') }}
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

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
                        <!-- Tombol Tambah -->
                        <a href="{{ route('direct-sales.create') }}" class="inline-block px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-bold shadow">
                            + Input Penjualan Baru
                        </a>

                        <!-- Form Filter Tanggal Export Excel -->
                        <form action="{{ route('direct-sales.export') }}" method="GET" class="bg-gray-50 p-3 rounded-lg border flex flex-col sm:flex-row items-end gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Dari Tanggal</label>
                                <input type="date" name="start_date" value="{{ date('Y-m-01') }}" class="border-gray-300 rounded text-sm w-36" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Sampai Tanggal</label>
                                <input type="date" name="end_date" value="{{ date('Y-m-t') }}" class="border-gray-300 rounded text-sm w-36" required>
                            </div>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-bold shadow-sm flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Export Laporan
                            </button>
                        </form>
                    </div>

                    <div class="overflow-x-auto w-full">
                        <table id="salesTable" class="w-full table-auto border-collapse border border-gray-300 text-sm whitespace-nowrap">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2">Tgl Transaksi</th>
                                    <th class="border px-4 py-2">No. Nota / Invoice</th>
                                    <th class="border px-4 py-2">Nama Pembeli</th>
                                    <th class="border px-4 py-2">Total Belanja (Rp)</th>
                                    <th class="border px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                <tr>
                                    <td class="border px-4 py-2 text-center">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                                    <td class="border px-4 py-2 font-bold text-gray-700">{{ $sale->invoice_number }}</td>
                                    <td class="border px-4 py-2 text-green-800 font-bold">
                                        @if($sale->store_id)
                                            {{ $sale->store->name }} <span class="text-xs text-gray-500 font-normal">(Terdaftar)</span>
                                        @else
                                            {{ $sale->customer_name }} <span class="text-xs text-gray-500 font-normal">(Umum)</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2 text-right font-bold text-lg">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                    <td class="border px-4 py-2 text-center">
                                        <a href="{{ route('direct-sales.print', $sale->id) }}" target="_blank" class="text-blue-600 hover:underline font-bold border border-blue-600 px-3 py-1 rounded">
                                            🖨️ Cetak Nota
                                        </a>
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>$(document).ready(function() { $('#salesTable').DataTable({ "order": [[ 0, "desc" ]] }); });</script>
</x-app-layout>