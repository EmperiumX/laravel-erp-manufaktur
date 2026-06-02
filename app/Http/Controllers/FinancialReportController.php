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
        
        $cogs = PurchaseOrder::where('status', 'Completed')
            ->whereBetween('order_date', [$currentMonthStart, $currentMonthEnd])
            ->sum('total_amount');
            
        $operationalExpenses = CashBankTransaction::where('type', 'Credit')
            ->whereIn('category', ['Biaya Operasional', 'Gaji'])
            ->whereBetween('transaction_date', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');
            
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

        // HPP (Harga Pokok Penjualan) dari PO yang Completed
        $cogs = PurchaseOrder::where('status', 'Completed')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->sum('total_amount');

        // Biaya operasional dari kas/bank
        $operationalExpenses = CashBankTransaction::where('type', 'Credit')
            ->whereIn('category', ['Biaya Operasional', 'Gaji'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $grossProfit = $totalRevenue - $cogs;
        $netProfit = $grossProfit - $operationalExpenses;

        return view('reports.profit_loss', compact(
            'startDate', 'endDate', 'salesRevenue', 'consignmentRevenue',
            'totalRevenue', 'cogs', 'operationalExpenses', 'grossProfit', 'netProfit'
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
}
