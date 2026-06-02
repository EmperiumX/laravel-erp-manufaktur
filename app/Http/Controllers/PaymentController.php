<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\CashBank;
use App\Models\CashBankTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Daftar semua pembayaran
     */
    public function index(Request $request)
    {
        $type = $request->query('type'); // 'inbound' atau 'outbound'

        $query = Payment::with(['invoice.store', 'invoice.supplier', 'cashBank', 'creator']);

        if ($type === 'inbound') {
            $query->inbound();
        } elseif ($type === 'outbound') {
            $query->outbound();
        }

        $payments = $query->latest()->get();

        return view('payments.index', compact('payments', 'type'));
    }

    /**
     * Form buat pembayaran baru
     */
    public function create(Request $request)
    {
        $invoiceId = $request->query('invoice_id');
        $invoice = null;

        if ($invoiceId) {
            $invoice = Invoice::with(['store', 'supplier'])->findOrFail($invoiceId);
        }

        // Invoice yang belum lunas
        $unpaidInvoices = Invoice::unpaid()
            ->with(['store', 'supplier'])
            ->latest()
            ->get();

        // Akun kas/bank aktif
        $cashBanks = CashBank::active()->get();

        return view('payments.create', compact('invoice', 'unpaidInvoices', 'cashBanks'));
    }

    /**
     * Simpan pembayaran & update saldo
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:Cash,Transfer,Giro,Cek,Lainnya',
            'cash_bank_id' => 'nullable|exists:cash_banks,id',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        // Validasi: pembayaran tidak boleh melebihi sisa tagihan
        $balanceDue = $invoice->total_amount - $invoice->paid_amount;
        if ($request->amount > $balanceDue) {
            return back()->with('error', 'Jumlah pembayaran melebihi sisa tagihan (Rp ' . number_format($balanceDue, 0, ',', '.') . ').');
        }

        DB::beginTransaction();
        try {
            // Generate nomor pembayaran
            $today = date('Ymd');
            $countToday = Payment::whereDate('created_at', date('Y-m-d'))->count();
            $paymentNumber = 'PAY-' . $today . '-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

            // Tentukan tipe pembayaran
            $paymentType = $invoice->type === 'sales' ? 'inbound' : 'outbound';

            // 1. Simpan pembayaran
            $payment = Payment::create([
                'payment_number' => $paymentNumber,
                'invoice_id' => $invoice->id,
                'type' => $paymentType,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'reference' => $request->reference,
                'cash_bank_id' => $request->cash_bank_id,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            // 2. Update paid_amount & status invoice
            $invoice->paid_amount += $request->amount;
            if ($invoice->paid_amount >= $invoice->total_amount) {
                $invoice->status = 'Paid';
            } else {
                $invoice->status = 'Partial';
            }
            $invoice->save();

            // 3. Update saldo Kas/Bank jika ada
            if ($request->cash_bank_id) {
                $cashBank = CashBank::findOrFail($request->cash_bank_id);

                if ($paymentType === 'inbound') {
                    // Uang masuk dari pelanggan
                    $cashBank->balance += $request->amount;
                    $transType = 'Debit';
                    $category = 'Pembayaran Piutang';
                } else {
                    // Uang keluar ke supplier
                    $cashBank->balance -= $request->amount;
                    $transType = 'Credit';
                    $category = 'Pembayaran Hutang';
                }
                $cashBank->save();

                // Catat transaksi kas/bank
                CashBankTransaction::create([
                    'cash_bank_id' => $cashBank->id,
                    'type' => $transType,
                    'amount' => $request->amount,
                    'balance_after' => $cashBank->balance,
                    'transaction_date' => $request->payment_date,
                    'reference' => $paymentNumber,
                    'description' => 'Pembayaran ' . $invoice->invoice_number,
                    'category' => $category,
                    'is_reconciled' => false,
                    'payment_id' => $payment->id,
                    'created_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Pembayaran berhasil dicatat! ' . ($invoice->status === 'Paid' ? 'Invoice sudah lunas.' : ''));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Detail pembayaran
     */
    public function show(Payment $payment)
    {
        $payment->load(['invoice.store', 'invoice.supplier', 'cashBank', 'creator']);
        return view('payments.show', compact('payment'));
    }
}
