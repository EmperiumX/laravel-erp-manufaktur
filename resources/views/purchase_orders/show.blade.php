<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Purchase Order: ') }} <span class="text-blue-600">{{ $purchaseOrder->po_number }}</span>
            </h2>
            <a href="{{ route('purchase-orders.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-gray-500 text-sm uppercase tracking-wide mb-2">Informasi Supplier</h3>
                        <p class="font-bold text-lg">{{ $purchaseOrder->supplier->name ?? 'Supplier Dihapus' }}</p>
                        <p class="text-gray-600">{{ $purchaseOrder->supplier->address ?? '-' }}</p>
                        <p class="text-gray-600">Telp: {{ $purchaseOrder->supplier->phone_number ?? '-' }}</p>
                    </div>
                    <div class="text-left md:text-right">
                        <h3 class="text-gray-500 text-sm uppercase tracking-wide mb-2">Informasi PO</h3>
                        <p><span class="text-gray-600">Tanggal:</span> <span class="font-bold">{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('d F Y') }}</span></p>
                        <p><span class="text-gray-600">Status:</span> 
                            @if($purchaseOrder->status == 'Pending')
                                <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded text-xs font-bold ml-1">PENDING</span>
                            @else
                                <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-xs font-bold ml-1">SELESAI</span>
                            @endif
                        </p>
                        <p class="mt-2"><span class="text-gray-600">Catatan:</span> {{ $purchaseOrder->notes ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4 text-blue-700 border-b pb-2">Daftar Barang (Item Details)</h3>
                    
                    <table class="w-full table-auto border-collapse border border-gray-300 text-sm mb-6">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">No</th>
                                <th class="border px-4 py-2">Bahan Baku / Material</th>
                                <th class="border px-4 py-2 text-center">Kuantitas</th>
                                <th class="border px-4 py-2 text-right">Harga Satuan</th>
                                <th class="border px-4 py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrder->items as $index => $item)
                            <tr>
                                <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                                <td class="border px-4 py-2 font-bold">{{ $item->material->name }}</td>
                                <td class="border px-4 py-2 text-center">{{ rtrim(rtrim(number_format($item->quantity, 4, ',', '.'), '0'), ',') }} {{ $item->material->unit }}</td>
                                <td class="border px-4 py-2 text-right">Rp {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                <td class="border px-4 py-2 text-right font-bold text-gray-700">Rp {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-blue-50">
                                <td colspan="4" class="border px-4 py-3 text-right font-bold text-lg">GRAND TOTAL :</td>
                                <td class="border px-4 py-3 text-right font-bold text-lg text-blue-700">Rp {{ number_format($purchaseOrder->total_amount, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- JIKA STATUS MASIH PENDING, MUNCULKAN TOMBOL TERIMA BARANG -->
                    @if($purchaseOrder->status == 'Pending')
                        <div class="mt-6 border-t pt-6 text-center">
                            <p class="text-gray-600 mb-4">Pastikan fisik barang sudah tiba di gudang dan jumlahnya sesuai sebelum mengeklik tombol ini.</p>
                            <form action="{{ route('purchase-orders.complete', $purchaseOrder->id) }}" method="POST" onsubmit="return confirm('Proses ini akan menambah stok gudang secara otomatis dan tidak dapat dibatalkan. Lanjutkan?');">
                                @csrf
                                <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 font-bold text-lg shadow-lg">
                                    ✓ Terima Barang & Tambah Stok Gudang
                                </button>
                            </form>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>