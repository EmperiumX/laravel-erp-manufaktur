<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Laporan Laba Rugi') }}</h2>
            <a href="{{ route('reports.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Filter Periode -->
            <div class="bg-white shadow-sm sm:rounded-lg p-4 mb-6">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-bold">Tampilkan</button>
                </form>
            </div>

            <!-- Visual Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                    Grafik Laba Rugi
                </h3>
                <div class="relative w-full" style="height: 250px;">
                    <canvas id="profitLossChart"></canvas>
                </div>
            </div>

            <!-- Laporan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg text-center mb-1">LAPORAN LABA RUGI</h3>
                    <p class="text-center text-gray-500 text-sm mb-6">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>

                    <table class="w-full text-sm">
                        <tbody>
                            <!-- Pendapatan -->
                            <tr class="bg-blue-50">
                                <td class="px-4 py-3 font-bold text-blue-700 text-lg" colspan="2">PENDAPATAN</td>
                            </tr>
                            <tr>
                                <td class="px-8 py-2">Penjualan Langsung</td>
                                <td class="px-4 py-2 text-right">Rp {{ number_format($salesRevenue, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-8 py-2">Penjualan Konsinyasi</td>
                                <td class="px-4 py-2 text-right">Rp {{ number_format($consignmentRevenue, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-t-2 border-blue-300">
                                <td class="px-4 py-3 font-bold">Total Pendapatan</td>
                                <td class="px-4 py-3 text-right font-bold text-blue-700 text-lg">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                            </tr>

                            <!-- HPP -->
                            <tr class="bg-red-50 mt-4">
                                <td class="px-4 py-3 font-bold text-red-700 text-lg" colspan="2">HARGA POKOK PENJUALAN (HPP)</td>
                            </tr>
                            <tr>
                                <td class="px-8 py-2">Pembelian Bahan Baku (PO Completed)</td>
                                <td class="px-4 py-2 text-right text-red-600">Rp {{ number_format($cogs, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-t-2 border-red-300">
                                <td class="px-4 py-3 font-bold">Total HPP</td>
                                <td class="px-4 py-3 text-right font-bold text-red-700 text-lg">Rp {{ number_format($cogs, 0, ',', '.') }}</td>
                            </tr>

                            <!-- Laba Kotor -->
                            <tr class="bg-green-50 border-t-4 border-green-500">
                                <td class="px-4 py-4 font-bold text-green-800 text-lg">LABA KOTOR</td>
                                <td class="px-4 py-4 text-right font-bold text-green-800 text-xl">Rp {{ number_format($grossProfit, 0, ',', '.') }}</td>
                            </tr>

                            <!-- Biaya Operasional -->
                            <tr class="bg-yellow-50 mt-4">
                                <td class="px-4 py-3 font-bold text-yellow-700 text-lg" colspan="2">BIAYA OPERASIONAL</td>
                            </tr>
                            <tr>
                                <td class="px-8 py-2">Biaya Operasional & Gaji</td>
                                <td class="px-4 py-2 text-right text-red-600">Rp {{ number_format($operationalExpenses, 0, ',', '.') }}</td>
                            </tr>

                            <!-- Laba Bersih -->
                            <tr class="border-t-4 {{ $netProfit >= 0 ? 'border-green-500 bg-green-100' : 'border-red-500 bg-red-100' }}">
                                <td class="px-4 py-4 font-bold text-xl">LABA BERSIH</td>
                                <td class="px-4 py-4 text-right font-bold text-2xl {{ $netProfit >= 0 ? 'text-green-800' : 'text-red-800' }}">
                                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('profitLossChart').getContext('2d');
            new Chart(ctx, {
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
        });
    </script>
</x-app-layout>
