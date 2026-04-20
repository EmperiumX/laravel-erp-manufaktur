<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Toko / Mitra') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <a href="{{ route('stores.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 mb-4">
                        + Tambah Toko
                    </a>

                    <div class="overflow-x-auto w-full">
                        <table id="storeTable" class="w-full table-auto border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 px-4 py-2">No</th>
                                <th class="border border-gray-300 px-4 py-2">Nama Toko/Mitra</th>
                                <th class="border border-gray-300 px-4 py-2">Kategori</th>
                                <th class="border border-gray-300 px-4 py-2">No. HP</th>
                                <th class="border border-gray-300 px-4 py-2">Alamat</th>
                                <th class="border border-gray-300 px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stores as $index => $store)
                            <tr>
                                <td class="border border-gray-300 px-4 py-2 text-center">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 px-4 py-2 font-bold">{{ $store->name }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <span class="bg-gray-200 text-gray-800 text-xs px-2 py-1 rounded-full">{{ $store->category }}</span>
                                </td>
                                <td class="border border-gray-300 px-4 py-2">{{ $store->phone_number }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $store->address }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">
                                    <a href="{{ route('stores.edit', $store->id) }}" class="text-yellow-600 hover:underline">Edit</a> | 
                                    
                                    <form action="{{ route('stores.destroy', $store->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus toko ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline ml-1">Hapus</button>
                                    </form>
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

    <!-- DataTables Script -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#storeTable').DataTable();
        });
    </script>
</x-app-layout>