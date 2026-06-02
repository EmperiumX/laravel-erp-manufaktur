<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Penerimaan: ') }} <span class="text-indigo-600">{{ $goodsReceipt->receipt_number }}</span>
            </h2>
            <a href="{{ route('goods-receipts.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-gray-500 text-sm uppercase tracking-wide mb-2">Informasi Penerimaan</h3>
                        <p><span class="text-gray-600">No. GR:</span> <span class="font-bold">{{ $goodsReceipt->receipt_number }}</span></p>
                        <p><span class="text-gray-600">Tanggal:</span> <span class="font-bold">{{ $goodsReceipt->receipt_date->format('d F Y') }}</span></p>
                        <p><span class="text-gray-600">Diterima Oleh:</span> <span class="font-bold">{{ $goodsReceipt->receiver->name ?? '-' }}</span></p>
                        <p><span class="text-gray-600">Catatan:</span> {{ $goodsReceipt->notes ?? '-' }}</p>
                    </div>
                    <div class="text-left md:text-right">
                        <h3 class="text-gray-500 text-sm uppercase tracking-wide mb-2">Referensi PO</h3>
                        <p><span class="text-gray-600">No. PO:</span> <a href="{{ route('purchase-orders.show', $goodsReceipt->purchase_order_id) }}" class="font-bold text-blue-600 hover:underline">{{ $goodsReceipt->purchaseOrder->po_number }}</a></p>
                        <p><span class="text-gray-600">Supplier:</span> <span class="font-bold">{{ $goodsReceipt->purchaseOrder->supplier->name ?? '-' }}</span></p>
                        <p><span class="text-gray-600">Status PO:</span> <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-xs font-bold ml-1">{{ $goodsReceipt->purchaseOrder->status }}</span></p>
                    </div>
                </div>
            </div>

            <!-- Invoice Info -->
            @if($invoice)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border-l-4 border-blue-500">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-lg text-blue-700 flex items-center gap-2">
                                <i class="ri-bill-line"></i> Invoice Otomatis: {{ $invoice->invoice_number }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Total: <span class="font-bold">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span> |
                                Jatuh Tempo: <span class="font-bold">{{ $invoice->due_date->format('d F Y') }}</span> |
                                Status:
                                @switch($invoice->status)
                                    @case('Sent') <span class="bg-blue-200 text-blue-800 px-2 py-0.5 rounded text-xs font-bold">Belum Dibayar</span> @break
                                    @case('Partial') <span class="bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded text-xs font-bold">Sebagian</span> @break
                                    @case('Paid') <span class="bg-green-200 text-green-800 px-2 py-0.5 rounded text-xs font-bold">Lunas</span> @break
                                @endswitch
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('goods-receipts.print-invoice', $goodsReceipt->id) }}" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-bold text-sm flex items-center gap-1">
                                <i class="ri-printer-line"></i> Print Invoice
                            </a>
                            @if(in_array($invoice->status, ['Sent', 'Partial']))
                                <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 font-bold text-sm flex items-center gap-1">
                                    <i class="ri-money-dollar-circle-line"></i> Bayar
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4 text-indigo-700 border-b pb-2">Detail Barang Diterima</h3>
                    <table class="w-full table-auto border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">No</th>
                                <th class="border px-4 py-2">Bahan Baku</th>
                                <th class="border px-4 py-2 text-center">Qty di PO</th>
                                <th class="border px-4 py-2 text-center">Qty Diterima</th>
                                <th class="border px-4 py-2 text-center">Selisih</th>
                                <th class="border px-4 py-2">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($goodsReceipt->items as $index => $item)
                            <tr>
                                <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                                <td class="border px-4 py-2 font-bold">{{ $item->material->name ?? '-' }}</td>
                                <td class="border px-4 py-2 text-center">{{ $item->quantity_ordered }}</td>
                                <td class="border px-4 py-2 text-center font-bold {{ $item->quantity_received < $item->quantity_ordered ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $item->quantity_received }}
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    @php $diff = $item->quantity_received - $item->quantity_ordered; @endphp
                                    @if($diff == 0)
                                        <span class="text-green-600 font-bold">Sesuai</span>
                                    @elseif($diff < 0)
                                        <span class="text-red-600 font-bold">{{ $diff }} (Kurang)</span>
                                    @else
                                        <span class="text-blue-600 font-bold">+{{ $diff }} (Lebih)</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-sm text-gray-500">{{ $item->notes ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
