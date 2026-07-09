<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Laporan Kinerja Sales & Target Tim') }}
            </h2>
            <a href="{{ route('reports.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">Kembali ke Laporan</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filter Periode -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
                <form action="{{ route('reports.sales-performance') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
                    <div class="w-full sm:w-auto">
                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full sm:w-44 border-gray-300 rounded text-sm" required>
                    </div>
                    <div class="w-full sm:w-auto">
                        <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full sm:w-44 border-gray-300 rounded text-sm" required>
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded text-sm font-bold shadow transition w-full sm:w-auto">
                        Terapkan Filter
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                
                <!-- BAGIAN TIM SALES & TARGET PROGRESS (lg:col-span-2) -->
                <div class="lg:col-span-2 flex flex-col gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h3 class="font-bold text-lg mb-4 border-b pb-2 text-indigo-700 uppercase flex items-center gap-2">
                            <span>Kinerja Target Tim Sales</span>
                        </h3>

                        <div class="flex flex-col gap-6">
                            @forelse($teamsData as $teamData)
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <div class="flex justify-between items-center mb-2">
                                        <div>
                                            <h4 class="font-bold text-base text-gray-800">{{ $teamData['team']->name }}</h4>
                                            <span class="text-xs text-gray-500">Leader: <span class="font-bold text-gray-700">{{ $teamData['team']->leader->name ?? 'Belum Ada' }}</span></span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs text-gray-500 block uppercase">Pencapaian</span>
                                            <span class="text-md font-bold font-mono text-green-600">Rp {{ number_format($teamData['achieved_sales'], 0, ',', '.') }}</span>
                                            <span class="text-xs text-gray-400">/ Rp {{ number_format($teamData['target'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="w-full bg-gray-200 rounded-full h-3 mb-2 overflow-hidden">
                                        <div class="bg-green-500 h-3 rounded-full transition-all duration-500" style="width: {{ min($teamData['progress'], 100) }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs font-bold text-gray-500">
                                        <span>Progres Target: {{ $teamData['progress'] }}%</span>
                                        @if($teamData['achieved_sales'] >= $teamData['target'])
                                            <span class="text-green-600 font-extrabold uppercase">🎉 Target Tercapai!</span>
                                        @else
                                            <span class="text-yellow-600">Sisa: Rp {{ number_format(max($teamData['target'] - $teamData['achieved_sales'], 0), 0, ',', '.') }}</span>
                                        @endif
                                    </div>

                                    <!-- Rincian Anggota Tim -->
                                    @if(count($teamData['members']) > 0)
                                        <div class="mt-4 border-t pt-3">
                                            <span class="text-xs font-bold text-gray-500 block uppercase mb-2">Kontribusi Anggota Tim:</span>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                @foreach($teamData['members'] as $member)
                                                    <div class="bg-white p-2 rounded border flex justify-between items-center text-xs shadow-xxs">
                                                        <span class="font-bold text-gray-700">{{ $member['name'] }}</span>
                                                        <span class="font-bold font-mono text-indigo-600">Rp {{ number_format($member['total_sales'], 0, ',', '.') }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500 italic">Belum ada tim sales yang dikonfigurasi.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- BAGIAN KANAN: PAPAN SKOR INDIVIDU (LEADERBOARD) (lg:col-span-1) -->
                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h3 class="font-bold text-lg mb-4 border-b pb-2 text-indigo-700 uppercase flex items-center gap-2">
                            <span>🏆 Papan Skor Sales</span>
                        </h3>

                        <div class="flex flex-col gap-3">
                            @forelse($leaderboard as $index => $row)
                                <div class="flex items-center gap-3 p-3 rounded-lg border {{ $index === 0 ? 'bg-yellow-50 border-yellow-200 shadow-sm' : 'bg-gray-50 border-gray-200' }}">
                                    <!-- Posisi Medal -->
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-sm {{ $index === 0 ? 'bg-yellow-400 text-yellow-950' : ($index === 1 ? 'bg-gray-300 text-gray-800' : ($index === 2 ? 'bg-orange-300 text-orange-900' : 'bg-gray-200 text-gray-600')) }}">
                                        {{ $index + 1 }}
                                    </div>
                                    
                                    <!-- Informasi Staf -->
                                    <div class="flex-grow">
                                        <h4 class="font-bold text-sm text-gray-800">{{ $row['user']->name }}</h4>
                                        <span class="text-xxs text-gray-500 block">Tim: <span class="font-bold text-gray-600">{{ $row['team_name'] }}</span></span>
                                    </div>

                                    <!-- Total Omzet -->
                                    <div class="text-right">
                                        <span class="text-sm font-black font-mono text-indigo-600 block">Rp {{ number_format($row['total_sales'], 0, ',', '.') }}</span>
                                        <span class="text-xxs text-gray-400 block">Kasir: Rp {{ number_format($row['direct_sales'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500 italic">Belum ada data penjualan staf.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
