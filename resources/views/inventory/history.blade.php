<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Pergerakan Stok (Kartu Stok)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6 flex gap-4">
                        <a href="{{ route('inventory.index') }}" class="bg-gray-200 text-gray-700 hover:bg-gray-300 px-4 py-2 rounded-md font-bold">📦 Stok Saat Ini</a>
                        <a href="{{ route('inventory.history') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md font-bold shadow">⏱️ Riwayat Pergerakan</a>
                    </div>

                    <div class="overflow-x-auto w-full">
                        <table id="historyTable" class="w-full table-auto border-collapse border border-gray-300 text-sm whitespace-nowrap">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2">Waktu Transaksi</th>
                                    <th class="border px-4 py-2">IN/OUT</th>
                                    <th class="border px-4 py-2">Nama Barang</th>
                                    <th class="border px-4 py-2 text-right">Kuantitas</th>
                                    <th class="border px-4 py-2">Referensi & Catatan</th>
                                    <th class="border px-4 py-2">Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($movements as $log)
                                <tr>
                                    <td class="border px-4 py-2">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</td>
                                    <td class="border px-4 py-2 text-center font-bold">
                                        @if($log->type == 'IN')
                                            <span class="text-green-600">IN (Masuk)</span>
                                        @else
                                            <span class="text-red-600">OUT (Keluar)</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2 font-bold text-gray-700">
                                        {{ $log->stockItem->material ? $log->stockItem->material->name : ($log->stockItem->product ? $log->stockItem->product->name : '-') }}
                                    </td>
                                    <td class="border px-4 py-2 text-right font-bold {{ $log->type == 'IN' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $log->type == 'IN' ? '+' : '-' }} {{ rtrim(rtrim(number_format($log->quantity, 4, ',', '.'), '0'), ',') }}
                                    </td>
                                    <td class="border px-4 py-2">
                                        <span class="font-bold text-blue-600">{{ $log->reference }}</span><br>
                                        <span class="text-xs text-gray-500">{{ $log->notes }}</span>
                                    </td>
                                    <td class="border px-4 py-2 text-xs">{{ $log->user->name ?? '-' }}</td>
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
    <script>$(document).ready(function() { $('#historyTable').DataTable({ "order": [[ 0, "desc" ]] }); });</script>
</x-app-layout>