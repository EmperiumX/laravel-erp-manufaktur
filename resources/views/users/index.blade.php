<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Karyawan (Users)') }}
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
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <a href="{{ route('users.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 mb-4 font-bold shadow">
                        + Tambah Karyawan Baru
                    </a>

                    <div class="overflow-x-auto w-full">
                        <table id="userTable" class="w-full table-auto border-collapse border border-gray-300 text-sm whitespace-nowrap">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2 w-10 text-center">No</th>
                                    <th class="border px-4 py-2">Nama Karyawan</th>
                                    <th class="border px-4 py-2">Email Login</th>
                                    <th class="border px-4 py-2 text-center">Jabatan / Hak Akses</th>
                                    <th class="border px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $index => $user)
                                <tr>
                                    <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                                    <td class="border px-4 py-2 font-bold">{{ $user->name }}</td>
                                    <td class="border px-4 py-2 text-gray-600">{{ $user->email }}</td>
                                    <td class="border px-4 py-2 text-center">
                                        @php
                                            $role = $user->getRoleNames()->first();
                                            $badgeColor = 'bg-gray-200 text-gray-800'; // Default
                                            
                                            if($role == 'Superadmin') $badgeColor = 'bg-purple-200 text-purple-800';
                                            elseif($role == 'Admin') $badgeColor = 'bg-blue-200 text-blue-800';
                                            elseif($role == 'Produser') $badgeColor = 'bg-orange-200 text-orange-800';
                                            elseif($role == 'Sales') $badgeColor = 'bg-green-200 text-green-800';
                                        @endphp
                                        <span class="{{ $badgeColor }} px-3 py-1 rounded-full text-xs font-bold tracking-wide">
                                            {{ $role ?? 'Tidak Ada Akses' }}
                                        </span>
                                    </td>
                                    <td class="border px-4 py-2 text-center">
                                        @if(!$user->hasRole('Superadmin'))
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENCABUT akses login karyawan ini? (Data transaksi yang pernah dia buat tidak akan hilang)');">
                                                @csrf 
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-100 text-red-600 border border-red-300 hover:bg-red-600 hover:text-white px-3 py-1 rounded text-xs font-bold transition-colors">
                                                    Cabut Akses (Hapus)
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Protected</span>
                                        @endif
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

    <!-- Integrasi DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() { 
            $('#userTable').DataTable({
                "language": {
                    "search": "Cari Karyawan:"
                }
            }); 
        });
    </script>
</x-app-layout>