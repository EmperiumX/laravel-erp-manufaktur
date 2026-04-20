<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Data Bahan Baku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('materials.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Material <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="Contoh: Daging Sapi">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Material <span class="text-red-500">*</span></label>
                            <select name="type" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Bahan Pokok">Bahan Pokok</option>
                                <option value="Bahan Penolong">Bahan Penolong</option>
                                <option value="Packaging">Packaging</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Satuan <span class="text-red-500">*</span></label>
                            <input type="text" name="unit" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="Contoh: GRAM, LEMBAR, EKOR">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Harga Per Satuan (Rp) <span class="text-red-500">*</span></label>
                            <!-- Atribut step="0.01" mengizinkan input desimal 2 angka di belakang koma -->
                            <input type="number" step="0.01" name="unit_price" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="Contoh: 15000 atau 11666.67">
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan Data</button>
                            <a href="{{ route('materials.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>