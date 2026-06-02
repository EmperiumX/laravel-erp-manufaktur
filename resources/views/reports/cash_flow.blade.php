<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Laporan Arus Kas') }}</h2>
            <a href="{{ route('reports.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filter -->
            <div class="bg-white shadow-sm sm:rounded-lg p-4 mb-6">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Dari</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Sampai</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-bold">Tampilkan</button>
                </form>
            </div>

            <!-- Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <p class="text-sm text-gray-500">Total Masuk (Debit)</p>
                    <p class="text-2xl font-bold text-green-700">Rp {{ number_format($totalDebit, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <p class="text-sm text-gray-500">Total Keluar (Credit)</p>
                    <p class="text-2xl font-bold text-red-700">Rp {{ number_format($totalCredit, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 {{ ($totalDebit - $totalCredit) >= 0 ? 'border-blue-500' : 'border-orange-500' }}">
                    <p class="text-sm text-gray-500">Arus Kas Bersih</p>
                    <p class="text-2xl font-bold {{ ($totalDebit - $totalCredit) >= 0 ? 'text-blue-700' : 'text-orange-700' }}">
                        Rp {{ number_format($totalDebit - $totalCredit, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Visual Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Debit vs Credit -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 text-md mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-blue-500 rounded-full"></span>
                        Perbandingan Arus Kas
                    </h3>
                    <div class="relative w-full" style="height: 220px;">
                        <canvas id="cashComparisonChart"></canvas>
                    </div>
                </div>

                <!-- Expenses By Category -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 text-md mb-4 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-purple-500 rounded-full"></span>
                        Alokasi Uang Keluar (Credit) Per Kategori
                    </h3>
                    <div class="relative w-full flex items-center justify-center" style="height: 220px;">
                        <canvas id="cashOutCategoryChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Saldo Per Akun -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4 text-blue-700 border-b pb-2">Saldo Per Akun</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        @foreach($cashBanks as $cb)
                        <div class="border rounded-lg p-3">
                            <p class="text-sm text-gray-500">{{ $cb->name }} <span class="text-xs">({{ $cb->type }})</span></p>
                            <p class="text-xl font-bold {{ $cb->balance >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                Rp {{ number_format($cb->balance, 0, ',', '.') }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Per Kategori -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4 text-purple-700 border-b pb-2">Ringkasan Per Kategori</h3>
                    <table class="w-full table-auto border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">Kategori</th>
                                <th class="border px-4 py-2 text-right">Masuk (Debit)</th>
                                <th class="border px-4 py-2 text-right">Keluar (Credit)</th>
                                <th class="border px-4 py-2 text-right">Selisih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byCategory as $cat)
                            <tr>
                                <td class="border px-4 py-2 font-bold">{{ $cat['category'] }}</td>
                                <td class="border px-4 py-2 text-right text-green-600">Rp {{ number_format($cat['debit'], 0, ',', '.') }}</td>
                                <td class="border px-4 py-2 text-right text-red-600">Rp {{ number_format($cat['credit'], 0, ',', '.') }}</td>
                                <td class="border px-4 py-2 text-right font-bold {{ ($cat['debit'] - $cat['credit']) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format($cat['debit'] - $cat['credit'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detail Transaksi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4 text-blue-700 border-b pb-2">Detail Transaksi</h3>
                    <div class="overflow-x-auto w-full">
                    <table id="cashFlowTable" class="w-full table-auto border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">Tanggal</th>
                                <th class="border px-4 py-2">Akun</th>
                                <th class="border px-4 py-2">Deskripsi</th>
                                <th class="border px-4 py-2">Kategori</th>
                                <th class="border px-4 py-2 text-right">Masuk</th>
                                <th class="border px-4 py-2 text-right">Keluar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $trx)
                            <tr>
                                <td class="border px-4 py-2 text-center">{{ $trx->transaction_date->format('d/m/Y') }}</td>
                                <td class="border px-4 py-2">{{ $trx->cashBank->name ?? '-' }}</td>
                                <td class="border px-4 py-2">{{ $trx->description }}</td>
                                <td class="border px-4 py-2"><span class="bg-gray-100 text-xs px-2 py-0.5 rounded">{{ $trx->category }}</span></td>
                                <td class="border px-4 py-2 text-right {{ $trx->type === 'Debit' ? 'text-green-600 font-bold' : '' }}">
                                    {{ $trx->type === 'Debit' ? 'Rp ' . number_format($trx->amount, 0, ',', '.') : '' }}
                                </td>
                                <td class="border px-4 py-2 text-right {{ $trx->type === 'Credit' ? 'text-red-600 font-bold' : '' }}">
                                    {{ $trx->type === 'Credit' ? 'Rp ' . number_format($trx->amount, 0, ',', '.') : '' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>$(document).ready(function() { $('#cashFlowTable').DataTable({ "order": [[ 0, "asc" ]] }); });</script>

    <!-- Chart.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Debit vs Credit (Bar Chart)
            const compCtx = document.getElementById('cashComparisonChart').getContext('2d');
            new Chart(compCtx, {
                type: 'bar',
                data: {
                    labels: ['Total Masuk (Debit)', 'Total Keluar (Credit)'],
                    datasets: [{
                        data: [{{ $totalDebit }}, {{ $totalCredit }}],
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.75)', // Green
                            'rgba(239, 68, 68, 0.75)'   // Red
                        ],
                        borderColor: [
                            'rgb(16, 185, 129)',
                            'rgb(239, 68, 68)'
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
                                    return ' Rp ' + context.raw.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });

            // 2. Expenses by Category (Doughnut Chart)
            const catCtx = document.getElementById('cashOutCategoryChart').getContext('2d');
            const categories = [
                @foreach($byCategory as $cat)
                    @if($cat['credit'] > 0)
                        "{{ $cat['category'] }}",
                    @endif
                @endforeach
            ];
            const credits = [
                @foreach($byCategory as $cat)
                    @if($cat['credit'] > 0)
                        {{ $cat['credit'] }},
                    @endif
                @endforeach
            ];

            if (credits.length === 0) {
                // Show placeholder message if no outgoing flow
                catCtx.font = "14px Inter";
                catCtx.fillStyle = "#9ca3af";
                catCtx.textAlign = "center";
                catCtx.fillText("Tidak ada transaksi keluar", 150, 110);
            } else {
                new Chart(catCtx, {
                    type: 'doughnut',
                    data: {
                        labels: categories,
                        datasets: [{
                            data: credits,
                            backgroundColor: [
                                'rgba(239, 68, 68, 0.8)',   // Red
                                'rgba(245, 158, 11, 0.8)',  // Amber
                                'rgba(59, 130, 246, 0.8)',   // Blue
                                'rgba(139, 92, 246, 0.8)',  // Purple
                                'rgba(236, 72, 153, 0.8)',  // Pink
                                'rgba(20, 184, 166, 0.8)'   // Teal
                            ],
                            borderWidth: 1.5,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    boxWidth: 10,
                                    padding: 10,
                                    font: { size: 11 }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return ' Rp ' + context.raw.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
            }
        });
    </script>
</x-app-layout>
