<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Tim Sales & Target') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- BAGIAN KIRI: DAFTAR TIM SALES & ALOKASI ANGGOTA (lg:col-span-2) -->
                <div class="lg:col-span-2 flex flex-col gap-6">
                    
                    <!-- 1. DAFTAR TIM SALES -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h3 class="font-bold text-lg mb-4 border-b pb-2 text-indigo-700">Daftar Tim Sales</h3>
                        
                        <div class="overflow-x-auto w-full">
                            <table class="w-full table-auto border-collapse border border-gray-300 text-sm whitespace-nowrap mb-4">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border px-4 py-2 text-left">Nama Tim</th>
                                        <th class="border px-4 py-2 text-left">Ketua Tim (Leader)</th>
                                        <th class="border px-4 py-2 text-right">Target Bulanan (Rp)</th>
                                        <th class="border px-4 py-2 text-center">Jumlah Anggota</th>
                                        <th class="border px-4 py-2 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salesTeams as $team)
                                    <tr>
                                        <td class="border px-4 py-2 font-bold text-gray-700">{{ $team->name }}</td>
                                        <td class="border px-4 py-2">
                                            {{ $team->leader->name ?? 'Belum Ditunjuk' }}
                                        </td>
                                        <td class="border px-4 py-2 text-right font-mono font-bold">
                                            Rp {{ number_format($team->monthly_target, 0, ',', '.') }}
                                        </td>
                                        <td class="border px-4 py-2 text-center font-bold text-blue-600">
                                            {{ $team->members->count() }} Orang
                                        </td>
                                        <td class="border px-4 py-2 text-center flex gap-2 justify-center">
                                            <!-- Edit Form Toggle -->
                                            <button onclick="toggleEditForm({{ $team->id }}, '{{ $team->name }}', {{ $team->leader_id ?? 'null' }}, {{ $team->monthly_target }}, '{{ $team->notes }}')" class="text-blue-600 hover:text-blue-900 text-xs font-bold border border-blue-600 px-2 py-1 rounded">
                                                Edit
                                            </button>
                                            <form action="{{ route('sales-teams.destroy', ['sales_team' => $team->id]) }}" method="POST" onsubmit="return confirm('Hapus tim sales ini? Anggota akan dialokasikan menjadi tanpa tim.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-bold border border-red-600 px-2 py-1 rounded">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="border px-4 py-8 text-center text-gray-500 italic">Belum ada data tim sales. Silakan buat di form sebelah kanan.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 2. ALOKASI ANGGOTA TIM -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h3 class="font-bold text-lg mb-4 border-b pb-2 text-indigo-700">Alokasikan Anggota (Staf Sales)</h3>
                        
                        <form action="{{ route('sales-teams.assign-members') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Anggota Staf <span class="text-red-500">*</span></label>
                                <div class="max-h-48 overflow-y-auto border p-3 rounded-md bg-gray-50">
                                    @foreach($users as $user)
                                        <div class="flex items-center mb-2">
                                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" id="user_chk_{{ $user->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <label for="user_chk_{{ $user->id }}" class="ml-2 text-sm text-gray-700 cursor-pointer">
                                                <span class="font-bold">{{ $user->name }}</span> ({{ $user->email }}) 
                                                @if($user->salesTeam)
                                                    <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded-full font-bold ml-2">Tim: {{ $user->salesTeam->name }}</span>
                                                @else
                                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-bold ml-2">Tanpa Tim</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-4 items-end">
                                <div class="w-full sm:w-2/3">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Pindahkan ke Tim Sales:</label>
                                    <select name="sales_team_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                        <option value="">-- Hapus dari Tim (Keluarkan) --</option>
                                        @foreach($salesTeams as $team)
                                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-full sm:w-1/3">
                                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded font-bold shadow text-sm transition">
                                        Terapkan Alokasi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

                <!-- BAGIAN KANAN: FORM TAMBAH / EDIT TIM (lg:col-span-1) -->
                <div class="lg:col-span-1">
                    
                    <!-- FORM TAMBAH -->
                    <div id="addTeamBox" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h3 class="font-bold text-lg mb-4 border-b pb-2 text-indigo-700 uppercase">Tambah Tim Sales Baru</h3>
                        
                        <form action="{{ route('sales-teams.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Tim Sales <span class="text-red-500">*</span></label>
                                <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="Cth: Tim Cabang Semarang">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Ketua Tim (Leader)</label>
                                <select name="leader_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">-- Pilih Ketua Tim --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Target Penjualan Bulanan (Rp) <span class="text-red-500">*</span></label>
                                <div class="flex">
                                    <span class="bg-gray-200 border border-gray-300 text-gray-600 px-3 py-2 rounded-l-md text-sm font-bold">Rp</span>
                                    <input type="number" name="monthly_target" class="w-full border-gray-300 rounded-r-md shadow-sm font-bold" required min="0" value="10000000" placeholder="Masukkan target omzet...">
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Keterangan / Notes</label>
                                <textarea name="notes" placeholder="Opsional..." class="w-full border-gray-300 rounded-md shadow-sm h-20 text-sm"></textarea>
                            </div>

                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-lg font-bold text-center shadow-lg transition">
                                Simpan Tim Sales
                            </button>
                        </form>
                    </div>

                    <!-- FORM EDIT (HIDDEN BY DEFAULT) -->
                    <div id="editTeamBox" class="bg-indigo-50 p-6 rounded-lg shadow-sm border border-indigo-200 hidden">
                        <div class="flex justify-between items-center mb-4 border-b border-indigo-200 pb-2">
                            <h3 class="font-bold text-lg text-indigo-900 uppercase">Edit Tim Sales</h3>
                            <button onclick="cancelEdit()" class="text-gray-500 hover:text-gray-800 font-bold text-sm">Batal</button>
                        </div>
                        
                        <form id="editFormElement" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Tim Sales <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="edit_name" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Ketua Tim (Leader)</label>
                                <select name="leader_id" id="edit_leader_id" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">-- Pilih Ketua Tim --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Target Penjualan Bulanan (Rp) <span class="text-red-500">*</span></label>
                                <div class="flex">
                                    <span class="bg-gray-200 border border-gray-300 text-gray-600 px-3 py-2 rounded-l-md text-sm font-bold">Rp</span>
                                    <input type="number" name="monthly_target" id="edit_monthly_target" class="w-full border-gray-300 rounded-r-md shadow-sm font-bold" required min="0">
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Keterangan / Notes</label>
                                <textarea name="notes" id="edit_notes" class="w-full border-gray-300 rounded-md shadow-sm h-20 text-sm"></textarea>
                            </div>

                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-lg font-bold text-center shadow-lg transition">
                                Perbarui Tim Sales
                            </button>
                        </form>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- Script Edit Handler -->
    <script>
        function toggleEditForm(id, name, leaderId, monthlyTarget, notes) {
            document.getElementById('addTeamBox').classList.add('hidden');
            document.getElementById('editTeamBox').classList.remove('hidden');
            
            // Set values
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_monthly_target').value = monthlyTarget;
            document.getElementById('edit_notes').value = notes;
            
            var leaderSelect = document.getElementById('edit_leader_id');
            leaderSelect.value = leaderId !== null ? leaderId : "";
            
            // Set action URL
            var updateUrl = "{{ route('sales-teams.update', ['sales_team' => ':id']) }}";
            document.getElementById('editFormElement').action = updateUrl.replace(':id', id);
        }

        function cancelEdit() {
            document.getElementById('addTeamBox').classList.remove('hidden');
            document.getElementById('editTeamBox').classList.add('hidden');
        }
    </script>
</x-app-layout>
