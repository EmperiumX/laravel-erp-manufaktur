<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Pembayaran: <span class="text-green-600">{{ $payment->payment_number }}</span></h2>
            <a href="{{ route('payments.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><span class="text-gray-500 text-sm">No. Payment</span><p class="font-bold">{{ $payment->payment_number }}</p></div>
                        <div><span class="text-gray-500 text-sm">Tanggal</span><p class="font-bold">{{ $payment->payment_date->format('d F Y') }}</p></div>
                        <div><span class="text-gray-500 text-sm">Tipe</span><p class="font-bold">{{ $payment->type === 'inbound' ? 'Masuk (Piutang)' : 'Keluar (Hutang)' }}</p></div>
                        <div><span class="text-gray-500 text-sm">Jumlah</span><p class="font-bold text-lg {{ $payment->type === 'inbound' ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p></div>
                        <div><span class="text-gray-500 text-sm">Metode</span><p class="font-bold">{{ $payment->payment_method }}</p></div>
                        <div><span class="text-gray-500 text-sm">Kas/Bank</span><p class="font-bold">{{ $payment->cashBank->full_name ?? '-' }}</p></div>
                        <div><span class="text-gray-500 text-sm">Invoice</span><p><a href="{{ route('invoices.show', $payment->invoice_id) }}" class="font-bold text-blue-600 hover:underline">{{ $payment->invoice->invoice_number }}</a></p></div>
                        <div><span class="text-gray-500 text-sm">Referensi</span><p class="font-bold">{{ $payment->reference ?? '-' }}</p></div>
                        <div class="col-span-2"><span class="text-gray-500 text-sm">Catatan</span><p>{{ $payment->notes ?? '-' }}</p></div>
                        <div><span class="text-gray-500 text-sm">Dicatat Oleh</span><p>{{ $payment->creator->name ?? '-' }}</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
