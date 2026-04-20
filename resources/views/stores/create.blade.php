<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Data Toko / Mitra') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('stores.store') }}" method="POST">
                        @csrf
                        
                        <!-- Input Nama Toko -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Toko / Mitra <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="Contoh: Toko Istana Buah">
                        </div>

                        <!-- Input Dropdown Kategori -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Kategori Harga <span class="text-red-500">*</span></label>
                            <select name="category" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Mitra">Mitra</option>
                                <option value="Agen">Agen</option>
                                <option value="Distributor">Distributor</option>
                                <option value="Reseller">Reseller</option>
                                <option value="End User">End User (Eceran)</option>
                                <option value="Maklon">Maklon</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Kategori ini akan menentukan harga jual produk secara otomatis.</p>
                        </div>

                        <!-- Input No HP -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">No. HP / Telepon Toko</label>
                            <input type="text" name="phone_number" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: 08123456789">
                        </div>

                        <!-- Input Alamat -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Lengkap</label>
                            <textarea name="address" rows="3" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Jl. Pandanaran No. 10, Semarang"></textarea>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan Data</button>
                            <a href="{{ route('stores.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>