<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Retur Konsinyasi') }}
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

                    <a href="{{ route('returns.create') }}" class="inline-block px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 mb-4 font-bold">
                        + Input Retur Barang
                    </a>

                    <div class="overflow-x-auto w-full">
                        <table id="returnTable" class="w-full table-auto border-collapse border border-gray-300 text-sm whitespace-nowrap">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2">Tgl Retur</th>
                                    <th class="border px-4 py-2">No. Bukti Retur</th>
                                    <th class="border px-4 py-2">Toko Pengirim</th>
                                    <th class="border px-4 py-2">Catatan</th>
                                    <th class="border px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($returns as $ret)
                                <tr>
                                    <td class="border px-4 py-2 text-center">{{ \Carbon\Carbon::parse($ret->return_date)->format('d/m/Y') }}</td>
                                    <td class="border px-4 py-2 font-bold">{{ $ret->return_number }}</td>
                                    <td class="border px-4 py-2 text-blue-700 font-bold">{{ $ret->store->name ?? '-' }}</td>
                                    <td class="border px-4 py-2">{{ $ret->notes ?? '-' }}</td>
                                    <td class="border px-4 py-2 text-center">
                                        <a href="#" class="text-blue-600 hover:underline">Detail</a>
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
    <script>$(document).ready(function() { $('#returnTable').DataTable({ "order": [[ 0, "desc" ]] }); });</script>
</x-app-layout>