<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $consignment->shipment_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: {!! $settings->invoice_font ?? "'Helvetica Neue', Helvetica, Arial, sans-serif" !!};
            font-size: 12px;
            color: #333;
            background: #fff;
            padding: 12mm 15mm;
        }

        .header-bar { width: 100%; background-color: #a81a1a; height: 3px; }

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
            font-size: 24px;
            font-weight: bold;
            color: #a81a1a;
            text-align: right;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .doc-number {
            font-size: 16px;
            font-weight: bold;
            color: #a81a1a;
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
        .info-table .label { width: 110px; color: #555; font-weight: 600; }

        .dest-box {
            border: 1.5px solid #a81a1a;
            border-radius: 6px;
            padding: 12px 14px;
            background-color: #fefaf0;
        }
        .dest-name {
            font-size: 15px;
            font-weight: bold;
            color: #a81a1a;
            margin-bottom: 2px;
        }
        .dest-detail { font-size: 11px; color: #444; line-height: 1.5; }
        .dest-badge {
            display: inline-block;
            background-color: #fef3c7;
            color: #9a3412;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 4px;
        }

        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items-table thead th {
            background-color: #fefaf0;
            color: #a81a1a;
            border-top: 1.5px solid #a81a1a;
            border-bottom: 1.5px solid #a81a1a;
            padding: 10px 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .items-table tbody td {
            padding: 9px 8px;
            border-bottom: 1px solid #ccc;
            font-size: 12px;
        }
        .items-table tbody tr:last-child td {
            border-bottom: 2px solid #a81a1a;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }

        .totals-table { width: 280px; float: right; border-collapse: collapse; }
        .totals-table td { padding: 6px 8px; font-size: 12px; }
        .grand-total-row { 
            background-color: #fefaf0; 
            border-top: 2px solid #a81a1a;
            border-bottom: 2px solid #a81a1a;
        }
        .grand-total-row td {
            padding: 10px 8px;
            font-size: 15px;
            font-weight: bold;
            color: #a81a1a !important;
        }

        .notice-box {
            clear: both;
            margin-top: 20px;
            padding: 10px 12px;
            background-color: #f9fafb;
            border-left: 3px solid #a81a1a;
            border-radius: 0 4px 4px 0;
            font-size: 13px;
            color: #333;
            font-style: italic;
            line-height: 1.6;
        }

        .signature-table { width: 100%; margin-top: 40px; text-align: center; border-collapse: collapse; }
        .signature-table td {
            width: 50%;
            padding: 10px 15px;
            vertical-align: bottom;
            height: 100px;
        }
        .sig-title { font-size: 13px; color: #333; font-weight: 600; }
        .sig-line { border-bottom: 1px solid #333; width: 80%; margin: 0 auto 5px auto; }
        .sig-name { font-size: 12px; color: #333; }

        .footer-bar { width: 100%; margin-top: 25px; border-top: 1px solid #ccc; padding-top: 10px; }
        .footer-text { font-size: 12px; color: #555; text-align: center; line-height: 1.5; }
    </style>
</head>
<body>



    <div class="header-bar"></div>

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
                    <div class="doc-title">Surat Jalan</div>
                    <div class="doc-number">{{ $consignment->shipment_number }}</div>
                    @if($consignment->invoice)
                        <div style="font-size: 12px; font-weight: bold; color: #333; margin-top: 6px;">
                            No. Invoice: {{ $consignment->invoice->invoice_number }}
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <table class="info-section">
        <tr>
            <td width="50%" style="vertical-align: top; padding-right: 20px;">
                <div class="info-label">Detail Pengiriman</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Tanggal Kirim</td>
                        <td>: {{ \Carbon\Carbon::parse($consignment->shipment_date)->format('d F Y') }}</td>
                    </tr>
                </table>
            </td>
            <td width="50%" style="vertical-align: top;">
                <div class="info-label">Tujuan Pengiriman</div>
                <div class="dest-box">
                    <div class="dest-name">{{ $consignment->store?->name ?? 'Toko Dihapus' }}</div>
                    <div class="dest-detail">
                        {{ $consignment->store?->address ?? 'Alamat tidak tersedia' }}<br>
                        Telp: {{ $consignment->store?->phone_number ?? '-' }}
                    </div>
                    <span class="dest-badge">{{ $consignment->store?->category ?? 'Mitra' }}</span>
                </div>
            </td>
        </tr>
    </table>

    <p style="margin-bottom: 8px; font-size: 11px; color: #555;">
        Bersama dengan ini kami kirimkan sejumlah produk dengan rincian sebagai berikut:
    </p>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="40%" class="text-left">Nama Barang / Produk</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="20%" class="text-right">Harga Jual (Rp)</th>
                <th width="25%" class="text-right">Subtotal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consignment->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td style="font-weight: 600;">{{ $item->product->name }}</td>
                <td class="text-center" style="font-size: 13px; font-weight: 600;">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight: 600;">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            @for($i = count($consignment->items); $i < 3; $i++)
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>

    <div style="overflow: hidden;">
        <table class="totals-table">
            <tr class="grand-total-row">
                <td style="text-align: right; color: #a81a1a; width: 140px;">TOTAL NILAI</td>
                <td style="text-align: right; color: #a81a1a; width: 140px;">Rp {{ number_format($consignment->total_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="notice-box">
        * Barang-barang di atas telah diterima dalam kondisi baik dan cukup.<br>
        * Surat jalan ini sah sebagai dokumen penagihan sesuai dengan nilai barang yang terjual.<br>
        * Barang yang tidak terjual dapat dikembalikan sesuai perjanjian yang berlaku.
    </div>

    <table class="signature-table">
        <tr>
            <td>
                <div class="sig-title">Penerima / Toko</div>
                <div style="margin-top: 55px;">
                    <div class="sig-line"></div>
                    <div class="sig-name">( Nama Terang & Cap Toko )</div>
                </div>
            </td>
            <td>
                <div class="sig-title">Pengirim / Gudang</div>
                <div style="margin-top: 55px;">
                    <div class="sig-line"></div>
                    <div class="sig-name">( Nama Terang )</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-bar">
        <div class="footer-text">
            Dokumen ini dicetak secara otomatis oleh Sistem ERP New Citra Indonesia dan sah tanpa tanda tangan basah.<br>
            © {{ date('Y') }} New Citra Indonesia — Jl. Rogojembangan Barat 1 No.31, Semarang
        </div>
    </div>




</body>
</html>