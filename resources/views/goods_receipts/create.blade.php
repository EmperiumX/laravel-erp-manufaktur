<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Penerimaan Barang dari PO: ') }} <span class="text-blue-600">{{ $purchaseOrder->po_number }}</span>
            </h2>
            <a href="{{ route('goods-receipts.create') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">


            <!-- Info PO -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b-4 border-indigo-500">
                    <h3 class="font-bold text-lg text-indigo-700 mb-4 flex items-center gap-2">
                        <i class="ri-file-list-3-line"></i> Dokumen Referensi PO
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p><span class="text-gray-500">Supplier</span>: <span class="font-bold">{{ $purchaseOrder->supplier->name }}</span></p>
                            <p><span class="text-gray-500">Tgl Order</span>: {{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p><span class="text-gray-500">Total Nilai</span>: <span class="font-bold text-red-600">Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}</span></p>
                            <p><span class="text-gray-500">Status PO</span>: <span class="bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded text-xs font-bold">{{ $purchaseOrder->status }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Penerimaan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                        <p class="text-sm text-blue-700">
                            <i class="ri-information-fill mr-1"></i>
                            Periksa dan isi jumlah barang yang <strong>benar-benar diterima</strong>. Jika jumlah berbeda dari PO, isikan jumlah aktual yang diterima.
                        </p>
                    </div>

                    <form action="{{ route('goods-receipts.store') }}" method="POST" onsubmit="return confirm('Pastikan jumlah barang yang diterima sudah benar. Lanjutkan?');">
                        @csrf
                        <input type="hidden" name="purchase_order_id" value="{{ $purchaseOrder->id }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Penerimaan <span class="text-red-500">*</span></label>
                                <input type="date" name="receipt_date" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Catatan (Opsional)</label>
                                <input type="text" name="notes" placeholder="Catatan penerimaan barang..." class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>

                        <!-- Kebijakan Selisih Barang (Backorder) -->
                        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md shadow-sm">
                            <label class="block text-yellow-800 text-sm font-bold mb-2">
                                <i class="ri-git-branch-line mr-1"></i> Kebijakan Selisih Penerimaan (Backorder)
                            </label>
                            <select name="backorder_policy" class="w-full border-gray-300 rounded-md text-sm shadow-sm" required>
                                <option value="backorder">Buat Backorder (Biarkan PO tetap aktif menunggu sisa barang)</option>
                                <option value="no_backorder">Jangan Buat Backorder (Batalkan sisa barang, selesaikan PO awal)</option>
                            </select>
                            <p class="text-xs text-yellow-700 mt-2">
                                * Pilihan ini hanya berlaku jika jumlah barang yang diterima kurang dari jumlah pesanan PO. Invoice tagihan akan otomatis dibuat hanya untuk barang yang riil diterima.
                            </p>
                        </div>

                        <table class="w-full table-auto border-collapse border border-gray-300 text-sm mb-6">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2 w-12">No</th>
                                    <th class="border px-4 py-2 text-left">Bahan Baku</th>
                                    <th class="border px-4 py-2 text-center">Qty di PO</th>
                                    <th class="border px-4 py-2 text-center">Qty Diterima <span class="text-red-500">*</span></th>
                                    <th class="border px-4 py-2">Catatan Item</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->items as $index => $item)
                                <tr>
                                    <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="border px-4 py-2 font-bold">{{ $item->material->name }}</td>
                                    <td class="border px-4 py-2 text-center bg-gray-50 font-mono">
                                        {{ rtrim(rtrim(number_format($item->quantity, 4, ',', '.'), '0'), ',') }} {{ $item->material->unit }}
                                    </td>
                                    <td class="border px-2 py-2 text-center">
                                        <input type="hidden" name="items[{{ $index }}][material_id]" value="{{ $item->material_id }}">
                                        <input type="hidden" name="items[{{ $index }}][quantity_ordered]" value="{{ $item->quantity }}">
                                        <input type="number" step="0.0001" name="items[{{ $index }}][quantity_received]" value="{{ $item->quantity }}" class="w-full border-gray-300 rounded text-center" required min="0">
                                    </td>
                                    <td class="border px-2 py-2">
                                        <input type="text" name="items[{{ $index }}][notes]" placeholder="Opsional..." class="w-full border-gray-300 rounded text-sm">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="flex justify-between items-center mt-8 pt-4 border-t border-gray-200">
                            <a href="{{ route('goods-receipts.create') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
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
