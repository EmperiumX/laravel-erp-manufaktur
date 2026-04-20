<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Supplier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Form Action mengarah ke route update, membawa parameter ID -->
                    <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                        @csrf
                        @method('PUT') <!-- Wajib ditambahkan untuk proses Update -->
                        
                        <!-- Input Nama Supplier -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Supplier / Perusahaan <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ $supplier->name }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <!-- Input Kontak Person -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Kontak Person</label>
                            <input type="text" name="contact_person" value="{{ $supplier->contact_person }}" class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Input No HP -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">No. HP / Telepon</label>
                            <input type="text" name="phone_number" value="{{ $supplier->phone_number }}" class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Input Alamat -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Lengkap</label>
                            <textarea name="address" rows="3" class="w-full border-gray-300 rounded-md shadow-sm">{{ $supplier->address }}</textarea>
                        </div>

                        <!-- Tombol Submit & Batal -->
                        <div class="flex items-center gap-4 mt-6">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Data</button>
                            <a href="{{ route('suppliers.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>