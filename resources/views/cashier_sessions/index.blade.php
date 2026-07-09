<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Sesi Kasir (POS Sessions)') }}
            </h2>
            <a href="{{ route('cashier-sessions.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-bold shadow transition">
                + Buka Sesi Kasir Baru
            </a>
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
            @if(session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('info') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="overflow-x-auto w-full">
                        <table id="sessionsTable" class="w-full table-auto border-collapse border border-gray-300 text-sm whitespace-nowrap">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2">Buka Sesi</th>
                                    <th class="border px-4 py-2">Tutup Sesi</th>
                                    <th class="border px-4 py-2">Staf Kasir</th>
                                    <th class="border px-4 py-2">Akun Kas</th>
                                    <th class="border px-4 py-2 text-right">Saldo Awal (Rp)</th>
                                    <th class="border px-4 py-2 text-right">Fisik Akhir (Rp)</th>
                                    <th class="border px-4 py-2 text-right">Selisih (Rp)</th>
                                    <th class="border px-4 py-2 text-center">Status</th>
                                    <th class="border px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sessions as $session)
                                <tr>
                                    <td class="border px-4 py-2 text-center">
                                        {{ $session->opening_date ? $session->opening_date->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td class="border px-4 py-2 text-center">
                                        {{ $session->closing_date ? $session->closing_date->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td class="border px-4 py-2 font-bold">{{ $session->user->name }}</td>
                                    <td class="border px-4 py-2">{{ $session->cashBank->name }}</td>
                                    <td class="border px-4 py-2 text-right font-mono">Rp {{ number_format($session->opening_cash, 0, ',', '.') }}</td>
                                    <td class="border px-4 py-2 text-right font-mono">
                                        {{ $session->closing_cash !== null ? 'Rp ' . number_format($session->closing_cash, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="border px-4 py-2 text-right font-bold font-mono">
                                        @if($session->difference === null)
                                            -
                                        @elseif($session->difference > 0)
                                            <span class="text-green-600">+Rp {{ number_format($session->difference, 0, ',', '.') }}</span>
                                        @elseif($session->difference < 0)
                                            <span class="text-red-600">-Rp {{ number_format(abs($session->difference), 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-gray-500">Rp 0 (Pas)</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2 text-center">
                                        @if($session->status === 'Open')
                                            <span class="bg-green-100 text-green-800 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase">Aktif</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase">Ditutup</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2 text-center">
                                        <a href="{{ route('cashier-sessions.show', $session->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold border border-indigo-600 px-3 py-1 rounded hover:bg-indigo-50 transition">
                                            Detail & Rekap
                                        </a>
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

    <!-- DataTables Integration -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#sessionsTable').DataTable({
                "order": [[ 0, "desc" ]]
            });
        });
    </script>
</x-app-layout>
