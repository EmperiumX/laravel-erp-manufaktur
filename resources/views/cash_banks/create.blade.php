<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tambah Akun Kas/Bank') }}</h2>
            <a href="{{ route('cash-banks.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('cash-banks.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Akun <span class="text-red-500">*</span></label>
                                <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="Contoh: Kas Kecil, BCA Utama">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tipe <span class="text-red-500">*</span></label>
                                <select name="type" class="w-full border-gray-300 rounded-md shadow-sm" required id="typeSelect">
                                    <option value="Cash">Kas (Tunai)</option>
                                    <option value="Bank">Bank</option>
                                </select>
                            </div>
                            <div id="bankFields" style="display:none;">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Bank</label>
                                        <input type="text" name="bank_name" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="BCA, Mandiri, BRI, dll">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">No. Rekening</label>
                                        <input type="text" name="account_number" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="1234567890">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Saldo Awal (Rp)</label>
                                <input type="number" step="0.01" name="balance" value="0" class="w-full border-gray-300 rounded-md shadow-sm" min="0">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Catatan</label>
                                <input type="text" name="notes" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Opsional">
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-4 mt-8 border-t pt-4">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-bold">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('typeSelect').addEventListener('change', function() {
            document.getElementById('bankFields').style.display = this.value === 'Bank' ? 'block' : 'none';
        });
    </script>
</x-app-layout>
