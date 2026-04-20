<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Karyawan Baru</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2">Nama Karyawan</label>
                        <input type="text" name="name" class="w-full border-gray-300 rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2">Email (Untuk Login)</label>
                        <input type="email" name="email" class="w-full border-gray-300 rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2">Password</label>
                        <input type="password" name="password" class="w-full border-gray-300 rounded" required minlength="8">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2">Role / Hak Akses</label>
                        <select name="role" class="w-full border-gray-300 rounded" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan User</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>