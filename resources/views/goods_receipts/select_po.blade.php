<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pilih PO untuk Penerimaan Barang') }}
            </h2>
            <a href="{{ route('goods-receipts.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                        <p class="text-sm text-blue-700">
                            <i class="ri-information-fill mr-1"></i>
                            Pilih Purchase Order yang berstatus <strong>Pending</strong> untuk memproses penerimaan barang.
                        </p>
                    </div>

                    @if($pendingPOs->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <i class="ri-inbox-line text-4xl mb-2 block"></i>
                            <p>Tidak ada PO yang menunggu penerimaan barang.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($pendingPOs as $po)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition flex justify-between items-center">
                                <div>
                                    <p class="font-bold text-lg text-blue-700">{{ $po->po_number }}</p>
                                    <p class="text-gray-600 text-sm">Supplier: {{ $po->supplier->name ?? '-' }}</p>
                                    <p class="text-gray-500 text-sm">Tanggal: {{ \Carbon\Carbon::parse($po->order_date)->format('d F Y') }} | Total: Rp {{ number_format($po->total_amount, 0, ',', '.') }}</p>
                                </div>
                                <a href="{{ route('goods-receipts.create', ['po_id' => $po->id]) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 font-bold text-sm flex items-center gap-1">
                                    <i class="ri-arrow-right-line"></i> Proses
                                </a>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
