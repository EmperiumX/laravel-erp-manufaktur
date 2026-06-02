<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Penerimaan Barang (Goods Receipt)') }}
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

                    <a href="{{ route('goods-receipts.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 mb-4 font-bold">
                        <i class="ri-inbox-archive-line mr-1"></i> Buat Penerimaan Baru
                    </a>

                    <div class="overflow-x-auto w-full">
                        <table id="grTable" class="w-full table-auto border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">Tgl Terima</th>
                                <th class="border px-4 py-2">No. GR</th>
                                <th class="border px-4 py-2">No. PO</th>
                                <th class="border px-4 py-2">Supplier</th>
                                <th class="border px-4 py-2">Diterima Oleh</th>
                                <th class="border px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($goodsReceipts as $gr)
                            <tr>
                                <td class="border px-4 py-2 text-center">{{ $gr->receipt_date->format('d/m/Y') }}</td>
                                <td class="border px-4 py-2 font-bold text-indigo-600">{{ $gr->receipt_number }}</td>
                                <td class="border px-4 py-2">
                                    <a href="{{ route('purchase-orders.show', $gr->purchase_order_id) }}" class="text-blue-600 hover:underline">{{ $gr->purchaseOrder->po_number }}</a>
                                </td>
                                <td class="border px-4 py-2">{{ $gr->purchaseOrder->supplier->name ?? '-' }}</td>
                                <td class="border px-4 py-2">{{ $gr->receiver->name ?? '-' }}</td>
                                <td class="border px-4 py-2 text-center">
                                    <a href="{{ route('goods-receipts.show', $gr->id) }}" class="text-blue-600 hover:underline font-bold border border-blue-600 px-3 py-1 rounded inline-block">Detail</a>
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
    <script>$(document).ready(function() { $('#grTable').DataTable({ "order": [[ 0, "desc" ]] }); });</script>
</x-app-layout>
