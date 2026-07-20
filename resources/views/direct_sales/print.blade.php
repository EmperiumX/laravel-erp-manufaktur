<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Penjualan - {{ $directSale->invoice_number }}</title>
    <style>
        @page {
            size: Letter;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: {!! $settings->invoice_font ?? "'Helvetica Neue', Helvetica, Arial, sans-serif" !!};
            font-size: 13px;
            color: #000;
            background: #fff;
            padding: 5mm 18mm;
        }

        .header-content {
            width: 100%;
            padding: 20px 0 15px 0;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #b91c1c;
            letter-spacing: 0.5px;
        }
        .company-info {
            font-size: 13px;
            color: #000;
            font-weight: bold;
            line-height: 1.6;
            margin-top: 4px;
        }
        .doc-title {
            font-size: 20px;
            font-weight: bold;
            color: #b91c1c;
            text-align: right;
            letter-spacing: 1px;
        }
        .doc-number {
            font-size: 16px;
            font-weight: bold;
            color: #000;
            margin-top: 6px;
        }

        .info-section { width: 100%; margin: 20px 0; }
        .info-label {
            color: #000;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .info-table { width: 100%; }
        .info-table td { padding: 3px 0; vertical-align: top; font-size: 13px; color: #000; font-weight: bold; }
        .info-table .label { width: 100px; color: #000; font-weight: bold; }

        .buyer-box {
            border: 2px solid #000;
            border-radius: 6px;
            padding: 12px 14px;
        }
        .buyer-name {
            font-size: 15px;
            font-weight: bold;
            color: #000;
            margin-bottom: 2px;
        }
        .buyer-detail { font-size: 13px; color: #000; font-weight: bold; line-height: 1.5; }

        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items-table thead th {
            background-color: #fff5f5;
            color: #000;
            padding: 10px 8px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
            border-bottom: 2px solid #000;
        }
        .items-table tbody td {
            padding: 9px 8px;
            font-size: 13px;
            color: #000;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        .totals-table {
            width: 260px;
            float: right;
            border-collapse: collapse;
        }
        .totals-table td { padding: 6px 8px; font-size: 13px; }
        .totals-table .label-col { text-align: right; color: #000; font-weight: bold; width: 120px; }
        .totals-table .value-col { text-align: right; color: #000; font-weight: bold; width: 140px; }
        .grand-total-row { 
            background-color: #fff5f5; 
        }
        .grand-total-row td {
            padding: 10px 8px;
            font-size: 15px;
            font-weight: bold;
            color: #b91c1c !important;
        }

        .signature-table { width: 100%; margin-top: 50px; text-align: center; }
        .signature-table td { width: 50%; padding: 10px 30px; vertical-align: bottom; height: 90px; }
        .sig-title { font-size: 13px; color: #000; font-weight: bold; }
        .sig-line { width: 70%; margin: 0 auto 5px auto; border-bottom: 2px solid #000; }
        .sig-name { font-size: 12px; color: #000; font-weight: bold; }

        .footer-bar { width: 100%; margin-top: 30px; padding-top: 10px; border-top: 1px solid #000; }
        .footer-text { font-size: 12px; color: #000; font-weight: bold; text-align: center; line-height: 1.5; }

        /* ====== ALIGN LEFT TO 100% WIDTH ====== */
        .header-content,
        .info-section,
        .items-table,
        .signature-table,
        .footer-bar {
            width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        .print-container {
            width: 100%;
            max-width: 630px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="print-container">





    <div class="header-content">
        <table style="width:100%; table-layout: fixed;">
            <tr>
                <td width="55%" style="vertical-align: top; word-wrap: break-word; overflow-wrap: break-word;">
                    <div class="company-name">NEW CITRA INDONESIA</div>
                    <div class="company-info">
                        Jl. Rogojembangan Barat 1 No.31<br>
                        Semarang<br>
                        Telp: 081225096633, 082133326959, 085866228323
                    </div>
                </td>
                <td width="45%" style="text-align: right; vertical-align: top;">
                    <div class="doc-title">NOTA PENJUALAN</div>
                    <div class="doc-number">{{ $directSale->invoice_number }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-section">
        <tr>
            <td width="50%" style="vertical-align: top; padding-right: 20px;">
                <div class="info-label">Detail Transaksi</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Tanggal</td>
                        <td>: {{ \Carbon\Carbon::parse($directSale->sale_date)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Catatan</td>
                        <td>: {{ $directSale->notes ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td width="50%" style="vertical-align: top;">
                <div class="info-label">Pembeli</div>
                <div class="buyer-box">
                    <div class="buyer-name">
                        @if($directSale->store_id)
                            {{ $directSale->store?->name ?? 'Toko Dihapus' }}
                        @else
                            {{ $directSale->customer_name }}
                        @endif
                    </div>
                    <div class="buyer-detail">
                        @if($directSale->store_id)
                            Kategori: {{ $directSale->store?->category ?? '-' }}<br>
                            {{ $directSale->store?->address ?? '-' }}<br>
                            Telp: {{ $directSale->store?->phone_number ?? '-' }}
                        @else
                            Pembeli Umum / Walk-in
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="40%" style="text-align: left;">Deskripsi Barang</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="20%" class="text-right">Harga (Rp)</th>
                <th width="25%" class="text-right">Subtotal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($directSale->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td style="font-weight: 600;">{{ strtoupper($item->product->name) }}</td>
                <td class="text-center" style="font-weight: 600;">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight: 600;">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            @for($i = count($directSale->items); $i < 3; $i++)
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>

    <div style="overflow: hidden;">
        <table class="totals-table">
            <tr class="grand-total-row">
                <td style="text-align: right; color: #b91c1c;">GRAND TOTAL</td>
                <td style="text-align: right; color: #b91c1c;">Rp {{ number_format($directSale->total_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <table class="signature-table">
        <tr>
            <td>
                <div class="sig-title">Pembeli</div>
                <div style="margin-top: 55px;">
                    <div class="sig-line"></div>
                    <div class="sig-name">( Nama Terang )</div>
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

    <div class="footer-bar">
        <div class="footer-text">
            * Nota ini adalah bukti transaksi yang sah. Barang yang sudah dibeli tidak dapat dikembalikan kecuali ada perjanjian sebelumnya.<br>
            © {{ date('Y') }} New Citra Indonesia — Jl. Rogojembangan Barat 1 No.31, Semarang
        </div>
    </div>




    </div>
</body>
</html>