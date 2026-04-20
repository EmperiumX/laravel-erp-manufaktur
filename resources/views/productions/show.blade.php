<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review & Eksekusi Produksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white p-6 rounded-lg shadow-sm border mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-bold text-2xl text-blue-700 mb-2">{{ $production->production_number }}</h3>
                        <p class="text-gray-600 text-lg">Target: <strong>{{ $production->quantity }} {{ $production->product->packaging }}</strong> {{ $production->product->name }}</p>
                    </div>
                    <div class="text-right text-sm">
                        <p>Tanggal: <span class="font-bold">{{ \Carbon\Carbon::parse($production->production_date)->format('d/m/Y') }}</span></p>
                        <p class="mt-1">Status: 
                            <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded font-bold">{{ $production->status }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <h3 class="font-bold text-lg mb-4 border-b pb-2">Pengecekan Ketersediaan Bahan Baku</h3>
                
                <div class="overflow-x-auto w-full mb-6">
                    <table class="w-full table-auto border-collapse border border-gray-300 text-sm whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">Bahan Baku</th>
                                <th class="border px-4 py-2 text-center">Dibutuhkan</th>
                                <th class="border px-4 py-2 text-center">Tersedia di Gudang</th>
                                <th class="border px-4 py-2 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materialsNeeded as $mat)
                            <tr>
                                <td class="border px-4 py-2 font-bold">{{ $mat['name'] }}</td>
                                <td class="border px-4 py-2 text-center text-blue-700 font-bold">
                                    {{ rtrim(rtrim(number_format($mat['required'], 4, ',', '.'), '0'), ',') }} {{ $mat['unit'] }}
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    {{ rtrim(rtrim(number_format($mat['available'], 4, ',', '.'), '0'), ',') }} {{ $mat['unit'] }}
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    @if($mat['is_enough'])
                                        <span class="text-green-600 font-bold">✔️ Cukup</span>
                                    @else
                                        <span class="text-red-600 font-bold">❌ Kurang</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($production->status == 'Pending')
                    @if($canProduce)
                        <div class="bg-green-50 border border-green-200 p-4 rounded-lg text-center">
                            <p class="text-green-800 mb-4 font-bold">✅ Semua bahan baku mencukupi. Anda dapat mengeksekusi produksi ini.</p>
                            <form action="{{ route('productions.complete', $production->id) }}" method="POST" onsubmit="return confirm('Mengeksekusi produksi akan otomatis mengurangi stok bahan baku dan menambah stok barang jadi. Lanjutkan?');">
                                @csrf
                                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-bold text-lg shadow">
                                    ⚙️ Eksekusi & Selesaikan Produksi
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-red-50 border border-red-200 p-4 rounded-lg text-center">
                            <p class="text-red-800 font-bold">⚠️ Bahan Baku Tidak Mencukupi!</p>
                            <p class="text-gray-600 text-sm mt-2">Sistem menolak eksekusi produksi. Silakan lakukan Pembelian (PO) bahan baku ke Supplier terlebih dahulu agar stok di gudang mencukupi.</p>
                        </div>
                    @endif
                @endif
                
                <div class="mt-4 text-center">
                    <a href="{{ route('productions.index') }}" class="text-gray-500 hover:underline">← Kembali ke Daftar Produksi</a>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>