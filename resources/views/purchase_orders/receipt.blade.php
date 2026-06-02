<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Penerimaan Barang (Good Receipt)') }}
            </h2>
            <a href="{{ route('purchase-orders.show', $purchaseOrder->id) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b-4 border-indigo-500">
                    <h3 class="font-bold text-lg text-indigo-700 mb-4 flex items-center gap-2">
                        <i class="ri-file-list-3-line"></i> Dokumen Referensi PO: {{ $purchaseOrder->po_number }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p><span class="text-gray-500 w-24 inline-block">Supplier</span>: <span class="font-bold">{{ $purchaseOrder->supplier->name }}</span></p>
                            <p><span class="text-gray-500 w-24 inline-block">Tgl Order</span>: {{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p><span class="text-gray-500 w-24 inline-block">Total Nilai</span>: <span class="font-bold text-red-600">Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}</span></p>
                            <p><span class="text-gray-500 w-24 inline-block">Status PO</span>: <span class="bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded text-xs font-bold">{{ $purchaseOrder->status }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="ri-information-fill text-blue-400 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Halaman ini digunakan untuk memverifikasi kedatangan barang secara fisik. Dengan menekan tombol <strong>Proses Penerimaan</strong> di bawah, sistem akan otomatis: <br>
                                    1. Menambah stok di gudang sesuai kuantitas PO.<br>
                                    2. Mengubah status PO menjadi "Completed".<br>
                                    3. Mencatat jurnal pergerakan barang (Stock Movement).
                                </p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('purchase-orders.complete', $purchaseOrder->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin barang fisik sudah sesuai dengan daftar di bawah ini? Proses ini tidak dapat dibatalkan.');">
                        @csrf
                        <table class="w-full table-auto border-collapse border border-gray-300 text-sm mb-6">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2 w-12 text-center">No</th>
                                    <th class="border px-4 py-2 text-left">Nama Bahan Baku</th>
                                    <th class="border px-4 py-2 text-center">Kuantitas di PO</th>
                                    <th class="border px-4 py-2 text-center">Aksi / Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->items as $index => $item)
                                <tr>
                                    <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="border px-4 py-2 font-bold">{{ $item->material->name }}</td>
                                    <td class="border px-4 py-2 text-center bg-gray-50 font-mono text-lg">
                                        {{ rtrim(rtrim(number_format($item->quantity, 4, ',', '.'), '0'), ',') }} {{ $item->material->unit }}
                                    </td>
                                    <td class="border px-4 py-2 text-center text-green-600 font-bold">
                                        <i class="ri-check-line"></i> Akan Diterima Penuh
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="flex justify-between items-center mt-8 pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-500">*Penerimaan parsial (sebagian) sedang dalam tahap pengembangan (Tahap 2).</p>
                            <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 font-bold text-lg shadow-lg flex items-center gap-2">
                                <i class="ri-checkbox-circle-fill"></i> Proses Penerimaan & Tambah Stok
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
