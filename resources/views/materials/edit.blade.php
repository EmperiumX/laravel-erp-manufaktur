<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Bahan Baku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('materials.update', $material->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Material <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ $material->name }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Material <span class="text-red-500">*</span></label>
                            <select name="type" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="Bahan Pokok" {{ $material->type == 'Bahan Pokok' ? 'selected' : '' }}>Bahan Pokok</option>
                                <option value="Bahan Penolong" {{ $material->type == 'Bahan Penolong' ? 'selected' : '' }}>Bahan Penolong</option>
                                <option value="Packaging" {{ $material->type == 'Packaging' ? 'selected' : '' }}>Packaging</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Satuan <span class="text-red-500">*</span></label>
                            <input type="text" name="unit" value="{{ $material->unit }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Harga Per Satuan (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="unit_price" value="{{ $material->unit_price }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Data</button>
                            <a href="{{ route('materials.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>