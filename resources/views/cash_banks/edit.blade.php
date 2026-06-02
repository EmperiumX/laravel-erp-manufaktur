<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Akun: ') }} {{ $cashBank->name }}</h2>
            <a href="{{ route('cash-banks.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('cash-banks.update', $cashBank->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Akun <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ $cashBank->name }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tipe</label>
                                <select name="type" class="w-full border-gray-300 rounded-md shadow-sm" id="typeSelect">
                                    <option value="Cash" {{ $cashBank->type === 'Cash' ? 'selected' : '' }}>Kas (Tunai)</option>
                                    <option value="Bank" {{ $cashBank->type === 'Bank' ? 'selected' : '' }}>Bank</option>
                                </select>
                            </div>
                            <div id="bankFields" style="{{ $cashBank->type === 'Bank' ? '' : 'display:none;' }}">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Bank</label>
                                        <input type="text" name="bank_name" value="{{ $cashBank->bank_name }}" class="w-full border-gray-300 rounded-md shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">No. Rekening</label>
                                        <input type="text" name="account_number" value="{{ $cashBank->account_number }}" class="w-full border-gray-300 rounded-md shadow-sm">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                                <select name="is_active" class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="1" {{ $cashBank->is_active ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ !$cashBank->is_active ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Catatan</label>
                                <input type="text" name="notes" value="{{ $cashBank->notes }}" class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-4 mt-8 border-t pt-4">
                            <form action="{{ route('cash-banks.destroy', $cashBank->id) }}" method="POST" onsubmit="return confirm('Yakin hapus akun ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm">Hapus Akun</button>
                            </form>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-bold">Simpan Perubahan</button>
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
