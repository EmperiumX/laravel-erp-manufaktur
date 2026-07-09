<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Dashboard Laporan Keuangan') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Rangkuman finansial periode {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Summary Cards / KPIs -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Laba Rugi -->
                <div class="bg-gradient-to-br from-emerald-500 to-teal-700 rounded-2xl shadow-xl p-6 text-white transform hover:scale-[1.02] transition-all duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider">Laba Bersih (Bulan Ini)</p>
                            <p class="text-2xl font-bold mt-2">Rp {{ number_format($netProfit, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="ri-line-chart-line text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-emerald-100">
                        <span>Margin Kotor: Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Arus Kas Bersih -->
                <div class="bg-gradient-to-br from-blue-500 to-indigo-700 rounded-2xl shadow-xl p-6 text-white transform hover:scale-[1.02] transition-all duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-blue-100 text-xs font-bold uppercase tracking-wider">Arus Kas Bersih (Bulan Ini)</p>
                            <p class="text-2xl font-bold mt-2">Rp {{ number_format($netCashFlow, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="ri-exchange-funds-line text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-blue-100">
                        <span>Total Masuk: Rp {{ number_format($cashIn, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Piutang Dagang -->
                <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-xl p-6 text-white transform hover:scale-[1.02] transition-all duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-amber-100 text-xs font-bold uppercase tracking-wider">Total Piutang Dagang</p>
                            <p class="text-2xl font-bold mt-2">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="ri-money-dollar-circle-line text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-amber-100">
                        <span>{{ $overduePiutangCount }} Invoice Jatuh Tempo</span>
                    </div>
                </div>

                <!-- Hutang Dagang -->
                <div class="bg-gradient-to-br from-rose-500 to-red-700 rounded-2xl shadow-xl p-6 text-white transform hover:scale-[1.02] transition-all duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-rose-100 text-xs font-bold uppercase tracking-wider">Total Hutang Dagang</p>
                            <p class="text-2xl font-bold mt-2">Rp {{ number_format($totalHutang, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="ri-hand-coin-line text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-rose-100">
                        <span>{{ $overdueHutangCount }} Invoice Jatuh Tempo</span>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Laba Rugi Chart -->
                <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100 lg:col-span-2">
                    <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                        Struktur Laba Rugi (Bulan Ini)
                    </h3>
                    <div class="relative w-full" style="height: 300px;">
                        <canvas id="profitLossChart"></canvas>
                    </div>
                </div>

                <!-- Arus Kas Chart -->
                <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-blue-500 rounded-full"></span>
                        Arus Kas (Masuk vs Keluar)
                    </h3>
                    <div class="relative w-full flex items-center justify-center" style="height: 220px;">
                        <canvas id="cashFlowChart"></canvas>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-2 text-center text-sm border-t pt-3">
                        <div>
                            <p class="text-gray-500 text-xs">Uang Masuk</p>
                            <p class="font-bold text-green-600">Rp {{ number_format($cashIn, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">Uang Keluar</p>
                            <p class="font-bold text-red-600">Rp {{ number_format($cashOut, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Saldo Akun Kas/Bank & Info Tambahan -->
            <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100 mb-8">
                <div class="flex justify-between items-center border-b pb-4 mb-4">
                    <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-indigo-500 rounded-full"></span>
                        Saldo Akun Kas & Bank
                    </h3>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Total Likuiditas</p>
                        <p class="font-bold text-indigo-700 text-lg">Rp {{ number_format($totalCashBalance, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($cashBanks as $cb)
                    <div class="bg-gray-50 hover:bg-gray-100 transition duration-150 border border-gray-100 rounded-xl p-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $cb->name }}</p>
                            <p class="text-xs text-gray-500 uppercase mt-0.5">{{ $cb->type }}</p>
                        </div>
                        <p class="text-md font-bold {{ $cb->balance >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            Rp {{ number_format($cb->balance, 0, ',', '.') }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Detailed Reports Navigation (Premium Cards) -->
            <h3 class="font-bold text-gray-800 text-lg mb-6 flex items-center gap-2">
                <span class="w-1.5 h-6 bg-gray-700 rounded-full"></span>
                Akses Detail Laporan Keuangan
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
                <!-- Laba Rugi -->
                <a href="{{ route('reports.profit-loss') }}" class="group bg-white hover:bg-gradient-to-br hover:from-blue-50 hover:to-indigo-50 border border-gray-100 hover:border-indigo-200 shadow-sm hover:shadow-md sm:rounded-2xl p-6 transition-all duration-300 block">
                    <div class="flex items-center gap-4">
                        <div class="bg-blue-100 text-blue-600 p-3 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                            <i class="ri-line-chart-line text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-md">Laba Rugi</h3>
                            <p class="text-xs text-gray-500 mt-1">Pendapatan, HPP, Biaya Operasional, Laba Bersih</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between items-center text-xs text-indigo-600 font-semibold opacity-0 group-hover:opacity-100 transition duration-300">
                        <span>Buka Detail Laporan</span>
                        <i class="ri-arrow-right-line"></i>
                    </div>
                </a>

                <!-- Piutang Dagang -->
                <a href="{{ route('reports.accounts-receivable') }}" class="group bg-white hover:bg-gradient-to-br hover:from-green-50 hover:to-emerald-50 border border-gray-100 hover:border-emerald-200 shadow-sm hover:shadow-md sm:rounded-2xl p-6 transition-all duration-300 block">
                    <div class="flex items-center gap-4">
                        <div class="bg-green-100 text-green-600 p-3 rounded-xl group-hover:bg-green-600 group-hover:text-white transition duration-300">
                            <i class="ri-money-dollar-circle-line text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-md">Piutang Dagang</h3>
                            <p class="text-xs text-gray-500 mt-1">Tagihan pelanggan belum lunas, invoice piutang</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between items-center text-xs text-emerald-600 font-semibold opacity-0 group-hover:opacity-100 transition duration-300">
                        <span>Buka Detail Laporan</span>
                        <i class="ri-arrow-right-line"></i>
                    </div>
                </a>

                <!-- Hutang Dagang -->
                <a href="{{ route('reports.accounts-payable') }}" class="group bg-white hover:bg-gradient-to-br hover:from-rose-50 hover:to-red-50 border border-gray-100 hover:border-red-200 shadow-sm hover:shadow-md sm:rounded-2xl p-6 transition-all duration-300 block">
                    <div class="flex items-center gap-4">
                        <div class="bg-rose-100 text-rose-600 p-3 rounded-xl group-hover:bg-rose-600 group-hover:text-white transition duration-300">
                            <i class="ri-hand-coin-line text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-md">Hutang Dagang</h3>
                            <p class="text-xs text-gray-500 mt-1">Kewajiban bayar supplier, invoice pembelian</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between items-center text-xs text-rose-600 font-semibold opacity-0 group-hover:opacity-100 transition duration-300">
                        <span>Buka Detail Laporan</span>
                        <i class="ri-arrow-right-line"></i>
                    </div>
                </a>

                <!-- Arus Kas -->
                <a href="{{ route('reports.cash-flow') }}" class="group bg-white hover:bg-gradient-to-br hover:from-purple-50 hover:to-fuchsia-50 border border-gray-100 hover:border-purple-200 shadow-sm hover:shadow-md sm:rounded-2xl p-6 transition-all duration-300 block">
                    <div class="flex items-center gap-4">
                        <div class="bg-purple-100 text-purple-600 p-3 rounded-xl group-hover:bg-purple-600 group-hover:text-white transition duration-300">
                            <i class="ri-exchange-funds-line text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-md">Arus Kas</h3>
                            <p class="text-xs text-gray-500 mt-1">Mutasi uang masuk & keluar, log transaksi kas</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between items-center text-xs text-purple-600 font-semibold opacity-0 group-hover:opacity-100 transition duration-300">
                        <span>Buka Detail Laporan</span>
                        <i class="ri-arrow-right-line"></i>
                    </div>
                </a>

                <!-- Target Sales & Kinerja -->
                <a href="{{ route('reports.sales-performance') }}" class="group bg-white hover:bg-gradient-to-br hover:from-amber-50 hover:to-yellow-50 border border-gray-100 hover:border-amber-200 shadow-sm hover:shadow-md sm:rounded-2xl p-6 transition-all duration-300 block">
                    <div class="flex items-center gap-4">
                        <div class="bg-amber-100 text-amber-600 p-3 rounded-xl group-hover:bg-amber-600 group-hover:text-white transition duration-300">
                            <i class="ri-trophy-line text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-md">Kinerja Sales</h3>
                            <p class="text-xs text-gray-500 mt-1">Kinerja penjualan staf & target tim bulanan</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between items-center text-xs text-amber-600 font-semibold opacity-0 group-hover:opacity-100 transition duration-300">
                        <span>Buka Detail Laporan</span>
                        <i class="ri-arrow-right-line"></i>
                    </div>
                </a>

                <!-- Jurnal Umum -->
                <a href="{{ route('reports.general-journal') }}" class="group bg-white hover:bg-gradient-to-br hover:from-cyan-50 hover:to-blue-50 border border-gray-100 hover:border-cyan-200 shadow-sm hover:shadow-md sm:rounded-2xl p-6 transition-all duration-300 block">
                    <div class="flex items-center gap-4">
                        <div class="bg-cyan-100 text-cyan-600 p-3 rounded-xl group-hover:bg-cyan-600 group-hover:text-white transition duration-300">
                            <i class="ri-book-open-line text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-md">Jurnal Umum</h3>
                            <p class="text-xs text-gray-500 mt-1">Daftar transaksi bulanan & jurnal per mitra/supplier</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between items-center text-xs text-cyan-600 font-semibold opacity-0 group-hover:opacity-100 transition duration-300">
                        <span>Buka Detail Jurnal</span>
                        <i class="ri-arrow-right-line"></i>
                    </div>
                </a>
            </div>

        </div>
    </div>

    <!-- Chart.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Profit Loss Chart (Bar Chart)
            const plCtx = document.getElementById('profitLossChart').getContext('2d');
            new Chart(plCtx, {
                type: 'bar',
                data: {
                    labels: ['Total Pendapatan', 'Harga Pokok Penjualan (HPP)', 'Biaya Operasional', 'Laba Bersih'],
                    datasets: [{
                        label: 'Nilai (Rupiah)',
                        data: [
                            {{ $totalRevenue }},
                            {{ $cogs }},
                            {{ $operationalExpenses }},
                            {{ $netProfit }}
                        ],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.75)',  // Blue (Revenue)
                            'rgba(245, 158, 11, 0.75)',  // Amber (COGS)
                            'rgba(239, 68, 68, 0.75)',   // Red (Expenses)
                            'rgba(16, 185, 129, 0.75)'   // Emerald (Net Profit)
                        ],
                        borderColor: [
                            'rgb(59, 130, 246)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)',
                            'rgb(16, 185, 129)'
                        ],
                        borderWidth: 1.5,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw;
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toLocaleString('id-ID') + ' Jt';
                                    }
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });

            // 2. Cash Flow Chart (Doughnut Chart)
            const cfCtx = document.getElementById('cashFlowChart').getContext('2d');
            new Chart(cfCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Uang Masuk', 'Uang Keluar'],
                    datasets: [{
                        data: [
                            {{ $cashIn }},
                            {{ $cashOut }}
                        ],
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.8)', // Green
                            'rgba(239, 68, 68, 0.8)'   // Red
                        ],
                        borderColor: [
                            '#fff',
                            '#fff'
                        ],
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw;
                                    return ' Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        });
    </script>
</x-app-layout>
