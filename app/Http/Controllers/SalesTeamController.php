<?php

namespace App\Http\Controllers;

use App\Models\SalesTeam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesTeamController extends Controller
{
    /**
     * Tampilkan daftar tim sales & pembagian anggota
     */
    public function index()
    {
        $salesTeams = SalesTeam::with(['leader', 'members'])->get();
        
        // Ambil semua user untuk dipilih sebagai leader atau anggota
        $users = User::all();

        return view('sales_teams.index', compact('salesTeams', 'users'));
    }

    /**
     * Simpan tim sales baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sales_teams,name',
            'leader_id' => 'nullable|exists:users,id',
            'monthly_target' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        SalesTeam::create($request->only(['name', 'leader_id', 'monthly_target', 'notes']));

        return redirect()->route('sales-teams.index')->with('success', 'Tim Sales baru berhasil dibuat!');
    }

    /**
     * Update data tim sales
     */
    public function update(Request $request, SalesTeam $salesTeam)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sales_teams,name,' . $salesTeam->id,
            'leader_id' => 'nullable|exists:users,id',
            'monthly_target' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $salesTeam->update($request->only(['name', 'leader_id', 'monthly_target', 'notes']));

        return redirect()->route('sales-teams.index')->with('success', 'Data Tim Sales berhasil diperbarui!');
    }

    /**
     * Hapus tim sales
     */
    public function destroy(SalesTeam $salesTeam)
    {
        // Set null members' sales_team_id
        User::where('sales_team_id', $salesTeam->id)->update(['sales_team_id' => null]);
        
        $salesTeam->delete();

        return redirect()->route('sales-teams.index')->with('success', 'Tim Sales berhasil dihapus!');
    }

    /**
     * Alokasikan anggota user ke tim sales
     */
    public function assignMembers(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'sales_team_id' => 'nullable|exists:sales_teams,id',
        ]);

        User::whereIn('id', $request->user_ids)->update([
            'sales_team_id' => $request->sales_team_id
        ]);

        return redirect()->route('sales-teams.index')->with('success', 'Anggota berhasil dialokasikan ke Tim Sales!');
    }
}
