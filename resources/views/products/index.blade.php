<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Master Produk (Barang Jadi)') }}
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

                    <a href="{{ route('products.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 mb-4">
                        + Tambah Produk Baru
                    </a>

                    <div class="overflow-x-auto w-full">
                                            <table id="productTable" class="w-full table-auto border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">SKU</th>
                                <th class="border px-4 py-2">Nama Produk</th>
                                <th class="border px-4 py-2">Berat & Kemasan</th>
                                <th class="border px-4 py-2">Harga End User</th>
                                <th class="border px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                            <tr>
                                <td class="border px-4 py-2 text-center">{{ $product->sku ?? '-' }}</td>
                                <td class="border px-4 py-2 font-bold">{{ $product->name }}</td>
                                <td class="border px-4 py-2 text-center">{{ $product->weight }} {{ $product->weight_unit }} / {{ $product->packaging }}</td>
                                <td class="border px-4 py-2 text-right text-green-600 font-bold">
                                    <!-- Kita cari harga khusus End User dari relasi prices -->
                                    @php
                                        $endUserPrice = $product->prices->where('category', 'End User')->first();
                                    @endphp
                                    {{ $endUserPrice ? 'Rp ' . number_format($endUserPrice->price, 0, ',', '.') : 'Belum di-set' }}
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    <!-- Tombol Detail & Resep -->
                                    <a href="{{ route('products.show', $product->id) }}" class="text-blue-600 hover:underline font-bold">Detail & Resep</a> | 
                                    
                                    <a href="{{ route('products.edit', $product->id) }}" class="text-yellow-600 hover:underline">Edit</a> | 
                                    
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus produk ini beserta semua harga jualnya?');">
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
            $('#productTable').DataTable();
        });
    </script>
</x-app-layout>