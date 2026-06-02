<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Catat Pembayaran') }}</h2>
            <a href="{{ route('payments.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">{{ session('error') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if($invoice)
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                            <p class="text-sm text-blue-700">
                                Pembayaran untuk invoice <strong>{{ $invoice->invoice_number }}</strong><br>
                                Pihak: <strong>{{ $invoice->type === 'sales' ? ($invoice->store->name ?? '-') : ($invoice->supplier->name ?? '-') }}</strong><br>
                                Sisa Tagihan: <strong class="text-red-600">Rp {{ number_format($invoice->balance_due, 0, ',', '.') }}</strong>
                            </p>
                        </div>
                    @endif

                    <form action="{{ route('payments.store') }}" method="POST">
                        @csrf

                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Invoice <span class="text-red-500">*</span></label>
                                @if($invoice)
                                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                                    <input type="text" value="{{ $invoice->invoice_number }} - Rp {{ number_format($invoice->balance_due, 0, ',', '.') }}" class="w-full border-gray-300 rounded-md shadow-sm bg-gray-100" disabled>
                                @else
                                    <select name="invoice_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                        <option value="">-- Pilih Invoice --</option>
                                        @foreach($unpaidInvoices as $inv)
                                            <option value="{{ $inv->id }}">
                                                {{ $inv->invoice_number }} - {{ $inv->type === 'sales' ? ($inv->store->name ?? '') : ($inv->supplier->name ?? '') }} - Sisa: Rp {{ number_format($inv->balance_due, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Jumlah Pembayaran (Rp) <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" name="amount" value="{{ $invoice ? $invoice->balance_due : '' }}" class="w-full border-gray-300 rounded-md shadow-sm" required min="0.01">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Pembayaran <span class="text-red-500">*</span></label>
                                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Metode Pembayaran <span class="text-red-500">*</span></label>
                                    <select name="payment_method" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                        <option value="Transfer">Transfer Bank</option>
                                        <option value="Cash">Tunai (Cash)</option>
                                        <option value="Giro">Giro</option>
                                        <option value="Cek">Cek</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Akun Kas/Bank</label>
                                    <select name="cash_bank_id" class="w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">-- Tidak Dicatat di Kas/Bank --</option>
                                        @foreach($cashBanks as $cb)
                                            <option value="{{ $cb->id }}">{{ $cb->full_name }} (Saldo: Rp {{ number_format($cb->balance, 0, ',', '.') }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Referensi (No. Transfer / No. Giro)</label>
                                <input type="text" name="reference" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Opsional">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Catatan</label>
                                <input type="text" name="notes" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Opsional">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-8 border-t pt-4">
                            <a href="{{ route('payments.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
                            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 font-bold text-lg">
                                <i class="ri-money-dollar-circle-line mr-1"></i> Simpan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
