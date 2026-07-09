<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buka Sesi Kasir Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-indigo-100">
                <div class="p-6 text-gray-900">
                    <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-6">
                        <p class="text-sm text-indigo-700 font-semibold">
                            <i class="ri-information-fill mr-1"></i>
                            Sebelum melayani transaksi penjualan langsung, Anda wajib membuka Sesi Kasir untuk mencatat saldo kas awal pada laci kasir Anda.
                        </p>
                    </div>

                    <form action="{{ route('cashier-sessions.store-open') }}" method="POST">
                        @csrf

                        <!-- Akun Kas / Laci -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Akun Kas / Laci Kasir <span class="text-red-500">*</span></label>
                            <select name="cash_bank_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">-- Pilih Akun Kasir --</option>
                                @foreach($cashBanks as $cashBank)
                                    <option value="{{ $cashBank->id }}" {{ $cashBank->name === 'Kas Kasir' ? 'selected' : '' }}>
                                        {{ $cashBank->full_name }} (Saldo: Rp {{ number_format($cashBank->balance, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Uang hasil penjualan kasir akan otomatis disinkronisasikan ke akun kas/laci yang Anda pilih di sini.</p>
                        </div>

                        <!-- Saldo Kas Awal -->
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Saldo Awal Kasir (Uang Kembalian / Modal Laci) <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <span class="bg-gray-200 border border-gray-300 text-gray-600 px-3 py-2 rounded-l-md text-sm font-bold">Rp</span>
                                <input type="number" name="opening_cash" value="250000" class="w-full border-gray-300 rounded-r-md shadow-sm" required min="0" placeholder="Masukkan jumlah modal laci kasir...">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Masukkan total uang tunai fisik yang ada di dalam laci kasir saat shift Anda dimulai.</p>
                        </div>

                        <!-- Catatan Pembukaan -->
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Catatan Pembukaan (Opsional)</label>
                            <textarea name="notes" placeholder="Cth: Shift Pagi, Kasir: Zidan..." class="w-full border-gray-300 rounded-md shadow-sm h-20"></textarea>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t">
                            <a href="{{ route('cashier-sessions.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 font-bold shadow-lg transition">
                                Buka Sesi Kasir & Mulai Tugas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
