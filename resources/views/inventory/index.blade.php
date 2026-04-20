<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stok Gudang Saat Ini') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6 flex justify-between items-center">
                        <div class="flex gap-4">
                            <a href="{{ route('inventory.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md font-bold shadow">📦 Stok Saat Ini</a>
                            <a href="{{ route('inventory.history') }}" class="bg-gray-200 text-gray-700 hover:bg-gray-300 px-4 py-2 rounded-md font-bold">⏱️ Riwayat Pergerakan</a>
                        </div>
                        
                        <!-- TOMBOL EXPORT EXCEL -->
                        <a href="{{ route('inventory.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-bold shadow-md flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            Export ke Excel
                        </a>
                    </div>

                    <div class="overflow-x-auto w-full">
                        <table id="stockTable" class="w-full table-auto border-collapse border border-gray-300 text-sm whitespace-nowrap">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2">No</th>
                                    <th class="border px-4 py-2">Nama Barang</th>
                                    <th class="border px-4 py-2 text-center">Tipe Kategori</th>
                                    <th class="border px-4 py-2 text-center">Kuantitas Tersedia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stocks as $index => $stock)
                                <tr>
                                    <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="border px-4 py-2 font-bold text-blue-700">
                                        {{ $stock->material ? $stock->material->name : ($stock->product ? $stock->product->name : 'Unknown') }}
                                    </td>
                                    <td class="border px-4 py-2 text-center">
                                        @if($stock->material)
                                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Bahan Baku / Kemasan</span>
                                        @elseif($stock->product)
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Produk Jadi</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2 text-center font-bold text-lg">
                                        {{ rtrim(rtrim(number_format($stock->quantity, 4, ',', '.'), '0'), ',') }} 
                                        {{ $stock->material ? $stock->material->unit : ($stock->product ? $stock->product->packaging : '') }}
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
    <script>$(document).ready(function() { $('#stockTable').DataTable(); });</script>
</x-app-layout>