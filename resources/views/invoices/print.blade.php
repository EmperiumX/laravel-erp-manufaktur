<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }} - {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: {!! $settings->invoice_font ?? "'Helvetica Neue', Helvetica, Arial, sans-serif" !!};
            font-size: 12px;
            color: #333;
            background: #fff;
            padding: 12mm 15mm;
        }

        /* ====== HEADER ====== */
        .header-content {
            width: 100%;
            padding: 20px 0 15px 0;
        }
        .header-table {
            width: 100%;
            table-layout: fixed;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #b91c1c;
            letter-spacing: 0.5px;
        }
        .company-info {
            font-size: 11px;
            color: #555;
            line-height: 1.6;
            margin-top: 4px;
        }
        .invoice-title {
            text-align: right;
            vertical-align: top;
        }
        .invoice-title-text {
            font-size: 32px;
            font-weight: bold;
            color: #a81a1a;
            letter-spacing: 2px;
        }
        .invoice-number-box {
            font-size: 16px;
            font-weight: bold;
            color: #a81a1a;
            letter-spacing: 0.5px;
            margin-top: 6px;
        }
        .invoice-type-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }
        .badge-purchase {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-sales {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-consignment {
            background-color: #f3e8ff;
            color: #6b21a8;
        }

        /* ====== INFO SECTION ====== */
        .info-section {
            width: 100%;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .info-left {
            vertical-align: top;
            width: 50%;
        }
        .info-right {
            vertical-align: top;
            width: 50%;
        }
        .info-label {
            color: #6b7280;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .info-detail-table {
            width: 100%;
        }
        .info-detail-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-detail-table .label {
            width: 100px;
            color: #555;
            font-weight: 600;
        }
        .info-detail-table .value {
            color: #111;
        }
        .party-box {
            border-radius: 6px;
            padding: 12px 14px;
            background-color: #fefaf0;
        }
        .party-name {
            font-size: 15px;
            font-weight: bold;
            color: #a81a1a;
            margin-bottom: 3px;
        }
        .party-detail {
            font-size: 11px;
            color: #444;
            line-height: 1.5;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-unpaid { background-color: #fef3c7; color: #92400e; }
        .status-partial { background-color: #e0e7ff; color: #3730a3; }
        .status-paid { background-color: #d1fae5; color: #065f46; }

        /* ====== TABLE ITEMS ====== */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            margin-top: 10px;
        }
        .items-table thead th {
            background-color: #fefaf0;
            color: #a81a1a;
            padding: 10px 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .items-table tbody td {
            padding: 9px 8px;
            font-size: 12px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }

        /* ====== TOTALS ====== */
        .totals-section {
            width: 100%;
            margin-top: 0;
        }
        .totals-table {
            width: 280px;
            float: right;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 8px;
            font-size: 12px;
        }
        .totals-table .label-col {
            text-align: right;
            color: #555;
            font-weight: 600;
            width: 140px;
        }
        .totals-table .value-col {
            text-align: right;
            width: 140px;
        }
        .grand-total-row {
            background-color: #fefaf0;
        }
        .grand-total-row td {
            padding: 10px 8px;
            font-size: 15px;
            font-weight: bold;
            color: #a81a1a !important;
        }
        .paid-row td {
            color: #059669;
            font-weight: bold;
        }
        .balance-row td {
            color: #dc2626;
            font-weight: bold;
            font-size: 13px;
        }

        /* ====== NOTES ====== */
        .notes-section {
            clear: both;
            margin-top: 25px;
            padding: 10px 12px;
            border-radius: 4px;
        }
        .notes-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .notes-text {
            font-size: 11px;
            color: #555;
        }

        /* ====== PAYMENT INFO ====== */
        .payment-info {
            clear: both;
            margin-top: 20px;
            padding: 12px 14px;
            background-color: #fefaf0;
            border-radius: 6px;
        }
        .payment-info-title {
            font-size: 11px;
            font-weight: bold;
            color: #a81a1a;
            margin-bottom: 4px;
        }
        .payment-info-text {
            font-size: 11px;
            color: #444;
            line-height: 1.5;
        }

        /* ====== SIGNATURE ====== */
        .signature-section {
            width: 100%;
            margin-top: 50px;
        }
        .signature-table {
            width: 100%;
            text-align: center;
        }
        .signature-table td {
            width: 50%;
            padding: 10px 30px;
            vertical-align: bottom;
            height: 90px;
        }
        .sig-title {
            font-size: 13px;
            color: #333;
            font-weight: 600;
        }
        .sig-line {
            width: 70%;
            margin: 0 auto 5px auto;
            height: 1px;
        }
        .sig-name {
            font-size: 12px;
            color: #333;
        }

        /* ====== FOOTER ====== */
        .footer-bar {
            width: 100%;
            margin-top: 30px;
            padding-top: 10px;
        }
        .footer-text {
            font-size: 12px;
            color: #555;
            text-align: center;
            line-height: 1.5;
        }
    </style>
</head>
<body>



    <!-- Top Color Bar -->


    <!-- Header -->
    <div class="header-content">
        <table class="header-table">
            <tr>
                <td width="55%" style="vertical-align: top; word-wrap: break-word; overflow-wrap: break-word;">
                    <div class="company-name">NEW CITRA INDONESIA</div>
                    <div class="company-info">
                        Jl. Rogojembangan Barat 1 No.31<br>
                        Semarang<br>
                        Telp: 081225096633, 082133326959, 085866228323
                    </div>
                </td>
                <td width="45%" class="invoice-title">
                    <div class="invoice-title-text">{{ $title }}</div>
                    <div class="invoice-number-box">{{ $invoice->invoice_number }}</div>
                    <br>
                    @if(isset($typeBadge))
                        <span class="invoice-type-badge {{ $typeBadgeClass ?? 'badge-purchase' }}">{{ $typeBadge }}</span>
                    @elseif($invoice->type === 'purchase')
                        <span class="invoice-type-badge badge-purchase">PEMBELIAN / HUTANG</span>
                    @else
                        <span class="invoice-type-badge badge-sales">PENJUALAN / PIUTANG</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Info Section -->
    <table class="info-section">
        <tr>
            <td class="info-left" style="padding-right: 20px;">
                <div class="info-label">Detail Invoice</div>
                <table class="info-detail-table">
                    <tr>
                        <td class="label">Tanggal</td>
                        <td class="value">: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jatuh Tempo</td>
                        <td class="value">: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Referensi</td>
                        <td class="value">: {{ $reference ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="value">:
                            @if($invoice->status === 'Paid')
                                <span class="status-badge status-paid">✓ LUNAS</span>
                            @elseif($invoice->status === 'Partial')
                                <span class="status-badge status-partial">SEBAGIAN</span>
                            @else
                                <span class="status-badge status-unpaid">BELUM DIBAYAR</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
            <td class="info-right">
                <div class="info-label">{{ $partyLabel }}</div>
                <div class="party-box">
                    <div class="party-name">{{ $partyName }}</div>
                    <div class="party-detail">
                        {{ $partyAddress }}<br>
                        Telp: {{ $partyPhone }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="37%" class="text-left">Deskripsi</th>
                <th width="8%" class="text-center">Qty</th>
                <th width="10%" class="text-center">Satuan</th>
                <th width="20%" class="text-right">Harga Satuan (Rp)</th>
                <th width="20%" class="text-right">Subtotal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td style="font-weight: 600;">{{ $item->description }}</td>
                <td class="text-center" style="font-weight: 600;">{{ rtrim(rtrim(number_format($item->quantity, 4, ',', '.'), '0'), ',') }}</td>
                <td class="text-center">{{ $item->unit }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight: 600;">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            {{-- Empty rows to fill space for short invoices --}}
            @for($i = count($invoice->items); $i < 3; $i++)
            <tr>
                <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals-section">
        <table class="totals-table">
            <tr>
                <td class="label-col">Subtotal</td>
                <td class="value-col">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($invoice->tax_amount > 0)
            <tr>
                <td class="label-col">PPN</td>
                <td class="value-col">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($invoice->discount_amount > 0)
            <tr>
                <td class="label-col">Diskon</td>
                <td class="value-col" style="color: #dc2626;">- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="grand-total-row">
                <td style="text-align: right; color: #fff;">TOTAL</td>
                <td style="text-align: right; color: #fff;">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
            </tr>
            @if($invoice->paid_amount > 0)
            <tr class="paid-row">
                <td class="label-col" style="color: #059669;">Dibayar</td>
                <td class="value-col" style="color: #059669;">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="balance-row">
                <td class="label-col" style="color: #dc2626;">Sisa Tagihan</td>
                <td class="value-col" style="color: #dc2626;">Rp {{ number_format($invoice->total_amount - $invoice->paid_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Notes -->
    @if($invoice->notes)
    <div class="notes-section">
        <div class="notes-label">Catatan</div>
        <div class="notes-text">{{ $invoice->notes }}</div>
    </div>
    @endif

    <!-- Payment Info Box -->
    <div class="payment-info">
        <div class="payment-info-title">INFORMASI PEMBAYARAN</div>
        <div class="payment-info-text">
            Pembayaran dapat dilakukan melalui transfer bank ke rekening perusahaan.<br>
            Mohon cantumkan nomor invoice <strong>{{ $invoice->invoice_number }}</strong> sebagai referensi pembayaran.<br>
            Jatuh tempo: <strong>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d F Y') }}</strong>
        </div>
    </div>

    <!-- Signatures -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="sig-title">Penerima</div>
                    <div style="margin-top: 55px;">
                        <div class="sig-line"></div>
                        <div class="sig-name">( Nama Terang & Cap )</div>
                    </div>
                </td>
                <td>
                    <div class="sig-title">Hormat Kami,</div>
                    <div style="margin-top: 55px;">
                        <div class="sig-line"></div>
                        <div class="sig-name">New Citra Indonesia</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer-bar">
        <div class="footer-text">
            Dokumen ini dicetak secara otomatis oleh Sistem ERP New Citra Indonesia dan sah tanpa tanda tangan basah.<br>
            © {{ date('Y') }} New Citra Indonesia — Jl. Rogojembangan Barat 1 No.31, Semarang
        </div>
    </div>




</body>
</html>
