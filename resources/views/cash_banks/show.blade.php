<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $cashBank->name }} <span class="text-sm font-normal text-gray-500">({{ $cashBank->type }})</span>
            </h2>
            <a href="{{ route('cash-banks.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">{{ session('success') }}</div>
            @endif

            <!-- Saldo Info -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6 border-l-4 {{ $cashBank->balance >= 0 ? 'border-green-500' : 'border-red-500' }}">
                <p class="text-sm text-gray-500">Saldo Saat Ini</p>
                <p class="text-3xl font-bold {{ $cashBank->balance >= 0 ? 'text-green-700' : 'text-red-700' }}">
                    Rp {{ number_format($cashBank->balance, 0, ',', '.') }}
                </p>
                @if($cashBank->type === 'Bank')
                    <p class="text-sm text-gray-500 mt-1">{{ $cashBank->bank_name }} - {{ $cashBank->account_number }}</p>
                @endif
            </div>

            <!-- Form Tambah Transaksi Manual -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4 text-blue-700 border-b pb-2">
                        <i class="ri-add-circle-line mr-1"></i> Tambah Transaksi Manual
                    </h3>
                    <form action="{{ route('cash-banks.transaction', $cashBank->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                            <div>
                                <select name="type" class="w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                                    <option value="Debit">Debit (Masuk)</option>
                                    <option value="Credit">Credit (Keluar)</option>
                                </select>
                            </div>
                            <div>
                                <input type="number" step="0.01" name="amount" class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Jumlah" required min="0.01">
                            </div>
                            <div>
                                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                            </div>
                            <div>
                                <select name="category" class="w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                                    <option value="Biaya Operasional">Biaya Operasional</option>
                                    <option value="Gaji">Gaji</option>
                                    <option value="Setoran Modal">Setoran Modal</option>
                                    <option value="Penarikan">Penarikan</option>
                                    <option value="Transfer Antar Akun">Transfer Antar Akun</option>
                                    <option value="Penjualan">Penjualan</option>
                                    <option value="Pembelian">Pembelian</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <input type="text" name="description" class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Deskripsi *" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mt-2">
                            <div class="md:col-span-4">
                                <input type="text" name="reference" class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Referensi (opsional)">
                            </div>
                            <div>
                                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-bold">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Daftar Transaksi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg text-blue-700">Daftar Transaksi</h3>
                        <!-- Rekonsiliasi -->
                        <form action="{{ route('cash-banks.reconcile', $cashBank->id) }}" method="POST" id="reconcileForm">
                            @csrf
                            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 text-sm font-bold" onclick="return confirm('Rekonsiliasi transaksi yang dicentang?');">
                                <i class="ri-check-double-line mr-1"></i> Rekonsiliasi Terpilih
                            </button>
                        </form>
                    </div>

                    <!-- Filter -->
                    <form method="GET" class="flex flex-wrap gap-2 mb-4 text-sm">
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="border-gray-300 rounded-md shadow-sm text-sm" placeholder="Dari">
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="border-gray-300 rounded-md shadow-sm text-sm" placeholder="Sampai">
                        <select name="reconciled" class="border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">Semua Status</option>
                            <option value="no" {{ request('reconciled') === 'no' ? 'selected' : '' }}>Belum Rekonsiliasi</option>
                            <option value="yes" {{ request('reconciled') === 'yes' ? 'selected' : '' }}>Sudah Rekonsiliasi</option>
                        </select>
                        <button type="submit" class="bg-gray-600 text-white px-3 py-1.5 rounded text-sm">Filter</button>
                    </form>

                    <div class="overflow-x-auto w-full">
                    <table class="w-full table-auto border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-2 py-2 w-8"><input type="checkbox" id="checkAll"></th>
                                <th class="border px-4 py-2">Tanggal</th>
                                <th class="border px-4 py-2">Deskripsi</th>
                                <th class="border px-4 py-2">Kategori</th>
                                <th class="border px-4 py-2">Referensi</th>
                                <th class="border px-4 py-2 text-right">Debit (Masuk)</th>
                                <th class="border px-4 py-2 text-right">Credit (Keluar)</th>
                                <th class="border px-4 py-2 text-right">Saldo</th>
                                <th class="border px-4 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                            <tr class="{{ $trx->is_reconciled ? 'bg-green-50' : '' }}">
                                <td class="border px-2 py-2 text-center">
                                    @if(!$trx->is_reconciled)
                                        <input type="checkbox" name="transaction_ids[]" value="{{ $trx->id }}" form="reconcileForm" class="trx-check">
                                    @else
                                        <i class="ri-check-line text-green-600"></i>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-center">{{ $trx->transaction_date->format('d/m/Y') }}</td>
                                <td class="border px-4 py-2">{{ $trx->description }}</td>
                                <td class="border px-4 py-2"><span class="bg-gray-100 text-gray-700 text-xs px-2 py-0.5 rounded">{{ $trx->category }}</span></td>
                                <td class="border px-4 py-2 text-sm text-gray-500">{{ $trx->reference ?? '-' }}</td>
                                <td class="border px-4 py-2 text-right {{ $trx->type === 'Debit' ? 'text-green-600 font-bold' : '' }}">
                                    {{ $trx->type === 'Debit' ? 'Rp ' . number_format($trx->amount, 0, ',', '.') : '' }}
                                </td>
                                <td class="border px-4 py-2 text-right {{ $trx->type === 'Credit' ? 'text-red-600 font-bold' : '' }}">
                                    {{ $trx->type === 'Credit' ? 'Rp ' . number_format($trx->amount, 0, ',', '.') : '' }}
                                </td>
                                <td class="border px-4 py-2 text-right font-bold">Rp {{ number_format($trx->balance_after, 0, ',', '.') }}</td>
                                <td class="border px-4 py-2 text-center">
                                    @if($trx->is_reconciled)
                                        <span class="bg-green-200 text-green-800 px-2 py-0.5 rounded text-xs">✓ Cocok</span>
                                    @else
                                        <span class="bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded text-xs">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="border px-4 py-8 text-center text-gray-500">Belum ada transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('checkAll')?.addEventListener('change', function() {
            document.querySelectorAll('.trx-check').forEach(cb => cb.checked = this.checked);
        });
    </script>
</x-app-layout>
