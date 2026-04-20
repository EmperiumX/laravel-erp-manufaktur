<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Bahan Baku & Packaging') }}
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

                    <a href="{{ route('materials.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 mb-4">
                        + Tambah Bahan Baku
                    </a>

                    <div class="overflow-x-auto w-full">
                        <table id="materialTable" class="w-full table-auto border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">No</th>
                                <th class="border px-4 py-2">Nama Material</th>
                                <th class="border px-4 py-2">Jenis</th>
                                <th class="border px-4 py-2">Satuan</th>
                                <th class="border px-4 py-2">Harga Satuan (Rp)</th>
                                <th class="border px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($materials as $index => $material)
                            <tr>
                                <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                                <td class="border px-4 py-2 font-bold">{{ $material->name }}</td>
                                <td class="border px-4 py-2">{{ $material->type }}</td>
                                <td class="border px-4 py-2 text-center">{{ strtoupper($material->unit) }}</td>
                                <td class="border px-4 py-2 text-right">{{ number_format($material->unit_price, 2, ',', '.') }}</td>
                                <td class="border px-4 py-2 text-center">
                                    <a href="{{ route('materials.edit', $material->id) }}" class="text-yellow-600 hover:underline">Edit</a> | 
                                    <form action="{{ route('materials.destroy', $material->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus material ini?');">
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#materialTable').DataTable();
        });
    </script>
</x-app-layout>