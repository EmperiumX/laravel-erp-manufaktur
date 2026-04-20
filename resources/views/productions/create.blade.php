<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Rencana Produksi Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if($products->isEmpty())
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded relative mb-4">
                            <strong>Perhatian!</strong> Belum ada satupun produk yang memiliki Resep (BOM). Silakan isi resep di Master Produk terlebih dahulu sebelum membuat produksi.
                        </div>
                    @else
                        <form action="{{ route('productions.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Produk Jadi yang Dibuat <span class="text-red-500">*</span></label>
                                <select name="product_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Target Produksi (Kuantitas) <span class="text-red-500">*</span></label>
                                <input type="number" name="quantity" class="w-full border-gray-300 rounded-md shadow-sm" required min="1" placeholder="Contoh: 100">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Produksi <span class="text-red-500">*</span></label>
                                <input type="date" name="production_date" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Catatan Tambahan</label>
                                <textarea name="notes" rows="2" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Opsional"></textarea>
                            </div>

                            <div class="flex items-center gap-4 mt-6">
                                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 font-bold">Simpan Rencana</button>
                                <a href="{{ route('productions.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Batal</a>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>