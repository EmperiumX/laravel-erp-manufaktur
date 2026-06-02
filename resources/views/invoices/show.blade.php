<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Faktur / Invoice: <span class="text-indigo-600 font-bold">{{ $invoice->invoice_number }}</span>
                </h2>
                <p class="text-xs text-gray-500 mt-1">Dibuat oleh: {{ $invoice->creator->name ?? '-' }}</p>
            </div>
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-xl text-sm font-semibold shadow-sm transition">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- KIRI: Invoice Document (Span 2) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        
                        <!-- Top Accent line -->
                        <div class="h-2 bg-gradient-to-r {{ $invoice->type === 'sales' ? 'from-blue-500 to-indigo-600' : 'from-rose-500 to-red-600' }}"></div>
                        
                        <div class="p-6 sm:p-8">
                            <!-- Header / Letterhead -->
                            <div class="flex flex-col sm:flex-row justify-between border-b pb-6 gap-4">
                                <div>
                                    <div class="text-red-600 font-extrabold text-2xl tracking-wide">CV. NEW CITRA INDONESIA</div>
                                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                        Jl. Kedungmundu Raya No. 161A Tembalang<br>
                                        Semarang, Jawa Tengah 50273<br>
                                        Telp: 085866228323
                                    </p>
                                </div>
                                <div class="sm:text-right">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Jenis Invoice</span>
                                    <h1 class="text-xl font-black mt-0.5 {{ $invoice->type === 'sales' ? 'text-blue-600' : 'text-rose-600' }}">
                                        {{ $invoice->type === 'sales' ? 'INVOICE PENJUALAN' : 'INVOICE PEMBELIAN' }}
                                    </h1>
                                    <p class="text-xs text-gray-400 mt-1">Tanggal: {{ $invoice->invoice_date->format('d F Y') }}</p>
                                    <p class="text-xs text-gray-400">Jatuh Tempo: <span class="font-semibold {{ $invoice->is_overdue ? 'text-red-500' : '' }}">{{ $invoice->due_date->format('d F Y') }}</span></p>
                                </div>
                            </div>

                            <!-- Parties (Billing details) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 border-b text-sm">
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Penerbit</p>
                                    <p class="font-bold text-gray-800">CV. New Citra Indonesia</p>
                                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Jl. Kedungmundu Raya No. 161A, Tembalang, Semarang, Jawa Tengah</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                        {{ $invoice->type === 'sales' ? 'Tagihan Kepada' : 'Tagihan Dari' }}
                                    </p>
                                    @if($invoice->type === 'sales')
                                        <p class="font-bold text-gray-800">{{ $invoice->store->name ?? ($invoice->directSale->customer_name ?? 'Pelanggan Umum') }}</p>
                                        @if($invoice->store)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $invoice->store->category }}</p>
                                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $invoice->store->address }}</p>
                                        @else
                                            <p class="text-xs text-gray-500 mt-0.5">Penjualan Langsung / Eceran</p>
                                        @endif
                                    @else
                                        <p class="font-bold text-gray-800">{{ $invoice->supplier->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $invoice->supplier->address ?? '-' }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Items Table -->
                            <div class="py-6">
                                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider mb-4">Rincian Item</h3>
                                <div class="overflow-x-auto rounded-xl border border-gray-100">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
                                                <th class="px-4 py-3 text-center font-bold w-12">No</th>
                                                <th class="px-4 py-3 text-left font-bold">Deskripsi</th>
                                                <th class="px-4 py-3 text-center font-bold w-20">Qty</th>
                                                <th class="px-4 py-3 text-center font-bold w-20">Satuan</th>
                                                <th class="px-4 py-3 text-right font-bold w-32">Harga</th>
                                                <th class="px-4 py-3 text-right font-bold w-36">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($invoice->items as $index => $item)
                                            <tr class="hover:bg-gray-50/50 transition">
                                                <td class="px-4 py-3.5 text-center text-gray-400">{{ $index + 1 }}</td>
                                                <td class="px-4 py-3.5 font-semibold text-gray-800">{{ $item->description }}</td>
                                                <td class="px-4 py-3.5 text-center font-semibold text-gray-800 font-mono">{{ rtrim(rtrim(number_format($item->quantity, 4, ',', '.'), '0'), ',') }}</td>
                                                <td class="px-4 py-3.5 text-center text-gray-500">{{ $item->unit }}</td>
                                                <td class="px-4 py-3.5 text-right text-gray-600">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3.5 text-right font-bold text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Financial Summary -->
                            <div class="flex justify-end pt-4 border-t">
                                <div class="w-full sm:w-80 space-y-2">
                                    <div class="flex justify-between text-sm px-2">
                                        <span class="text-gray-500 font-medium">Subtotal</span>
                                        <span class="font-bold text-gray-800">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    @if($invoice->tax_amount > 0)
                                    <div class="flex justify-between text-sm px-2">
                                        <span class="text-gray-500 font-medium">PPN</span>
                                        <span class="font-bold text-gray-800">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
                                    </div>
                                    @endif
                                    @if($invoice->discount_amount > 0)
                                    <div class="flex justify-between text-sm px-2">
                                        <span class="text-gray-500 font-medium">Diskon</span>
                                        <span class="font-bold text-red-600">- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</span>
                                    </div>
                                    @endif
                                    
                                    <div class="flex justify-between items-center bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                        <span class="font-bold text-sm text-gray-700">Total Tagihan</span>
                                        <span class="font-black text-lg text-indigo-700">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                                    </div>

                                    @if($invoice->paid_amount > 0)
                                    <div class="flex justify-between text-sm px-4">
                                        <span class="text-green-600 font-semibold">Sudah Dibayar</span>
                                        <span class="font-bold text-green-600">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                                    </div>
                                    @endif

                                    @if($invoice->balance_due > 0)
                                    <div class="flex justify-between items-center bg-rose-50 rounded-xl px-4 py-2.5 border border-rose-100">
                                        <span class="font-bold text-xs text-rose-700">Sisa Tagihan</span>
                                        <span class="font-black text-sm text-rose-600">Rp {{ number_format($invoice->balance_due, 0, ',', '.') }}</span>
                                    </div>
                                    @else
                                    <div class="flex justify-between items-center bg-green-50 rounded-xl px-4 py-2.5 border border-green-100">
                                        <span class="font-bold text-xs text-green-700">Sisa Tagihan</span>
                                        <span class="font-black text-sm text-green-600">✓ LUNAS</span>
                                    </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- KANAN: Status, Actions & Payment History -->
                <div class="space-y-6">
                    
                    <!-- Status & Actions Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider mb-4 border-b pb-2">Status & Aksi</h3>
                        
                        <div class="mb-6">
                            <span class="text-xs text-gray-400 font-bold block uppercase mb-1">Status Saat Ini</span>
                            @switch($invoice->status)
                                @case('Draft')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 text-gray-700 rounded-xl text-xs font-bold border border-gray-200">
                                        <span class="w-2.5 h-2.5 bg-gray-400 rounded-full"></span> Draft
                                    </span>
                                    @break
                                @case('Sent')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-xl text-xs font-bold border border-blue-200">
                                        <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span> Terkirim
                                    </span>
                                    @break
                                @case('Partial')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-xl text-xs font-bold border border-amber-200">
                                        <span class="w-2.5 h-2.5 bg-amber-500 rounded-full"></span> Sebagian
                                    </span>
                                    @break
                                @case('Paid')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-700 rounded-xl text-xs font-bold border border-green-200">
                                        <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span> Lunas
                                    </span>
                                    @break
                                @case('Canceled')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-700 rounded-xl text-xs font-bold border border-rose-200">
                                        <span class="w-2.5 h-2.5 bg-rose-500 rounded-full"></span> Batal
                                    </span>
                                    @break
                            @endswitch
                            
                            @if($invoice->is_overdue)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-100 text-red-700 rounded-xl text-xs font-bold border border-red-200 ml-1">
                                    <span class="w-2.5 h-2.5 bg-red-600 rounded-full animate-pulse"></span> Terlambat
                                </span>
                            @endif
                        </div>

                        <div class="space-y-3">
                            <!-- Cetak PDF -->
                            <a href="{{ route('invoices.print', $invoice->id) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Cetak Invoice (PDF)
                            </a>

                            @if($invoice->status === 'Draft')
                                <form action="{{ route('invoices.send', $invoice->id) }}" method="POST" class="w-full">
                                    @csrf
                                    <button class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow transition">
                                        <i class="ri-send-plane-line"></i> Kirim Invoice
                                    </button>
                                </form>
                                <form action="{{ route('invoices.cancel', $invoice->id) }}" method="POST" class="w-full" onsubmit="return confirm('Yakin ingin membatalkan invoice ini?');">
                                    @csrf
                                    <button class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl font-bold text-sm transition">
                                        <i class="ri-close-line"></i> Batalkan Invoice
                                    </button>
                                </form>
                            @endif

                            @if(in_array($invoice->status, ['Sent', 'Partial']))
                                <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-xl font-bold text-sm shadow-md transition">
                                    <i class="ri-money-dollar-circle-line text-lg"></i> Catat Pembayaran
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Payment History Card -->
                    @if($invoice->payments->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider mb-4 border-b pb-2">Riwayat Pembayaran</h3>
                        <div class="space-y-4">
                            @foreach($invoice->payments as $payment)
                            <div class="bg-gray-50 border border-gray-100 rounded-xl p-3.5 space-y-2 relative hover:bg-gray-100/50 transition">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-xs text-indigo-600">{{ $payment->payment_number }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $payment->payment_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-600">
                                    <span>Metode: {{ $payment->payment_method }}</span>
                                    <span>Akun: {{ $payment->cashBank->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t border-gray-200/50">
                                    <span class="text-[10px] text-gray-400">Oleh: {{ $payment->creator->name ?? '-' }}</span>
                                    <span class="font-bold text-green-600 text-sm">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 text-center text-gray-400 text-xs py-8">
                        <i class="ri-bank-card-line text-3xl block mb-2 text-gray-300"></i>
                        Belum ada riwayat pembayaran yang tercatat.
                    </div>
                    @endif

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
