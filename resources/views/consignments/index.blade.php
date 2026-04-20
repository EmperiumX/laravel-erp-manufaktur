<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengiriman Konsinyasi (Surat Jalan / DO)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
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

                    <a href="{{ route('consignments.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 mb-4 font-bold">
                        + Buat Surat Jalan (DO)
                    </a>

                    <div class="overflow-x-auto w-full">
                        <table id="doTable" class="w-full table-auto border-collapse border border-gray-300 text-sm whitespace-nowrap">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2">Tgl Kirim</th>
                                    <th class="border px-4 py-2">No. Surat Jalan</th>
                                    <th class="border px-4 py-2">Toko / Tujuan</th>
                                    <th class="border px-4 py-2">Total Nilai (Rp)</th>
                                    <th class="border px-4 py-2">Status</th>
                                    <th class="border px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shipments as $do)
                                <tr>
                                    <td class="border px-4 py-2 text-center">{{ \Carbon\Carbon::parse($do->shipment_date)->format('d/m/Y') }}</td>
                                    <td class="border px-4 py-2 font-bold">{{ $do->shipment_number }}</td>
                                    <td class="border px-4 py-2 text-blue-700 font-bold">{{ $do->store->name ?? '-' }}</td>
                                    <td class="border px-4 py-2 text-right font-bold">Rp {{ number_format($do->total_amount, 0, ',', '.') }}</td>
                                    <td class="border px-4 py-2 text-center">
                                        @if($do->status == 'Sent')
                                            <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded text-xs font-bold">Terkirim (Konsinyasi)</span>
                                        @elseif($do->status == 'Invoiced')
                                            <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-xs font-bold">Sudah Ditagih</span>
                                        @else
                                            <span class="bg-red-200 text-red-800 px-2 py-1 rounded text-xs font-bold">Batal</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2 text-center">
                                        <a href="{{ route('consignments.print', $do->id) }}" target="_blank" class="text-blue-600 hover:underline font-bold border border-blue-600 px-3 py-1 rounded">
                                            🖨️ Cetak Surat Jalan
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>$(document).ready(function() { $('#doTable').DataTable({ "order": [[ 0, "desc" ]] }); });</script>
</x-app-layout>