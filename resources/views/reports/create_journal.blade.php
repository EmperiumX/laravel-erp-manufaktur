<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Buat Jurnal Voucher Manual') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Catat transaksi manual debit/kredit langsung ke akun Kas & Bank</p>
            </div>
            <a href="{{ route('reports.general-journal') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm transition">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    

                    <form method="POST" action="{{ route('reports.store-journal') }}" class="space-y-6">
                        @csrf

                        <!-- Akun Kas / Bank -->
                        <div>
                            <label for="cash_bank_id" class="block text-sm font-bold text-gray-700 mb-1">Pilih Akun Kas & Bank <span class="text-red-500">*</span></label>
                            <select id="cash_bank_id" name="cash_bank_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">-- Pilih Akun --</option>
                                @foreach($cashBanks as $cb)
                                    <option value="{{ $cb->id }}" {{ old('cash_bank_id') == $cb->id ? 'selected' : '' }}>
                                        {{ $cb->name }} (Saldo Saat Ini: Rp {{ number_format($cb->balance, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih akun kas atau bank yang saldonya akan terpengaruh secara langsung oleh transaksi ini.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Jenis Transaksi -->
                            <div>
                                <label for="type" class="block text-sm font-bold text-gray-700 mb-1">Jenis Mutasi Jurnal <span class="text-red-500">*</span></label>
                                <select id="type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="Debit" {{ old('type') == 'Debit' ? 'selected' : '' }}>DEBIT (Uang Masuk / Menambah Saldo)</option>
                                    <option value="Credit" {{ old('type') == 'Credit' ? 'selected' : '' }}>KREDIT (Uang Keluar / Mengurangi Saldo)</option>
                                </select>
                            </div>

                            <!-- Tanggal Transaksi -->
                            <div>
                                <label for="transaction_date" class="block text-sm font-bold text-gray-700 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                                <input type="date" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Jumlah (Amount) -->
                            <div>
                                <label for="amount" class="block text-sm font-bold text-gray-700 mb-1">Jumlah Transaksi (Rupiah) <span class="text-red-500">*</span></label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" step="0.01" min="0.01" name="amount" id="amount" value="{{ old('amount') }}" required class="focus:border-indigo-500 focus:ring-indigo-500 block w-full pl-10 pr-3 sm:text-sm border-gray-300 rounded-md" placeholder="0">
                                </div>
                            </div>

                            <!-- Kategori Transaksi -->
                            <div>
                                <label for="category" class="block text-sm font-bold text-gray-700 mb-1">Kategori Jurnal / Biaya <span class="text-red-500">*</span></label>
                                <input type="text" id="category" name="category" value="{{ old('category') }}" required list="categorySuggestions" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Operasional, Gaji, Sewa, Penyesuaian">
                                <datalist id="categorySuggestions">
                                    <option value="Operasional">
                                    <option value="Gaji & Staf">
                                    <option value="Sewa Tempat">
                                    <option value="Peralatan & Aset">
                                    <option value="Penyusutan">
                                    <option value="Modal Tambahan">
                                    <option value="Penyesuaian Saldo">
                                    <option value="Biaya Listrik & Air">
                                </datalist>
                                <p class="text-xs text-gray-500 mt-1">Kategori pengeluaran atau pendapatan. Anda bisa mengetik langsung atau memilih opsi.</p>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label for="description" class="block text-sm font-bold text-gray-700 mb-1">Deskripsi / Keterangan Transaksi <span class="text-red-500">*</span></label>
                            <textarea id="description" name="description" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Tuliskan alasan atau keterangan lengkap mengenai mutasi jurnal ini...">{{ old('description') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Maksimal 255 karakter. Tulis secara jelas sebagai bukti audit keuangan.</p>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="flex justify-end gap-4 border-t pt-6">
                            <a href="{{ route('reports.general-journal') }}" class="bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-200 text-sm font-bold transition">
                                Batal
                            </a>
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 text-sm font-bold transition shadow-md">
                                Simpan Jurnal Voucher
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
