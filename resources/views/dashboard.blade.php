<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                    {{ __('Dashboard Analitik') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Selamat datang kembali! Berikut ringkasan operasional New Citra hari ini.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- WIDGET KARTU ATAS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Kartu 1: Pendapatan -->
                <div class="bg-white rounded-2xl shadow-premium border-l-4 border-emerald-500 p-6 flex items-center shadow-premium-hover transition-all duration-300">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mr-4 text-2xl shrink-0">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Penjualan Bulan Ini</p>
                        <p class="text-2xl font-extrabold text-gray-900 mt-1">Rp {{ number_format($totalSalesMonth, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Kartu 2: Pengeluaran -->
                <div class="bg-white rounded-2xl shadow-premium border-l-4 border-rose-500 p-6 flex items-center shadow-premium-hover transition-all duration-300">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center mr-4 text-2xl shrink-0">
                        <i class="ri-shopping-bag-3-line"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Pembelian Bahan Baku</p>
                        <p class="text-2xl font-extrabold text-gray-900 mt-1">Rp {{ number_format($totalPurchaseMonth, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Kartu 3: Status Konsinyasi -->
                <div class="bg-white rounded-2xl shadow-premium border-l-4 border-blue-500 p-6 flex items-center shadow-premium-hover transition-all duration-300">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mr-4 text-2xl shrink-0">
                        <i class="ri-truck-line"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Konsinyasi Aktif</p>
                        <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $activeConsignments }} Surat Jalan</p>
                    </div>
                </div>

            </div>

            <!-- BAGIAN BAWAH: ALERT STOK MENIPIS -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-premium p-6 overflow-hidden">
                <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-rose-500 rounded-full"></span>
                        <h3 class="font-extrabold text-lg text-gray-900">Peringatan: Stok Menipis (Di Bawah 10)</h3>
                    </div>
                    @if(!$lowStocks->isEmpty())
                        <span class="px-2.5 py-0.5 bg-rose-50 text-rose-600 rounded-full text-xs font-bold border border-rose-100">Segera Tindaki</span>
                    @endif
                </div>

                @if($lowStocks->isEmpty())
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center text-2xl mb-2">
                            <i class="ri-checkbox-circle-line"></i>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">Semua stok barang aman.</p>
                    </div>
                @else
                    <div class="overflow-x-auto w-full">
                        <table class="w-full table-auto text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="px-4 py-3 text-left font-bold text-gray-600">Tipe Barang</th>
                                    <th class="px-4 py-3 text-left font-bold text-gray-600">Nama Barang</th>
                                    <th class="px-4 py-3 text-right font-bold text-gray-600">Sisa Stok</th>
                                    <th class="px-4 py-3 text-center font-bold text-gray-600">Saran Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($lowStocks as $stock)
                                <tr>
                                    <td class="px-4 py-3.5">
                                        @if($stock->material)
                                            <span class="bg-amber-50 text-amber-700 border border-amber-200/60 px-2.5 py-1 rounded-lg text-xs font-semibold">Bahan Baku</span>
                                        @else
                                            <span class="bg-emerald-50 text-emerald-700 border border-emerald-200/60 px-2.5 py-1 rounded-lg text-xs font-semibold">Produk Jadi</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 font-bold text-gray-800">
                                        {{ $stock->material ? $stock->material->name : ($stock->product ? $stock->product->name : '-') }}
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-extrabold text-rose-600 text-lg">
                                        {{ rtrim(rtrim(number_format($stock->quantity, 2, ',', '.'), '0'), ',') }}
                                        <span class="text-xs text-gray-400 font-normal ml-0.5">
                                            {{ $stock->material ? $stock->material->unit : ($stock->product ? $stock->product->packaging : '') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        @if($stock->material)
                                            <a href="{{ route('purchase-orders.create') }}" class="inline-flex items-center justify-center px-3.5 py-1.5 bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white rounded-xl text-xs font-bold transition-all duration-200">
                                                <i class="ri-shopping-cart-2-line mr-1"></i> Buat PO Pembelian
                                            </a>
                                        @else
                                            <a href="{{ route('productions.create') }}" class="inline-flex items-center justify-center px-3.5 py-1.5 bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white rounded-xl text-xs font-bold transition-all duration-200">
                                                <i class="ri-tools-line mr-1"></i> Rencanakan Produksi
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>