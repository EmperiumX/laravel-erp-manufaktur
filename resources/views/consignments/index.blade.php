<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengiriman Konsinyasi (Surat Jalan / DO)') }}
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
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <a href="{{ route('consignments.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 mb-4 font-bold">
                        + Buat Surat Jalan (DO)
                    </a>

                    <div class="overflow-x-auto w-full">
                        <table id="doTable" class="w-full table-auto border-collapse border border-gray-300 text-sm whitespace-nowrap">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2">Tgl Kirim</th>
                                    <th class="border px-4 py-2">No. Surat Jalan</th>
                                    <th class="border px-4 py-2">Toko / Tujuan</th>
                                    <th class="border px-4 py-2">Total Nilai (Rp)</th>
                                    <th class="border px-4 py-2">Status</th>
                                    <th class="border px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shipments as $do)
                                <tr>
                                    <td class="border px-4 py-2 text-center">{{ \Carbon\Carbon::parse($do->shipment_date)->format('d/m/Y') }}</td>
                                    <td class="border px-4 py-2 font-bold">{{ $do->shipment_number }}</td>
                                    <td class="border px-4 py-2 text-blue-700 font-bold">{{ $do->store->name ?? '-' }}</td>
                                    <td class="border px-4 py-2 text-right font-bold">Rp {{ number_format($do->total_amount, 0, ',', '.') }}</td>
                                    <td class="border px-4 py-2 text-center">
                                        @if($do->status == 'Sent')
                                            <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded text-xs font-bold">Terkirim (Konsinyasi)</span>
                                        @elseif($do->status == 'Invoiced')
                                            <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-xs font-bold">Sudah Ditagih</span>
                                        @else
                                            <span class="bg-red-200 text-red-800 px-2 py-1 rounded text-xs font-bold">Batal</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2 text-center">
                                        <a href="{{ route('consignments.print', $do->id) }}" target="_blank" class="text-blue-600 hover:underline font-bold border border-blue-600 px-3 py-1 rounded inline-block mb-1">
                                            🖨️ Surat Jalan
                                        </a>
                                        @if($do->invoice)
                                        <button
                                            type="button"
                                            onclick="openInvoicePreview({{ $do->id }})"
                                            class="text-green-600 hover:text-white hover:bg-green-600 font-bold border border-green-600 px-3 py-1 rounded inline-block transition-all duration-200"
                                            id="btn-invoice-{{ $do->id }}"
                                        >
                                            🧾 Invoice
                                        </button>
                                        @endif
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

    {{-- ================================================================== --}}
    {{-- MODAL PREVIEW INVOICE --}}
    {{-- ================================================================== --}}
    <div id="invoiceModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        {{-- Backdrop --}}
        <div id="invoiceBackdrop" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeInvoicePreview()"></div>

        {{-- Modal Container --}}
        <div class="flex items-center justify-center min-h-screen p-4">
            <div id="invoiceModalContent" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden transform transition-all duration-300 scale-95 opacity-0 z-10">

                {{-- Modal Header --}}
                <div class="sticky top-0 bg-gradient-to-r from-[#1e3a8a] to-[#2563eb] px-6 py-4 flex items-center justify-between z-20">
                    <div>
                        <h3 id="modalTitle" class="text-white font-bold text-lg tracking-wide">Preview Invoice</h3>
                        <p id="modalInvoiceNumber" class="text-blue-200 text-sm font-medium mt-0.5"></p>
                    </div>
                    <button onclick="closeInvoicePreview()" class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all duration-200" aria-label="Tutup modal">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
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
                                    <span id="modalInvoiceDate" class="text-gray-800 font-semibold"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 font-medium">Jatuh Tempo</span>
                                    <span id="modalDueDate" class="text-gray-800 font-semibold"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 font-medium">Referensi DO</span>
                                    <span id="modalReference" class="text-blue-700 font-semibold"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 font-medium">Status</span>
                                    <span id="modalStatus"></span>
                                </div>
                            </div>
                        </div>
                        {{-- Toko Tujuan --}}
                        <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                            <p class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-3">Kepada</p>
                            <p id="modalStoreName" class="text-lg font-bold text-[#1e3a8a] mb-1"></p>
                            <p id="modalStoreAddress" class="text-sm text-gray-600 leading-relaxed"></p>
                            <p id="modalStorePhone" class="text-sm text-gray-600 mt-1"></p>
                        </div>
                    </div>

                    {{-- Items Table --}}
                    <div class="rounded-xl overflow-hidden border border-gray-200 mb-5">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-[#1e3a8a] text-white">
                                    <th class="px-4 py-3 text-center text-xs uppercase tracking-wider font-semibold w-12">No</th>
                                    <th class="px-4 py-3 text-left text-xs uppercase tracking-wider font-semibold">Deskripsi</th>
                                    <th class="px-4 py-3 text-center text-xs uppercase tracking-wider font-semibold w-16">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs uppercase tracking-wider font-semibold w-32">Harga</th>
                                    <th class="px-4 py-3 text-right text-xs uppercase tracking-wider font-semibold w-36">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="modalItemsBody">
                                {{-- Rows populated by JS --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals --}}
                    <div class="flex justify-end mb-2">
                        <div class="w-full md:w-80 space-y-1" id="modalTotalsArea">
                            {{-- Populated by JS --}}
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div id="modalNotesSection" class="hidden bg-gray-50 border-l-4 border-[#1e3a8a] rounded-r-lg p-4 mt-4">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Catatan</p>
                        <p id="modalNotes" class="text-sm text-gray-600"></p>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex items-center justify-between z-20">
                    <button onclick="closeInvoicePreview()" class="px-5 py-2.5 text-gray-600 hover:text-gray-800 hover:bg-gray-200 font-semibold rounded-lg transition-all duration-200 text-sm">
                        Tutup
                    </button>
                    <a id="modalPrintLink" href="#" target="_blank" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-[#1e3a8a] to-[#2563eb] text-white font-bold rounded-lg hover:from-[#1e3080] hover:to-[#1d4ed8] shadow-lg shadow-blue-500/25 transition-all duration-200 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak PDF
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- ================================================================== --}}
    {{-- INVOICE DATA (embedded as JSON for JS consumption) --}}
    {{-- ================================================================== --}}
    <script>
        // Invoice data prepared in the controller (avoids Blade parser issues)
        const invoiceData = @json($invoiceDataMap);

        function formatRupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }

        function openInvoicePreview(shipmentId) {
            const data = invoiceData[shipmentId];
            if (!data) return;

            const modal = document.getElementById('invoiceModal');
            const backdrop = document.getElementById('invoiceBackdrop');
            const content = document.getElementById('invoiceModalContent');

            // Populate header
            document.getElementById('modalInvoiceNumber').textContent = data.invoice_number;

            // Populate info
            document.getElementById('modalInvoiceDate').textContent = data.invoice_date;
            document.getElementById('modalDueDate').textContent = data.due_date;
            document.getElementById('modalReference').textContent = 'DO: ' + data.shipment_number;

            // Status badge
            const statusEl = document.getElementById('modalStatus');
            statusEl.replaceChildren();
            const badge = document.createElement('span');
            badge.classList.add('px-2.5', 'py-1', 'rounded-full', 'text-xs', 'font-bold');
            if (data.status === 'Paid') {
                badge.classList.add('bg-green-100', 'text-green-700');
                badge.textContent = '✓ LUNAS';
            } else if (data.status === 'Partial') {
                badge.classList.add('bg-indigo-100', 'text-indigo-700');
                badge.textContent = 'SEBAGIAN';
            } else {
                badge.classList.add('bg-yellow-100', 'text-yellow-700');
                badge.textContent = 'BELUM DIBAYAR';
            }
            statusEl.appendChild(badge);

            // Store info
            document.getElementById('modalStoreName').textContent = data.store_name;
            document.getElementById('modalStoreAddress').textContent = data.store_address;
            document.getElementById('modalStorePhone').textContent = 'Telp: ' + data.store_phone;

            // Items table
            const tbody = document.getElementById('modalItemsBody');
            tbody.replaceChildren();
            data.items.forEach(function(item, idx) {
                const tr = document.createElement('tr');
                tr.classList.add(idx % 2 === 0 ? 'bg-white' : 'bg-gray-50');

                const tdNo = document.createElement('td');
                tdNo.classList.add('px-4', 'py-3', 'text-center', 'text-gray-500');
                tdNo.textContent = idx + 1;
                tr.appendChild(tdNo);

                const tdDesc = document.createElement('td');
                tdDesc.classList.add('px-4', 'py-3', 'font-semibold', 'text-gray-800');
                tdDesc.textContent = item.description;
                tr.appendChild(tdDesc);

                const tdQty = document.createElement('td');
                tdQty.classList.add('px-4', 'py-3', 'text-center', 'font-semibold');
                tdQty.textContent = Number(item.quantity).toLocaleString('id-ID');
                tr.appendChild(tdQty);

                const tdPrice = document.createElement('td');
                tdPrice.classList.add('px-4', 'py-3', 'text-right', 'text-gray-600');
                tdPrice.textContent = formatRupiah(item.unit_price);
                tr.appendChild(tdPrice);

                const tdSubtotal = document.createElement('td');
                tdSubtotal.classList.add('px-4', 'py-3', 'text-right', 'font-bold', 'text-gray-800');
                tdSubtotal.textContent = formatRupiah(item.subtotal);
                tr.appendChild(tdSubtotal);

                tbody.appendChild(tr);
            });

            // Totals
            const totalsArea = document.getElementById('modalTotalsArea');
            totalsArea.replaceChildren();

            // Subtotal row
            const subtotalRow = createTotalRow('Subtotal', formatRupiah(data.subtotal), 'text-gray-600', 'text-gray-800');
            totalsArea.appendChild(subtotalRow);

            // Tax
            if (Number(data.tax_amount) > 0) {
                totalsArea.appendChild(createTotalRow('PPN', formatRupiah(data.tax_amount), 'text-gray-600', 'text-gray-800'));
            }
            // Discount
            if (Number(data.discount_amount) > 0) {
                totalsArea.appendChild(createTotalRow('Diskon', '- ' + formatRupiah(data.discount_amount), 'text-gray-600', 'text-red-600'));
            }

            // Grand total
            const grandDiv = document.createElement('div');
            grandDiv.classList.add('flex', 'justify-between', 'items-center', 'bg-gradient-to-r', 'from-[#1e3a8a]', 'to-[#2563eb]', 'text-white', 'rounded-lg', 'px-4', 'py-3', 'mt-1');
            const grandLabel = document.createElement('span');
            grandLabel.classList.add('font-bold', 'text-sm');
            grandLabel.textContent = 'TOTAL';
            grandDiv.appendChild(grandLabel);
            const grandValue = document.createElement('span');
            grandValue.classList.add('font-bold', 'text-lg');
            grandValue.textContent = formatRupiah(data.total_amount);
            grandDiv.appendChild(grandValue);
            totalsArea.appendChild(grandDiv);

            // Paid & Balance
            if (Number(data.paid_amount) > 0) {
                totalsArea.appendChild(createTotalRow('Dibayar', formatRupiah(data.paid_amount), 'text-green-600', 'text-green-600', true));
                const balance = Number(data.total_amount) - Number(data.paid_amount);
                if (balance > 0) {
                    totalsArea.appendChild(createTotalRow('Sisa Tagihan', formatRupiah(balance), 'text-red-600', 'text-red-600', true));
                }
            }

            // Notes
            const notesSection = document.getElementById('modalNotesSection');
            if (data.notes) {
                document.getElementById('modalNotes').textContent = data.notes;
                notesSection.classList.remove('hidden');
            } else {
                notesSection.classList.add('hidden');
            }

            // Print link
            document.getElementById('modalPrintLink').setAttribute('href', data.print_url);

            // Show modal with animation
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
            const modal = document.getElementById('invoiceModal');
            const backdrop = document.getElementById('invoiceBackdrop');
            const content = document.getElementById('invoiceModalContent');

            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');

            setTimeout(function() {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

        function createTotalRow(label, value, labelColor, valueColor, hasBorderTop) {
            const div = document.createElement('div');
            div.classList.add('flex', 'justify-between', 'px-4', 'py-2', 'text-sm');
            if (hasBorderTop) {
                div.classList.add('border-t', 'border-gray-200');
            }

            const labelSpan = document.createElement('span');
            labelSpan.classList.add('font-medium', labelColor);
            labelSpan.textContent = label;
            div.appendChild(labelSpan);

            const valueSpan = document.createElement('span');
            valueSpan.classList.add('font-bold', valueColor);
            valueSpan.textContent = value;
            div.appendChild(valueSpan);

            return div;
        }

        // Close with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeInvoicePreview();
            }
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>$(document).ready(function() { $('#doTable').DataTable({ "order": [[ 0, "desc" ]] }); });</script>
</x-app-layout>