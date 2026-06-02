<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Daftar Pembayaran') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">{{ session('success') }}</div>
                    @endif

                    <div class="mb-4 flex flex-col md:flex-row gap-3 justify-between items-start md:items-center">
                        <a href="{{ route('payments.create') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-bold text-sm">
                            <i class="ri-add-line mr-1"></i> Catat Pembayaran
                        </a>
                        <div class="flex gap-2">
                            <a href="{{ route('payments.index') }}" class="px-3 py-1.5 rounded text-sm {{ !$type ? 'bg-gray-800 text-white' : 'bg-gray-200 text-gray-700' }}">Semua</a>
                            <a href="{{ route('payments.index', ['type' => 'inbound']) }}" class="px-3 py-1.5 rounded text-sm {{ $type === 'inbound' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }}">Masuk (Piutang)</a>
                            <a href="{{ route('payments.index', ['type' => 'outbound']) }}" class="px-3 py-1.5 rounded text-sm {{ $type === 'outbound' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700' }}">Keluar (Hutang)</a>
                        </div>
                    </div>

                    <div class="overflow-x-auto w-full">
                        <table id="paymentTable" class="w-full table-auto border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">Tanggal</th>
                                <th class="border px-4 py-2">No. Payment</th>
                                <th class="border px-4 py-2">Tipe</th>
                                <th class="border px-4 py-2">No. Invoice</th>
                                <th class="border px-4 py-2">Pihak</th>
                                <th class="border px-4 py-2">Metode</th>
                                <th class="border px-4 py-2 text-right">Jumlah</th>
                                <th class="border px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $pay)
                            <tr>
                                <td class="border px-4 py-2 text-center">{{ $pay->payment_date->format('d/m/Y') }}</td>
                                <td class="border px-4 py-2 font-bold">{{ $pay->payment_number }}</td>
                                <td class="border px-4 py-2 text-center">
                                    @if($pay->type === 'inbound')
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">Masuk</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-bold">Keluar</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2"><a href="{{ route('invoices.show', $pay->invoice_id) }}" class="text-blue-600 hover:underline">{{ $pay->invoice->invoice_number }}</a></td>
                                <td class="border px-4 py-2">
                                    {{ $pay->invoice->type === 'sales' ? ($pay->invoice->store->name ?? '-') : ($pay->invoice->supplier->name ?? '-') }}
                                </td>
                                <td class="border px-4 py-2 text-center">{{ $pay->payment_method }}</td>
                                <td class="border px-4 py-2 text-right font-bold {{ $pay->type === 'inbound' ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    <a href="{{ route('payments.show', $pay->id) }}" class="text-blue-600 hover:underline font-bold">Detail</a>
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
    <script>$(document).ready(function() { $('#paymentTable').DataTable({ "order": [[ 0, "desc" ]] }); });</script>
</x-app-layout>
