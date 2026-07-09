<?php

namespace App\Http\Controllers;

use App\Models\CashierSession;
use App\Models\CashBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierSessionController extends Controller
{
    /**
     * Tampilkan riwayat sesi kasir
     */
    public function index()
    {
        // Owner/Admin bisa melihat semua sesi, Kasir biasa hanya melihat sesinya sendiri
        $query = CashierSession::with(['user', 'cashBank']);
        
        if (!Auth::user()->hasRole('Superadmin') && !Auth::user()->hasRole('Admin')) {
            $query->where('user_id', Auth::id());
        }

        $sessions = $query->latest()->get();

        return view('cashier_sessions.index', compact('sessions'));
    }

    /**
     * Form pembukaan sesi kasir baru
     */
    public function create()
    {
        // Cek apakah ada sesi aktif untuk user ini
        $activeSession = CashierSession::where('user_id', Auth::id())
            ->where('status', 'Open')
            ->first();

        if ($activeSession) {
            return redirect()->route('cashier-sessions.show', $activeSession->id)
                ->with('info', 'Anda memiliki sesi kasir yang sedang aktif.');
        }

        $cashBanks = CashBank::active()->get();

        return view('cashier_sessions.create', compact('cashBanks'));
    }

    /**
     * Buka sesi kasir baru (simpan ke DB)
     */
    public function storeOpen(Request $request)
    {
        $request->validate([
            'cash_bank_id' => 'required|exists:cash_banks,id',
            'opening_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Cek lagi untuk mencegah double session
        $activeSession = CashierSession::where('user_id', Auth::id())
            ->where('status', 'Open')
            ->first();

        if ($activeSession) {
            return redirect()->route('cashier-sessions.show', $activeSession->id)
                ->with('error', 'Gagal! Anda sudah memiliki sesi aktif.');
        }

        CashierSession::create([
            'user_id' => Auth::id(),
            'cash_bank_id' => $request->cash_bank_id,
            'opening_cash' => $request->opening_cash,
            'expected_cash' => $request->opening_cash, // Awalnya expected cash = opening cash
            'status' => 'Open',
            'opening_date' => now(),
            'notes' => $request->notes,
        ]);

        return redirect()->route('direct-sales.create')
            ->with('success', 'Sesi kasir berhasil dibuka! Selamat bertugas.');
    }

    /**
     * Detail Sesi Kasir & Form Tutup Kasir
     */
    public function show(CashierSession $cashierSession)
    {
        // Proteksi hak akses
        if (!Auth::user()->hasRole('Superadmin') && !Auth::user()->hasRole('Admin') && $cashierSession->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $cashierSession->load(['user', 'cashBank', 'directSales.store']);

        return view('cashier_sessions.show', compact('cashierSession'));
    }

    /**
     * Tutup sesi kasir (proses rekonsiliasi kas)
     */
    public function storeClose(Request $request, CashierSession $cashierSession)
    {
        // Proteksi hak akses
        if (!Auth::user()->hasRole('Superadmin') && !Auth::user()->hasRole('Admin') && $cashierSession->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($cashierSession->status !== 'Open') {
            return redirect()->route('cashier-sessions.show', $cashierSession->id)
                ->with('error', 'Sesi ini sudah ditutup sebelumnya.');
        }

        $request->validate([
            'closing_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Hitung selisih
            $expected = $cashierSession->expected_cash;
            $closing = $request->closing_cash;
            $difference = $closing - $expected;

            $cashierSession->update([
                'closing_cash' => $closing,
                'difference' => $difference,
                'status' => 'Closed',
                'closing_date' => now(),
                'notes' => $cashierSession->notes . "\n[Catatan Penutupan]: " . $request->notes,
            ]);

            DB::commit();

            return redirect()->route('cashier-sessions.show', $cashierSession->id)
                ->with('success', 'Sesi kasir berhasil ditutup! Laporan rekonsiliasi kas telah dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menutup sesi kasir: ' . $e->getMessage());
        }
    }
}
