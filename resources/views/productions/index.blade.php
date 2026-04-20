<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Perintah Produksi (Production Order)') }}
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

                    <a href="{{ route('productions.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 mb-4 font-bold">
                        + Buat Rencana Produksi
                    </a>

                    <div class="overflow-x-auto w-full">
                        <table id="prodTable" class="w-full table-auto border-collapse border border-gray-300 text-sm whitespace-nowrap">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2">Tgl Produksi</th>
                                    <th class="border px-4 py-2">No. Produksi</th>
                                    <th class="border px-4 py-2">Produk Jadi</th>
                                    <th class="border px-4 py-2">Target Kuantitas</th>
                                    <th class="border px-4 py-2">Status</th>
                                    <th class="border px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productions as $prod)
                                <tr>
                                    <td class="border px-4 py-2 text-center">{{ \Carbon\Carbon::parse($prod->production_date)->format('d/m/Y') }}</td>
                                    <td class="border px-4 py-2 font-bold">{{ $prod->production_number }}</td>
                                    <td class="border px-4 py-2 font-bold text-blue-700">{{ $prod->product->name }}</td>
                                    <td class="border px-4 py-2 text-center font-bold">{{ $prod->quantity }} {{ $prod->product->packaging }}</td>
                                    <td class="border px-4 py-2 text-center">
                                        @if($prod->status == 'Pending')
                                            <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded text-xs font-bold">Pending</span>
                                        @elseif($prod->status == 'Completed')
                                            <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-xs font-bold">Selesai</span>
                                        @else
                                            <span class="bg-red-200 text-red-800 px-2 py-1 rounded text-xs font-bold">Batal</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2 text-center">
                                        <a href="{{ route('productions.show', $prod->id) }}" class="text-blue-600 hover:underline font-bold border border-blue-600 px-3 py-1 rounded inline-block mb-2">Eksekusi/Detail</a>
                                        
                                        <!-- HANYA TAMPIL JIKA STATUS MASIH PENDING -->
                                        @if($prod->status == 'Pending')
                                            <br>
                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('productions.destroy', $prod->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin membatalkan produksi ini? (Hanya bisa dilakukan karena produksi belum dimulai)');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline text-sm font-bold">Batalkan Produksi</button>
                                            </form>
                                        @endif
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
    <script>$(document).ready(function() { $('#prodTable').DataTable({ "order": [[ 0, "desc" ]] }); });</script>
</x-app-layout>