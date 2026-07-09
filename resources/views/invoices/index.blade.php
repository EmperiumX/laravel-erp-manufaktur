<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat & Daftar Invoice / Faktur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-[1.01] transition-all duration-150">
                    <p class="text-blue-100 text-xs font-bold uppercase tracking-wider">Total Piutang (Belum Lunas)</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</p>
                    <div class="mt-4 flex items-center text-xs text-blue-200">
                        <i class="ri-information-line mr-1"></i>
                        <span>Invoice penjualan toko/mitra</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-[1.01] transition-all duration-150">
                    <p class="text-rose-100 text-xs font-bold uppercase tracking-wider">Total Hutang (Belum Lunas)</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($totalHutang, 0, ',', '.') }}</p>
                    <div class="mt-4 flex items-center text-xs text-rose-200">
                        <i class="ri-information-line mr-1"></i>
                        <span>Tagihan dari supplier bahan baku</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-[1.01] transition-all duration-150">
                    <p class="text-amber-100 text-xs font-bold uppercase tracking-wider">Total Jatuh Tempo (Overdue)</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($totalOverdue, 0, ',', '.') }}</p>
                    <div class="mt-4 flex items-center text-xs text-amber-200">
                        <i class="ri-error-warning-line mr-1"></i>
                        <span>Invoice yang melewati batas bayar</span>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-md sm:rounded-2xl border border-gray-100">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-r-xl relative mb-6">
                            <span class="block sm:inline font-medium">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-r-xl relative mb-6">
                            <span class="block sm:inline font-medium">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="mb-6 flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
                        <div>
                            <span class="text-sm text-gray-500 font-medium">
                                <i class="ri-information-line mr-1 text-indigo-500"></i>
                                Klik tombol <strong>Detail</strong> atau ikon printer 🖨️ untuk melihat rincian dan mencetak ulang dokumen invoice.
                            </span>
                        </div>
                        <div class="bg-gray-100 p-1 rounded-xl flex gap-1">
                            <a href="{{ route('invoices.index') }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ !$type ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-800' }}">Semua</a>
                            <a href="{{ route('invoices.index', ['type' => 'sales']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $type === 'sales' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 hover:text-blue-600' }}">Piutang</a>
                            <a href="{{ route('invoices.index', ['type' => 'purchase']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $type === 'purchase' ? 'bg-red-600 text-white shadow-sm' : 'text-gray-500 hover:text-red-600' }}">Hutang</a>
                        </div>
                    </div>

                    <div class="overflow-x-auto w-full">
                        <table id="invoiceTable" class="w-full table-auto border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider border-b border-gray-200">
                                <th class="px-4 py-3 text-center font-bold">Tanggal</th>
                                <th class="px-4 py-3 text-left font-bold">No. Invoice</th>
                                <th class="px-4 py-3 text-center font-bold">Tipe</th>
                                <th class="px-4 py-3 text-left font-bold">Pihak</th>
                                <th class="px-4 py-3 text-right font-bold">Total</th>
                                <th class="px-4 py-3 text-right font-bold">Dibayar</th>
                                <th class="px-4 py-3 text-right font-bold">Sisa</th>
                                <th class="px-4 py-3 text-center font-bold">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-center font-bold">Status</th>
                                <th class="px-4 py-3 text-center font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($invoices as $inv)
                            <tr class="hover:bg-gray-50/80 transition duration-150">
                                <td class="px-4 py-3.5 text-center text-gray-500">{{ $inv->invoice_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3.5 font-bold text-gray-900">
                                    {{ $inv->invoice_number }}
                                    @if($inv->consignmentShipment)
                                        <span class="block text-[10px] text-blue-600 font-semibold mt-0.5" title="Nomor Surat Jalan / DO">DO: {{ $inv->consignmentShipment->shipment_number }}</span>
                                    @elseif($inv->purchaseOrder)
                                        <span class="block text-[10px] text-rose-600 font-semibold mt-0.5" title="Nomor Purchase Order">PO: {{ $inv->purchaseOrder->po_number }}</span>
                                    @elseif($inv->directSale)
                                        <span class="block text-[10px] text-indigo-600 font-semibold mt-0.5" title="Nomor Nota Penjualan">Direct: {{ $inv->directSale->invoice_number }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($inv->type === 'sales')
                                        <span class="bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-md text-xs font-bold">Piutang</span>
                                    @else
                                        <span class="bg-red-50 text-red-700 border border-red-200 px-2 py-0.5 rounded-md text-xs font-bold">Hutang</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-gray-700 font-medium">
                                    @if($inv->type === 'sales')
                                        {{ $inv->store->name ?? ($inv->directSale->customer_name ?? 'Pelanggan Umum') }}
                                    @else
                                        {{ $inv->supplier->name ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right font-bold text-gray-800">Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3.5 text-right text-green-600 font-medium">Rp {{ number_format($inv->paid_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3.5 text-right font-bold {{ $inv->balance_due > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    Rp {{ number_format($inv->balance_due, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 text-center {{ $inv->is_overdue ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                    {{ $inv->due_date->format('d/m/Y') }}
                                    @if($inv->is_overdue) <span class="bg-red-100 text-red-700 text-[10px] px-1.5 py-0.5 rounded font-bold block mt-1">LEWAT!</span> @endif
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @switch($inv->status)
                                        @case('Draft')
                                            <span class="bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full text-xs font-bold">Draft</span>
                                            @break
                                        @case('Sent')
                                            <span class="bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full text-xs font-bold">Terkirim</span>
                                            @break
                                        @case('Partial')
                                            <span class="bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full text-xs font-bold">Sebagian</span>
                                            @break
                                        @case('Paid')
                                            <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-bold">✓ Lunas</span>
                                            @break
                                        @case('Canceled')
                                            <span class="bg-rose-100 text-rose-700 px-2.5 py-1 rounded-full text-xs font-bold">Batal</span>
                                            @break
                                        @default
                                            <span class="bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full text-xs font-bold">{{ $inv->status }}</span>
                                    @endswitch
                                </td>
                                <td class="px-4 py-3.5 text-center font-bold">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('invoices.show', $inv->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold transition">
                                            Detail
                                        </a>
                                        <a href="{{ route('invoices.print', $inv->id) }}" target="_blank" class="inline-flex items-center justify-center p-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg transition" title="Cetak PDF">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        </a>
                                    </div>
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
    <script>
        $(document).ready(function() { 
            $('#invoiceTable').DataTable({ 
                "order": [[ 0, "desc" ]],
                "pageLength": 10,
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ invoice",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                }
            }); 
        });
    </script>
</x-app-layout>
