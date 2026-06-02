<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Laporan Piutang Dagang') }}</h2>
            <a href="{{ route('reports.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500">Total Piutang Belum Lunas</p>
                    <p class="text-2xl font-bold text-blue-700">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <p class="text-sm text-gray-500">Jumlah Invoice</p>
                    <p class="text-2xl font-bold text-yellow-700">{{ $invoices->count() }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <p class="text-sm text-gray-500">Jatuh Tempo</p>
                    <p class="text-2xl font-bold text-red-700">{{ $overdue->count() }} invoice</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto w-full">
                    <table id="arTable" class="w-full table-auto border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">No. Invoice</th>
                                <th class="border px-4 py-2">Toko/Mitra</th>
                                <th class="border px-4 py-2">Tgl Invoice</th>
                                <th class="border px-4 py-2">Jatuh Tempo</th>
                                <th class="border px-4 py-2 text-right">Total</th>
                                <th class="border px-4 py-2 text-right">Dibayar</th>
                                <th class="border px-4 py-2 text-right">Sisa</th>
                                <th class="border px-4 py-2">Status</th>
                                <th class="border px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $inv)
                            <tr class="{{ $inv->is_overdue ? 'bg-red-50' : '' }}">
                                <td class="border px-4 py-2 font-bold">{{ $inv->invoice_number }}</td>
                                <td class="border px-4 py-2">{{ $inv->store->name ?? '-' }}</td>
                                <td class="border px-4 py-2 text-center">{{ $inv->invoice_date->format('d/m/Y') }}</td>
                                <td class="border px-4 py-2 text-center {{ $inv->is_overdue ? 'text-red-600 font-bold' : '' }}">
                                    {{ $inv->due_date->format('d/m/Y') }}
                                    @if($inv->is_overdue) <span class="text-xs block">({{ $inv->due_date->diffInDays(now()) }} hari lewat)</span> @endif
                                </td>
                                <td class="border px-4 py-2 text-right">Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</td>
                                <td class="border px-4 py-2 text-right text-green-600">Rp {{ number_format($inv->paid_amount, 0, ',', '.') }}</td>
                                <td class="border px-4 py-2 text-right font-bold text-red-600">Rp {{ number_format($inv->balance_due, 0, ',', '.') }}</td>
                                <td class="border px-4 py-2 text-center">
                                    <span class="px-2 py-0.5 rounded text-xs font-bold {{ $inv->is_overdue ? 'bg-red-200 text-red-800' : 'bg-yellow-200 text-yellow-800' }}">
                                        {{ $inv->is_overdue ? 'Jatuh Tempo!' : $inv->status }}
                                    </span>
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    <a href="{{ route('invoices.show', $inv->id) }}" class="text-blue-600 hover:underline text-sm font-bold">Detail</a>
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
    <script>$(document).ready(function() { $('#arTable').DataTable(); });</script>
</x-app-layout>
