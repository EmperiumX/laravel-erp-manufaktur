<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sesi Kasir: ') }} <span class="text-indigo-600">#{{ $cashierSession->id }}</span>
            </h2>
            <a href="{{ route('cashier-sessions.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                
                <!-- BAGIAN KIRI: RINGKASAN SESI -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 lg:col-span-2">
                    <h3 class="font-bold text-lg mb-4 border-b pb-2 text-indigo-700 flex justify-between items-center">
                        <span>Ringkasan Sesi Kasir</span>
                        @if($cashierSession->status === 'Open')
                            <span class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase">Sesi Aktif</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-0.5 rounded-full uppercase">Sesi Ditutup</span>
                        @endif
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-6">
                        <div>
                            <p class="mb-2"><span class="text-gray-500">Staf Kasir</span>: <span class="font-bold">{{ $cashierSession->user->name }}</span></p>
                            <p class="mb-2"><span class="text-gray-500">Akun Kas Laci</span>: <span class="font-bold">{{ $cashierSession->cashBank->name }}</span></p>
                            <p class="mb-2"><span class="text-gray-500">Catatan Buka</span>: <span class="text-gray-700 font-semibold">{{ $cashierSession->notes ?? '-' }}</span></p>
                        </div>
                        <div>
                            <p class="mb-2"><span class="text-gray-500">Waktu Buka</span>: <span class="font-bold">{{ $cashierSession->opening_date ? $cashierSession->opening_date->format('d F Y H:i') : '-' }}</span></p>
                            @if($cashierSession->status === 'Closed')
                                <p class="mb-2"><span class="text-gray-500">Waktu Tutup</span>: <span class="font-bold">{{ $cashierSession->closing_date ? $cashierSession->closing_date->format('d F Y H:i') : '-' }}</span></p>
                            @endif
                        </div>
                    </div>

                    <h4 class="font-bold text-sm text-gray-600 mb-3 uppercase border-b pb-1">Rincian Finansial</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div class="bg-gray-50 p-3 rounded border">
                            <span class="text-xs text-gray-500 block uppercase">Saldo Awal</span>
                            <span class="text-md font-bold font-mono text-gray-700">Rp {{ number_format($cashierSession->opening_cash, 0, ',', '.') }}</span>
                        </div>
                        <div class="bg-blue-50 p-3 rounded border border-blue-100">
                            <span class="text-xs text-blue-600 block uppercase">Total Penjualan</span>
                            <span class="text-md font-bold font-mono text-blue-700">Rp {{ number_format($cashierSession->directSales->sum('total_amount'), 0, ',', '.') }}</span>
                        </div>
                        <div class="bg-green-50 p-3 rounded border border-green-100">
                            <span class="text-xs text-green-600 block uppercase">Kas Sistem (Expected)</span>
                            <span class="text-md font-bold font-mono text-green-700">Rp {{ number_format($cashierSession->expected_cash, 0, ',', '.') }}</span>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded border border-yellow-100">
                            <span class="text-xs text-yellow-600 block uppercase">Fisik Laci (Closing)</span>
                            <span class="text-md font-bold font-mono text-yellow-700">
                                {{ $cashierSession->closing_cash !== null ? 'Rp ' . number_format($cashierSession->closing_cash, 0, ',', '.') : 'Belum Input' }}
                            </span>
                        </div>
                    </div>

                    @if($cashierSession->status === 'Closed')
                        <div class="mt-4 p-4 rounded border {{ $cashierSession->difference == 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                            <h5 class="font-bold text-sm {{ $cashierSession->difference == 0 ? 'text-green-800' : 'text-red-800' }} uppercase mb-1">Status Rekonsiliasi Tutup Kasir</h5>
                            @if($cashierSession->difference == 0)
                                <p class="text-sm text-green-700 font-semibold">Kas Seimbang (Sesuai). Tidak ada selisih saldo laci kasir.</p>
                            @else
                                <p class="text-sm text-red-700 font-bold">
                                    Terdapat SELISIH KAS sebesar 
                                    @if($cashierSession->difference > 0)
                                        <span class="underline">Uang Lebih (+Rp {{ number_format($cashierSession->difference, 0, ',', '.') }})</span>
                                    @else
                                        <span class="underline">Uang Kurang (-Rp {{ number_format(abs($cashierSession->difference), 0, ',', '.') }})</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- BAGIAN KANAN: FORM PENUTUPAN KASIR -->
                <div class="lg:col-span-1">
                    @if($cashierSession->status === 'Open')
                        <div class="bg-yellow-50 p-6 rounded-lg shadow-sm border border-yellow-200 sticky top-4">
                            <h3 class="font-bold text-lg mb-4 border-b pb-2 text-yellow-800 uppercase flex items-center gap-1">
                                🔒 Tutup Shift Kasir
                            </h3>
                            
                            <form action="{{ route('cashier-sessions.close', $cashierSession->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menutup sesi kasir ini? Tindakan ini tidak bisa dibatalkan.');">
                                @csrf
                                
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Total Uang Fisik di Laci Kasir <span class="text-red-500">*</span></label>
                                    <div class="flex">
                                        <span class="bg-gray-200 border border-gray-300 text-gray-600 px-3 py-2 rounded-l-md text-sm font-bold">Rp</span>
                                        <input type="number" name="closing_cash" class="w-full border-gray-300 rounded-r-md shadow-sm font-bold" required min="0" placeholder="Hitung uang fisik di laci...">
                                    </div>
                                    <p class="text-xs text-yellow-700 mt-2">
                                        * Hitung secara fisik seluruh uang kertas dan koin di laci kasir Anda saat shift berakhir. Sistem akan membandingkannya dengan saldo yang tercatat di sistem.
                                    </p>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Catatan Penutupan / Selisih</label>
                                    <textarea name="notes" placeholder="Tulis catatan jika ada selisih kas atau informasi penting lain..." class="w-full border-gray-300 rounded-md text-sm shadow-sm h-20"></textarea>
                                </div>

                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-bold text-center shadow-lg transition">
                                    Selesaikan Shift & Tutup Kasir
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-200 text-center text-gray-500">
                            <p class="font-bold text-lg mb-2">Sesi Sudah Ditutup</p>
                            <p class="text-sm">Sesi kasir ini telah diselesaikan dan dikunci untuk audit keuangan.</p>
                            @if(Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Admin'))
                                <a href="{{ route('cashier-sessions.create') }}" class="mt-4 inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-bold transition">
                                    Buka Sesi Baru
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

            </div>

            <!-- BAGIAN BAWAH: DAFTAR TRANSAKSI -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="font-bold text-lg mb-4 border-b pb-2 text-indigo-700 uppercase">
                    Daftar Penjualan Selama Sesi
                </h3>

                <div class="overflow-x-auto w-full">
                    <table class="w-full table-auto border-collapse border border-gray-300 text-sm whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">Waktu Transaksi</th>
                                <th class="border px-4 py-2">No. Nota / Invoice</th>
                                <th class="border px-4 py-2">Nama Pembeli</th>
                                <th class="border px-4 py-2 text-right">Total Belanja (Rp)</th>
                                <th class="border px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cashierSession->directSales as $sale)
                            <tr>
                                <td class="border px-4 py-2 text-center">{{ $sale->created_at->format('H:i') }}</td>
                                <td class="border px-4 py-2 font-bold text-gray-700">{{ $sale->invoice_number }}</td>
                                <td class="border px-4 py-2 text-green-800 font-bold">
                                    @if($sale->store_id)
                                        {{ $sale->store->name }} <span class="text-xs text-gray-500 font-normal">(Terdaftar)</span>
                                    @else
                                        {{ $sale->customer_name }} <span class="text-xs text-gray-500 font-normal">(Umum)</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-right font-bold">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                <td class="border px-4 py-2 text-center">
                                    <a href="{{ route('direct-sales.print', $sale->id) }}" target="_blank" class="text-indigo-600 hover:underline text-xs font-bold border border-indigo-600 px-3 py-1 rounded">
                                        🖨️ Cetak Nota
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="border px-4 py-8 text-center text-gray-500 italic">Belum ada transaksi penjualan pada sesi ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
