<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Penjualan - {{ $directSale->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            background: #fff;
        }

        .header-bar { width: 100%; background-color: #b91c1c; height: 8px; }

        .header-content {
            width: 100%;
            padding: 20px 0 15px 0;
            border-bottom: 2px solid #e5e7eb;
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
        .doc-title {
            font-size: 28px;
            font-weight: bold;
            color: #b91c1c;
            text-align: right;
            letter-spacing: 1px;
        }
        .doc-number {
            display: inline-block;
            background-color: #b91c1c;
            color: #fff;
            padding: 5px 14px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 6px;
        }

        .info-section { width: 100%; margin: 20px 0; }
        .info-label {
            color: #6b7280;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .info-table { width: 100%; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        .info-table .label { width: 100px; color: #555; font-weight: 600; }

        .buyer-box {
            border: 1.5px solid #b91c1c;
            border-radius: 6px;
            padding: 12px 14px;
            background-color: #fff5f5;
        }
        .buyer-name {
            font-size: 15px;
            font-weight: bold;
            color: #b91c1c;
            margin-bottom: 2px;
        }
        .buyer-detail { font-size: 11px; color: #444; line-height: 1.5; }

        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items-table thead th {
            background-color: #b91c1c;
            color: #fff;
            padding: 10px 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .items-table thead th:first-child { border-radius: 4px 0 0 0; }
        .items-table thead th:last-child { border-radius: 0 4px 0 0; }
        .items-table tbody td {
            padding: 9px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 12px;
        }
        .items-table tbody tr:nth-child(even) { background-color: #f9fafb; }
        .items-table tbody tr:last-child td { border-bottom: 2px solid #b91c1c; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        .totals-table {
            width: 260px;
            float: right;
            border-collapse: collapse;
        }
        .totals-table td { padding: 6px 8px; font-size: 12px; }
        .totals-table .label-col { text-align: right; color: #555; font-weight: 600; width: 120px; }
        .totals-table .value-col { text-align: right; width: 140px; }
        .grand-total-row { background-color: #b91c1c; color: #fff !important; }
        .grand-total-row td {
            padding: 10px 8px;
            font-size: 15px;
            font-weight: bold;
            border-radius: 4px;
            color: #fff;
        }

        .signature-table { width: 100%; margin-top: 50px; text-align: center; }
        .signature-table td { width: 50%; padding: 10px 30px; vertical-align: bottom; height: 90px; }
        .sig-title { font-size: 11px; color: #555; font-weight: 600; }
        .sig-line { border-bottom: 1px solid #333; width: 70%; margin: 0 auto 5px auto; }
        .sig-name { font-size: 11px; color: #333; }

        .footer-bar { width: 100%; margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        .footer-text { font-size: 9px; color: #9ca3af; text-align: center; line-height: 1.5; }
    </style>
</head>
<body>

    <div class="header-bar"></div>

    <div class="header-content">
        <table style="width:100%;">
            <tr>
                <td width="55%" style="vertical-align: top;">
                    <div class="company-name">CV. NEW CITRA INDONESIA</div>
                    <div class="company-info">
                        Jl. Kedungmundu Raya No. 161A Tembalang<br>
                        Semarang, Jawa Tengah 50273<br>
                        Telp: 085866228323
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
                            {{ $directSale->store->name }}
                        @else
                            {{ $directSale->customer_name }}
                        @endif
                    </div>
                    <div class="buyer-detail">
                        @if($directSale->store_id)
                            Kategori: {{ $directSale->store->category ?? '-' }}<br>
                            {{ $directSale->store->address ?? '-' }}<br>
                            Telp: {{ $directSale->store->phone_number ?? '-' }}
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
                <td class="font-bold">{{ $item->product->name }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
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
                <td style="text-align: right; color: #fff;">GRAND TOTAL</td>
                <td style="text-align: right; color: #fff;">Rp {{ number_format($directSale->total_amount, 0, ',', '.') }}</td>
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
                    <div class="sig-name">CV. New Citra Indonesia</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-bar">
        <div class="footer-text">
            * Nota ini adalah bukti transaksi yang sah. Barang yang sudah dibeli tidak dapat dikembalikan kecuali ada perjanjian sebelumnya.<br>
            © {{ date('Y') }} CV. New Citra Indonesia — Jl. Kedungmundu Raya No. 161A Tembalang, Semarang 50273
        </div>
    </div>

</body>
</html>