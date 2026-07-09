<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\CashBank;
use App\Models\CashBankTransaction;
use App\Models\PurchaseOrder;
use App\Models\DirectSale;
use App\Models\ConsignmentShipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;

class FinancialReportController extends Controller
{
    public function index()
    {
        $currentMonthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentMonthEnd = Carbon::now()->endOfMonth()->format('Y-m-d');
        $startDate = $currentMonthStart;
        $endDate = $currentMonthEnd;

        // 1. Laba Rugi (Bulan Ini)
        $salesRevenue = DirectSale::whereBetween('sale_date', [$currentMonthStart, $currentMonthEnd])->sum('total_amount');
        
        $consignmentRevenue = Invoice::sales()
            ->whereNotNull('consignment_shipment_id')
            ->whereBetween('invoice_date', [$currentMonthStart, $currentMonthEnd])
            ->sum('total_amount');
            
        $totalRevenue = $salesRevenue + $consignmentRevenue;
        
        $directSaleCogs = DB::table('direct_sale_items')
            ->join('direct_sales', 'direct_sale_items.direct_sale_id', '=', 'direct_sales.id')
            ->join('products', 'direct_sale_items.product_id', '=', 'products.id')
            ->whereBetween('direct_sales.sale_date', [$currentMonthStart, $currentMonthEnd])
            ->sum(DB::raw('direct_sale_items.quantity * products.hpp')) ?? 0;

        $consignmentCogs = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->where('invoices.type', 'sales')
            ->whereNotNull('invoices.consignment_shipment_id')
            ->whereBetween('invoices.invoice_date', [$currentMonthStart, $currentMonthEnd])
            ->sum(DB::raw('invoice_items.quantity * products.hpp')) ?? 0;

        $cogs = $directSaleCogs + $consignmentCogs;
            
        $operationalExpenses = CashBankTransaction::where('type', 'Credit')
            ->where('category', '!=', 'Pembayaran Hutang')
            ->whereBetween('transaction_date', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount') ?? 0;
            
        $grossProfit = $totalRevenue - $cogs;
        $netProfit = $grossProfit - $operationalExpenses;

        // 2. Arus Kas (Bulan Ini)
        $cashIn = CashBankTransaction::where('type', 'Debit')
            ->whereBetween('transaction_date', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');
            
        $cashOut = CashBankTransaction::where('type', 'Credit')
            ->whereBetween('transaction_date', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');
            
        $netCashFlow = $cashIn - $cashOut;

        // 3. Piutang Dagang (Outstanding)
        $invoicesSales = Invoice::sales()->unpaid()->get();
        $totalPiutang = $invoicesSales->sum(function ($inv) {
            return $inv->total_amount - $inv->paid_amount;
        });
        $overduePiutangCount = $invoicesSales->filter(fn($inv) => $inv->is_overdue)->count();

        // 4. Hutang Dagang (Outstanding)
        $invoicesPurchase = Invoice::purchase()->unpaid()->get();
        $totalHutang = $invoicesPurchase->sum(function ($inv) {
            return $inv->total_amount - $inv->paid_amount;
        });
        $overdueHutangCount = $invoicesPurchase->filter(fn($inv) => $inv->is_overdue)->count();

        // Saldo Akun Kas & Bank
        $cashBanks = CashBank::active()->get();
        $totalCashBalance = $cashBanks->sum('balance');

        return view('reports.index', compact(
            'startDate', 'endDate',
            'salesRevenue', 'consignmentRevenue', 'totalRevenue', 'cogs', 'operationalExpenses', 'grossProfit', 'netProfit',
            'cashIn', 'cashOut', 'netCashFlow',
            'totalPiutang', 'overduePiutangCount',
            'totalHutang', 'overdueHutangCount',
            'cashBanks', 'totalCashBalance'
        ));
    }

    /**
     * Laporan Laba Rugi
     */
    public function profitLoss(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Pendapatan dari penjualan langsung
        $salesRevenue = DirectSale::whereBetween('sale_date', [$startDate, $endDate])->sum('total_amount');

        // Pendapatan dari konsinyasi (yang sudah di-invoice)
        $consignmentRevenue = Invoice::sales()
            ->whereNotNull('consignment_shipment_id')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->sum('total_amount');

        $totalRevenue = $salesRevenue + $consignmentRevenue;

        $directSaleCogs = DB::table('direct_sale_items')
            ->join('direct_sales', 'direct_sale_items.direct_sale_id', '=', 'direct_sales.id')
            ->join('products', 'direct_sale_items.product_id', '=', 'products.id')
            ->whereBetween('direct_sales.sale_date', [$startDate, $endDate])
            ->sum(DB::raw('direct_sale_items.quantity * products.hpp')) ?? 0;

        $consignmentCogs = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->where('invoices.type', 'sales')
            ->whereNotNull('invoices.consignment_shipment_id')
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->sum(DB::raw('invoice_items.quantity * products.hpp')) ?? 0;

        $cogs = $directSaleCogs + $consignmentCogs;

        // Biaya operasional dari kas/bank
        $operationalExpenses = CashBankTransaction::where('type', 'Credit')
            ->where('category', '!=', 'Pembayaran Hutang')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount') ?? 0;

        $expenseBreakdown = CashBankTransaction::where('type', 'Credit')
            ->where('category', '!=', 'Pembayaran Hutang')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        $expenseTransactions = CashBankTransaction::with('cashBank')
            ->where('type', 'Credit')
            ->where('category', '!=', 'Pembayaran Hutang')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();

        $grossProfit = $totalRevenue - $cogs;
        $netProfit = $grossProfit - $operationalExpenses;

        return view('reports.profit_loss', compact(
            'startDate', 'endDate', 'salesRevenue', 'consignmentRevenue',
            'totalRevenue', 'cogs', 'operationalExpenses', 'grossProfit', 'netProfit',
            'expenseBreakdown', 'expenseTransactions'
        ));
    }

    /**
     * Laporan Piutang (Accounts Receivable)
     */
    public function accountsReceivable(Request $request)
    {
        $invoices = Invoice::sales()->unpaid()
            ->with(['store', 'payments'])
            ->latest('due_date')
            ->get();

        $totalPiutang = $invoices->sum(function ($inv) {
            return $inv->total_amount - $inv->paid_amount;
        });

        $overdue = $invoices->filter(fn($inv) => $inv->is_overdue);

        return view('reports.accounts_receivable', compact('invoices', 'totalPiutang', 'overdue'));
    }

    /**
     * Laporan Hutang (Accounts Payable)
     */
    public function accountsPayable(Request $request)
    {
        $invoices = Invoice::purchase()->unpaid()
            ->with(['supplier', 'payments'])
            ->latest('due_date')
            ->get();

        $totalHutang = $invoices->sum(function ($inv) {
            return $inv->total_amount - $inv->paid_amount;
        });

        $overdue = $invoices->filter(fn($inv) => $inv->is_overdue);

        return view('reports.accounts_payable', compact('invoices', 'totalHutang', 'overdue'));
    }

    /**
     * Laporan Arus Kas
     */
    public function cashFlow(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $transactions = CashBankTransaction::with(['cashBank', 'creator'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date')
            ->get();

        $totalDebit = $transactions->where('type', 'Debit')->sum('amount');
        $totalCredit = $transactions->where('type', 'Credit')->sum('amount');

        // Group by category
        $byCategory = $transactions->groupBy('category')->map(function ($items, $category) {
            return [
                'category' => $category,
                'debit' => $items->where('type', 'Debit')->sum('amount'),
                'credit' => $items->where('type', 'Credit')->sum('amount'),
            ];
        });

        $cashBanks = CashBank::active()->get();

        return view('reports.cash_flow', compact(
            'startDate', 'endDate', 'transactions',
            'totalDebit', 'totalCredit', 'byCategory', 'cashBanks'
        ));
    }

    /**
     * Laporan Kinerja Sales & Tim
     */
    public function salesPerformance(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // 1. Ambil semua Tim Sales
        $teams = \App\Models\SalesTeam::with(['leader', 'members'])->get();

        $teamsData = [];
        foreach ($teams as $team) {
            $achievedSales = 0;
            $membersData = [];

            foreach ($team->members as $member) {
                // Penjualan Langsung oleh Staf Sales
                $directSales = \App\Models\DirectSale::where('cashier_id', $member->id)
                    ->whereBetween('sale_date', [$startDate, $endDate])
                    ->sum('total_amount');

                // Invoice Sales oleh Staf Sales (konsinyasi, non-direct sales)
                $invoiceSales = Invoice::sales()
                    ->where('created_by', $member->id)
                    ->whereNull('direct_sale_id')
                    ->whereBetween('invoice_date', [$startDate, $endDate])
                    ->sum('total_amount');

                $totalSales = $directSales + $invoiceSales;
                $achievedSales += $totalSales;

                $membersData[] = [
                    'id' => $member->id,
                    'name' => $member->name,
                    'direct_sales' => $directSales,
                    'invoice_sales' => $invoiceSales,
                    'total_sales' => $totalSales,
                ];
            }

            // Target Progress
            $progressPercentage = $team->monthly_target > 0 ? min(round(($achievedSales / $team->monthly_target) * 100, 2), 1000) : 100;

            $teamsData[] = [
                'team' => $team,
                'achieved_sales' => $achievedSales,
                'target' => $team->monthly_target,
                'progress' => $progressPercentage,
                'members' => $membersData,
            ];
        }

        // 2. Leaderboard Sales (Semua user dengan role 'Sales' atau yang punya transaksi penjualan)
        $salesUsers = User::role('Sales')->get();
        if ($salesUsers->isEmpty()) {
            // Fallback: ambil user yang memiliki transaksi direct_sales
            $userIdsWithSales = \App\Models\DirectSale::distinct()->pluck('cashier_id');
            $salesUsers = User::whereIn('id', $userIdsWithSales)->get();
        }

        $leaderboard = [];
        foreach ($salesUsers as $user) {
            $directSales = \App\Models\DirectSale::where('cashier_id', $user->id)
                ->whereBetween('sale_date', [$startDate, $endDate])
                ->sum('total_amount');

            $invoiceSales = Invoice::sales()
                ->where('created_by', $user->id)
                ->whereNull('direct_sale_id')
                ->whereBetween('invoice_date', [$startDate, $endDate])
                ->sum('total_amount');

            $totalSales = $directSales + $invoiceSales;

            $leaderboard[] = [
                'user' => $user,
                'team_name' => $user->salesTeam->name ?? 'Tanpa Tim',
                'direct_sales' => $directSales,
                'invoice_sales' => $invoiceSales,
                'total_sales' => $totalSales,
            ];
        }

        // Sort leaderboard by total sales descending
        usort($leaderboard, function($a, $b) {
            return $b['total_sales'] <=> $a['total_sales'];
        });

        return view('reports.sales_performance', compact('startDate', 'endDate', 'teamsData', 'leaderboard'));
    }

    /**
     * Laporan Jurnal Umum & Jurnal per Mitra
     */
    public function generalJournal(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $storeId = $request->input('store_id');
        $supplierId = $request->input('supplier_id');

        $transactions = [];

        // 1. Penjualan Langsung
        $salesQuery = \App\Models\DirectSale::with('store')
            ->whereBetween('sale_date', [$startDate, $endDate]);
        if ($storeId) {
            $salesQuery->where('store_id', $storeId);
        }
        if (!$supplierId) {
            foreach ($salesQuery->get() as $item) {
                $transactions[] = [
                    'date' => $item->sale_date instanceof Carbon ? $item->sale_date->format('Y-m-d') : Carbon::parse($item->sale_date)->format('Y-m-d'),
                    'type' => 'Penjualan Langsung',
                    'reference' => $item->invoice_number,
                    'party' => $item->store?->name ?? $item->customer_name,
                    'description' => $item->notes ?? 'Penjualan langsung kasir',
                    'amount' => $item->total_amount,
                    'details_url' => route('direct-sales.show', $item->id),
                ];
            }
        }

        // 2. Konsinyasi (DO)
        $consQuery = \App\Models\ConsignmentShipment::with('store')
            ->whereBetween('shipment_date', [$startDate, $endDate]);
        if ($storeId) {
            $consQuery->where('store_id', $storeId);
        }
        if (!$supplierId) {
            foreach ($consQuery->get() as $item) {
                $transactions[] = [
                    'date' => $item->shipment_date instanceof Carbon ? $item->shipment_date->format('Y-m-d') : Carbon::parse($item->shipment_date)->format('Y-m-d'),
                    'type' => 'Konsinyasi (DO)',
                    'reference' => $item->shipment_number,
                    'party' => $item->store?->name ?? '-',
                    'description' => $item->notes ?? 'Pengiriman konsinyasi',
                    'amount' => $item->total_amount,
                    'details_url' => route('consignments.show', $item->id),
                ];
            }
        }

        // 3. Purchase Orders
        $poQuery = \App\Models\PurchaseOrder::with('supplier')
            ->whereBetween('order_date', [$startDate, $endDate]);
        if ($supplierId) {
            $poQuery->where('supplier_id', $supplierId);
        }
        if (!$storeId) {
            foreach ($poQuery->get() as $item) {
                $transactions[] = [
                    'date' => $item->order_date instanceof Carbon ? $item->order_date->format('Y-m-d') : Carbon::parse($item->order_date)->format('Y-m-d'),
                    'type' => 'Purchase Order',
                    'reference' => $item->po_number,
                    'party' => $item->supplier?->name ?? '-',
                    'description' => $item->notes ?? 'Pemesanan pembelian',
                    'amount' => $item->total_amount,
                    'details_url' => route('purchase-orders.show', $item->id),
                ];
            }
        }

        // 4. Invoices (Faktur)
        $invQuery = \App\Models\Invoice::with(['store', 'supplier'])
            ->whereBetween('invoice_date', [$startDate, $endDate]);
        if ($storeId) {
            $invQuery->where('store_id', $storeId);
        }
        if ($supplierId) {
            $invQuery->where('supplier_id', $supplierId);
        }
        foreach ($invQuery->get() as $item) {
            $transactions[] = [
                'date' => $item->invoice_date->format('Y-m-d'),
                'type' => $item->type === 'sales' ? 'Faktur Penjualan' : 'Faktur Pembelian',
                'reference' => $item->invoice_number,
                'party' => $item->type === 'sales' ? ($item->store?->name ?? '-') : ($item->supplier?->name ?? '-'),
                'description' => $item->notes ?? 'Faktur komersial',
                'amount' => $item->total_amount,
                'details_url' => route('invoices.show', $item->id),
            ];
        }

        // 5. Payments
        $payQuery = \App\Models\Payment::with(['invoice.store', 'invoice.supplier'])
            ->whereBetween('payment_date', [$startDate, $endDate]);
        if ($storeId) {
            $payQuery->whereHas('invoice', fn($q) => $q->where('store_id', $storeId));
        }
        if ($supplierId) {
            $payQuery->whereHas('invoice', fn($q) => $q->where('supplier_id', $supplierId));
        }
        foreach ($payQuery->get() as $item) {
            $transactions[] = [
                'date' => $item->payment_date->format('Y-m-d'),
                'type' => $item->type === 'inbound' ? 'Pembayaran Masuk' : 'Pembayaran Keluar',
                'reference' => $item->payment_number,
                'party' => $item->type === 'inbound' ? ($item->invoice?->store?->name ?? '-') : ($item->invoice?->supplier?->name ?? '-'),
                'description' => $item->notes ?? 'Pelunasan faktur',
                'amount' => $item->amount,
                'details_url' => route('payments.show', $item->id),
            ];
        }

        // 6. Cash Transactions / Manual Journals (Tampil jika tidak sedang difilter Mitra/Supplier tertentu)
        if (!$storeId && !$supplierId) {
            $cashQuery = \App\Models\CashBankTransaction::with('cashBank')
                ->whereNull('payment_id')
                ->whereBetween('transaction_date', [$startDate, $endDate]);
            foreach ($cashQuery->get() as $item) {
                $isJV = $item->reference && (strpos($item->reference, 'JV-') === 0);
                $transactions[] = [
                    'date' => $item->transaction_date->format('Y-m-d'),
                    'type' => $isJV ? 'Jurnal Voucher' : ($item->type === 'Debit' ? 'Kas Masuk (Lain-lain)' : 'Kas Keluar / Biaya'),
                    'reference' => $item->reference ?? 'TRX-' . $item->id,
                    'party' => $item->cashBank?->name ?? '-',
                    'description' => $item->description . ' [' . $item->category . ']',
                    'amount' => $item->amount,
                    'details_url' => route('cash-banks.show', $item->cash_bank_id),
                ];
            }
        }

        // Urutkan transaksi berdasarkan tanggal terbaru (descending)
        usort($transactions, function ($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        // Ambil data Toko dan Supplier untuk opsi filter pencarian
        $stores = \App\Models\Store::orderBy('name')->get();
        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        return view('reports.general_journal', compact('startDate', 'endDate', 'storeId', 'supplierId', 'transactions', 'stores', 'suppliers'));
    }

    /**
     * Form Jurnal Voucher Manual
     */
    public function createJournal()
    {
        $cashBanks = \App\Models\CashBank::active()->get();
        return view('reports.create_journal', compact('cashBanks'));
    }

    /**
     * Simpan Jurnal Voucher Manual
     */
    public function storeJournal(Request $request)
    {
        $request->validate([
            'cash_bank_id' => 'required|exists:cash_banks,id',
            'type' => 'required|in:Debit,Credit',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
            'category' => 'required|string|max:100',
        ]);

        $cashBank = \App\Models\CashBank::findOrFail($request->cash_bank_id);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Generate Journal Voucher Reference Number (JV-YYYYMMDD-001)
            $today = date('Ymd');
            $countToday = \App\Models\CashBankTransaction::where('reference', 'like', 'JV-' . $today . '-%')->count();
            $jvNumber = 'JV-' . $today . '-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

            // Update CashBank Balance
            if ($request->type === 'Debit') {
                $cashBank->balance += $request->amount;
            } else {
                $cashBank->balance -= $request->amount;
            }
            $cashBank->save();

            // Create Transaction record
            \App\Models\CashBankTransaction::create([
                'cash_bank_id' => $cashBank->id,
                'type' => $request->type,
                'amount' => $request->amount,
                'balance_after' => $cashBank->balance,
                'transaction_date' => $request->transaction_date,
                'reference' => $jvNumber,
                'description' => $request->description,
                'category' => $request->category,
                'is_reconciled' => false,
                'created_by' => auth()->id(),
            ]);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('reports.general-journal')
                ->with('success', 'Jurnal Voucher manual ' . $jvNumber . ' berhasil disimpan!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal menyimpan Jurnal Voucher: ' . $e->getMessage());
        }
    }
}
