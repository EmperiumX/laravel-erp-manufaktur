<?php

namespace App\Http\Controllers;

use App\Models\CashBank;
use App\Models\CashBankTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashBankController extends Controller
{
    public function index()
    {
        $cashBanks = CashBank::withCount('transactions')->get();
        $totalCash = CashBank::cash()->active()->sum('balance');
        $totalBank = CashBank::bank()->active()->sum('balance');
        return view('cash_banks.index', compact('cashBanks', 'totalCash', 'totalBank'));
    }

    public function create()
    {
        return view('cash_banks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Cash,Bank',
            'account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'balance' => 'nullable|numeric|min:0',
        ]);

        $cashBank = CashBank::create($request->all());

        if ($request->balance > 0) {
            CashBankTransaction::create([
                'cash_bank_id' => $cashBank->id,
                'type' => 'Debit',
                'amount' => $request->balance,
                'balance_after' => $request->balance,
                'transaction_date' => now()->format('Y-m-d'),
                'description' => 'Saldo Awal',
                'category' => 'Setoran Modal',
                'is_reconciled' => true,
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->route('cash-banks.index')->with('success', 'Akun Kas/Bank berhasil ditambahkan!');
    }

    public function show(Request $request, CashBank $cashBank)
    {
        $query = $cashBank->transactions()->with('creator');
        if ($request->filled('start_date')) {
            $query->where('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('transaction_date', '<=', $request->end_date);
        }
        if ($request->filled('reconciled')) {
            $query->where('is_reconciled', $request->reconciled === 'yes');
        }
        $transactions = $query->latest('transaction_date')->get();
        return view('cash_banks.show', compact('cashBank', 'transactions'));
    }

    public function edit(CashBank $cashBank)
    {
        return view('cash_banks.edit', compact('cashBank'));
    }

    public function update(Request $request, CashBank $cashBank)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Cash,Bank',
            'account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);
        $cashBank->update($request->all());
        return redirect()->route('cash-banks.index')->with('success', 'Akun Kas/Bank berhasil diperbarui!');
    }

    public function addTransaction(Request $request, CashBank $cashBank)
    {
        $request->validate([
            'type' => 'required|in:Debit,Credit',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
            'category' => 'required|string',
        ]);

        if ($request->type === 'Debit') {
            $cashBank->balance += $request->amount;
        } else {
            $cashBank->balance -= $request->amount;
        }
        $cashBank->save();

        CashBankTransaction::create([
            'cash_bank_id' => $cashBank->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'balance_after' => $cashBank->balance,
            'transaction_date' => $request->transaction_date,
            'reference' => $request->reference,
            'description' => $request->description,
            'category' => $request->category,
            'is_reconciled' => false,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Transaksi berhasil dicatat!');
    }

    public function reconcile(Request $request, CashBank $cashBank)
    {
        $request->validate([
            'transaction_ids' => 'required|array',
            'transaction_ids.*' => 'exists:cash_bank_transactions,id',
        ]);
        CashBankTransaction::where('cash_bank_id', $cashBank->id)
            ->whereIn('id', $request->transaction_ids)
            ->update(['is_reconciled' => true]);
        return back()->with('success', count($request->transaction_ids) . ' transaksi berhasil direkonsiliasi!');
    }

    public function destroy(CashBank $cashBank)
    {
        if ($cashBank->transactions()->count() > 0) {
            return back()->with('error', 'Akun yang sudah memiliki transaksi tidak bisa dihapus.');
        }
        $cashBank->delete();
        return redirect()->route('cash-banks.index')->with('success', 'Akun Kas/Bank berhasil dihapus!');
    }
}
