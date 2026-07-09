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

                    {{-- TOMBOL AKSI --}}
                    <div class="mt-6 border-t pt-6 flex flex-wrap items-center justify-between gap-3">
                        {{-- Kiri: Invoice (jika sudah completed & invoice ada) --}}
                        <div>
                            @if($invoice)
                                <div class="flex flex-wrap items-center gap-2">
                                    <button
                                        type="button"
                                        onclick="openInvoicePreview()"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-lg hover:from-emerald-600 hover:to-teal-700 shadow-lg shadow-emerald-500/25 transition-all duration-200 text-sm"
                                        id="btn-preview-invoice"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Lihat Invoice
                                    </button>
                                    <a href="{{ route('purchase-orders.print-invoice', $purchaseOrder->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow transition-all duration-200 text-sm">
                                        🖨️ Cetak Tagihan (Supplier)
                                    </a>
                                    <a href="{{ route('purchase-orders.print-consignment', $purchaseOrder->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 shadow transition-all duration-200 text-sm">
                                        🖨️ Cetak Konsinyasi (Mitra)
                                    </a>
                                </div>
                            @elseif($purchaseOrder->status == 'Completed')
                                <span class="text-sm text-gray-400 italic">Invoice belum tersedia</span>
                            @endif
                        </div>

                        {{-- Kanan: Penerimaan Barang (jika masih Pending) --}}
                        <div>
                            @if($purchaseOrder->status == 'Pending')
                                <a href="{{ route('goods-receipts.create', ['po_id' => $purchaseOrder->id]) }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-bold text-md shadow flex items-center gap-2 transition">
                                    <span>Langkah Selanjutnya: Penerimaan Barang (Goods Receipt)</span>
                                    <i class="ri-arrow-right-line"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- ================================================================== --}}
    {{-- MODAL PREVIEW INVOICE PEMBELIAN --}}
    {{-- ================================================================== --}}
    @if($invoice)
    <div id="invoiceModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        {{-- Backdrop --}}
        <div id="invoiceBackdrop" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeInvoicePreview()"></div>

        {{-- Modal Container --}}
        <div class="flex items-center justify-center min-h-screen p-4">
            <div id="invoiceModalContent" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden transform transition-all duration-300 scale-95 opacity-0 z-10">

                {{-- Modal Header --}}
                <div class="sticky top-0 bg-gradient-to-r from-[#065f46] to-[#059669] px-6 py-4 flex items-center justify-between z-20">
                    <div>
                        <h3 id="modalTitle" class="text-white font-bold text-lg tracking-wide">Preview Invoice Pembelian</h3>
                        <p class="text-emerald-200 text-sm font-medium mt-0.5">{{ $invoice->invoice_number }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold
                            @if($invoice->status === 'Paid') bg-green-100 text-green-700
                            @elseif($invoice->status === 'Partial') bg-indigo-100 text-indigo-700
                            @else bg-yellow-100 text-yellow-700
                            @endif
                        ">
                            @if($invoice->status === 'Paid') ✓ LUNAS
                            @elseif($invoice->status === 'Partial') SEBAGIAN
                            @else BELUM DIBAYAR
                            @endif
                        </span>
                        <button onclick="closeInvoicePreview()" class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all duration-200" aria-label="Tutup modal">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Body (Scrollable) --}}
                <div class="overflow-y-auto px-6 py-5" style="max-height: calc(90vh - 140px);">

                    {{-- Info Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        {{-- Detail Invoice --}}
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Detail Invoice</p>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 font-medium">Tanggal</span>
                                    <span class="text-gray-800 font-semibold">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d F Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 font-medium">Jatuh Tempo</span>
                                    <span class="text-gray-800 font-semibold">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d F Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 font-medium">Referensi PO</span>
                                    <span class="text-blue-700 font-semibold">{{ $purchaseOrder->po_number }}</span>
                                </div>
                            </div>
                        </div>
                        {{-- Supplier --}}
                        <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-200">
                            <p class="text-xs font-bold text-emerald-400 uppercase tracking-widest mb-3">Supplier</p>
                            <p class="text-lg font-bold text-[#065f46] mb-1">{{ $purchaseOrder->supplier->name ?? '-' }}</p>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $purchaseOrder->supplier->address ?? '-' }}</p>
                            <p class="text-sm text-gray-600 mt-1">Telp: {{ $purchaseOrder->supplier->phone_number ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Items Table --}}
                    <div class="rounded-xl overflow-hidden border border-gray-200 mb-5">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-[#065f46] text-white">
                                    <th class="px-4 py-3 text-center text-xs uppercase tracking-wider font-semibold w-12">No</th>
                                    <th class="px-4 py-3 text-left text-xs uppercase tracking-wider font-semibold">Deskripsi</th>
                                    <th class="px-4 py-3 text-center text-xs uppercase tracking-wider font-semibold w-16">Qty</th>
                                    <th class="px-4 py-3 text-center text-xs uppercase tracking-wider font-semibold w-16">Satuan</th>
                                    <th class="px-4 py-3 text-right text-xs uppercase tracking-wider font-semibold w-32">Harga</th>
                                    <th class="px-4 py-3 text-right text-xs uppercase tracking-wider font-semibold w-36">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $idx => $invItem)
                                <tr class="{{ $idx % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                                    <td class="px-4 py-3 text-center text-gray-500">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3 font-semibold text-gray-800">{{ $invItem->description }}</td>
                                    <td class="px-4 py-3 text-center font-semibold">{{ number_format($invItem->quantity, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-center text-gray-500">{{ $invItem->unit }}</td>
                                    <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($invItem->unit_price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-800">Rp {{ number_format($invItem->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals --}}
                    <div class="flex justify-end mb-2">
                        <div class="w-full md:w-80 space-y-1">
                            <div class="flex justify-between px-4 py-2 text-sm">
                                <span class="font-medium text-gray-600">Subtotal</span>
                                <span class="font-bold text-gray-800">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if($invoice->tax_amount > 0)
                            <div class="flex justify-between px-4 py-2 text-sm">
                                <span class="font-medium text-gray-600">PPN</span>
                                <span class="font-bold text-gray-800">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @if($invoice->discount_amount > 0)
                            <div class="flex justify-between px-4 py-2 text-sm">
                                <span class="font-medium text-gray-600">Diskon</span>
                                <span class="font-bold text-red-600">- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between items-center bg-gradient-to-r from-[#065f46] to-[#059669] text-white rounded-lg px-4 py-3 mt-1">
                                <span class="font-bold text-sm">TOTAL</span>
                                <span class="font-bold text-lg">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                            </div>
                            @if($invoice->paid_amount > 0)
                            <div class="flex justify-between px-4 py-2 text-sm border-t border-gray-200">
                                <span class="font-medium text-green-600">Dibayar</span>
                                <span class="font-bold text-green-600">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                            </div>
                            @if(($invoice->total_amount - $invoice->paid_amount) > 0)
                            <div class="flex justify-between px-4 py-2 text-sm border-t border-gray-200">
                                <span class="font-medium text-red-600">Sisa Tagihan</span>
                                <span class="font-bold text-red-600">Rp {{ number_format($invoice->total_amount - $invoice->paid_amount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @endif
                        </div>
                    </div>

                    {{-- Notes --}}
                    @if($invoice->notes)
                    <div class="bg-gray-50 border-l-4 border-[#065f46] rounded-r-lg p-4 mt-4">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Catatan</p>
                        <p class="text-sm text-gray-600">{{ $invoice->notes }}</p>
                    </div>
                    @endif

                </div>

                {{-- Modal Footer --}}
                <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex flex-wrap items-center justify-between gap-2 z-20">
                    <button onclick="closeInvoicePreview()" class="px-5 py-2.5 text-gray-600 hover:text-gray-800 hover:bg-gray-200 font-semibold rounded-lg transition-all duration-200 text-sm">
                        Tutup
                    </button>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('purchase-orders.print-invoice', $purchaseOrder->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-bold rounded-lg hover:from-blue-700 hover:to-indigo-800 shadow-lg shadow-blue-500/25 transition-all duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Cetak Tagihan (Supplier)
                        </a>
                        <a href="{{ route('purchase-orders.print-consignment', $purchaseOrder->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-fuchsia-700 text-white font-bold rounded-lg hover:from-purple-700 hover:to-fuchsia-800 shadow-lg shadow-purple-500/25 transition-all duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Cetak Konsinyasi (Mitra)
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function openInvoicePreview() {
            var modal = document.getElementById('invoiceModal');
            var backdrop = document.getElementById('invoiceBackdrop');
            var content = document.getElementById('invoiceModalContent');

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            requestAnimationFrame(function() {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            });
        }

        function closeInvoicePreview() {
            var modal = document.getElementById('invoiceModal');
            var backdrop = document.getElementById('invoiceBackdrop');
            var content = document.getElementById('invoiceModalContent');

            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');

            setTimeout(function() {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeInvoicePreview();
            }
        });
    </script>
    @endif

</x-app-layout>