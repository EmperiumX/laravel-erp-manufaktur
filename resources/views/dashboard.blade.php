<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard Analitik - CV. New Citra') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- WIDGET KARTU ATAS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <!-- Kartu 1: Pendapatan -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 flex items-center">
                    <div class="p-3 bg-green-100 text-green-600 rounded-full mr-4">
                        <!-- Icon Dollar/Uang -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-bold uppercase">Penjualan Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalSalesMonth, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Kartu 2: Pengeluaran -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 flex items-center">
                    <div class="p-3 bg-red-100 text-red-600 rounded-full mr-4">
                        <!-- Icon Belanja -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-bold uppercase">Pembelian Bahan Baku (Bulan Ini)</p>
                        <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalPurchaseMonth, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Kartu 3: Status Konsinyasi -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 flex items-center">
                    <div class="p-3 bg-blue-100 text-blue-600 rounded-full mr-4">
                        <!-- Icon Truk -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-bold uppercase">Konsinyasi Aktif</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $activeConsignments }} Surat Jalan</p>
                    </div>
                </div>

            </div>

            <!-- BAGIAN BAWAH: ALERT STOK MENIPIS -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-red-500">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h3 class="font-bold text-lg text-gray-800">Peringatan: Stok Menipis (Di Bawah 10)</h3>
                    </div>

                    @if($lowStocks->isEmpty())
                        <p class="text-gray-500 italic">✅ Semua stok barang aman.</p>
                    @else
                        <div class="overflow-x-auto w-full">
                            <table class="w-full table-auto border-collapse text-sm">
                                <thead>
                                    <tr class="bg-gray-50 border-b">
                                        <th class="px-4 py-2 text-left text-gray-600">Tipe Barang</th>
                                        <th class="px-4 py-2 text-left text-gray-600">Nama Barang</th>
                                        <th class="px-4 py-2 text-right text-gray-600">Sisa Stok</th>
                                        <th class="px-4 py-2 text-center text-gray-600">Saran Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStocks as $stock)
                                    <tr class="border-b">
                                        <td class="px-4 py-3">
                                            @if($stock->material)
                                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Bahan Baku</span>
                                            @else
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Produk Jadi</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 font-bold">
                                            {{ $stock->material ? $stock->material->name : ($stock->product ? $stock->product->name : '-') }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-bold text-red-600 text-lg">
                                            {{ rtrim(rtrim(number_format($stock->quantity, 2, ',', '.'), '0'), ',') }}
                                            <span class="text-xs text-gray-500 font-normal">
                                                {{ $stock->material ? $stock->material->unit : ($stock->product ? $stock->product->packaging : '') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($stock->material)
                                                <a href="{{ route('purchase-orders.create') }}" class="text-blue-600 hover:underline text-xs">Buat PO Pembelian</a>
                                            @else
                                                <a href="{{ route('productions.create') }}" class="text-blue-600 hover:underline text-xs">Rencanakan Produksi</a>
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
    </div>
</x-app-layout>