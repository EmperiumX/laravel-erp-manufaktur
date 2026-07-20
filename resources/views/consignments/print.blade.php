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
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #1e293b;
            background: #fff;
            padding: 8mm 15mm;
            box-sizing: border-box;
            line-height: 1.4;
        }
        .main-container {
            width: 100%;
            margin: 0;
        }
        .top-red-bar {
            border-top: 3px solid #b91c1c;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #b91c1c;
            letter-spacing: 0.5px;
        }
        .company-info {
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
            line-height: 1.4;
        }
        .doc-title {
            font-size: 24px;
            font-weight: bold;
            color: #b91c1c;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .doc-number {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            margin-top: 4px;
            text-align: right;
        }
        .doc-subnumber {
            font-size: 12px;
            color: #64748b;
            font-weight: bold;
            margin-top: 2px;
            text-align: right;
        }
        .divider {
            border-top: 1px solid #e2e8f0;
            margin: 15px 0;
        }

        .info-section-table {
            width: 100%;
            margin: 15px 0 20px 0;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .info-label {
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 13px;
            color: #000;
            font-weight: bold;
        }
        .info-table .label {
            color: #000;
            font-weight: bold;
        }
        .dest-box {
            border: 1.5px solid #b91c1c;
            border-radius: 6px;
            padding: 10px 14px;
            background: transparent;
        }
        .dest-name {
            font-size: 15px;
            font-weight: bold;
            color: #b91c1c;
            margin-bottom: 3px;
        }
        .dest-detail {
            font-size: 13px;
            color: #475569;
            line-height: 1.4;
        }
        .mitra-badge {
            display: inline-block;
            background-color: #fef08a;
            color: #854d0e;
            font-size: 11px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            margin-top: 6px;
        }

        .items-intro {
            font-size: 13px;
            color: #334155;
            margin: 15px 0 10px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 5px;
        }
        .items-table thead th {
            background: transparent;
            color: #b91c1c;
            padding: 10px 8px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
            text-align: left;
            border-top: 2px solid #b91c1c;
            border-bottom: 2px solid #b91c1c;
        }
        .items-table tbody td {
            padding: 10px 8px;
            font-size: 13px;
            font-weight: bold;
            color: #000;
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .notice-box {
            clear: both;
            margin: 25px 0;
            width: 100%;
            padding: 8px 14px;
            border-left: 3px solid #b91c1c;
            background: transparent;
            font-size: 12px;
            color: #334155;
            font-style: italic;
            line-height: 1.6;
        }

        .signature-table {
            width: 100%;
            margin: 35px 0 0 0;
            text-align: center;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            padding: 10px 15px;
            vertical-align: bottom;
            height: 85px;
        }
        .sig-title { font-size: 13px; color: #000; font-weight: bold; }
        .sig-name { font-size: 12px; color: #475569; font-weight: bold; }
        .sig-line { border-bottom: 1px solid #475569; width: 70%; margin: 0 auto 6px auto; }

        .footer-bar {
            width: 100%;
            margin: 30px 0 0 0;
            padding-top: 12px;
            clear: both;
            border-top: 1px solid #e2e8f0;
        }
        .footer-text {
            font-size: 11px;
            color: #64748b;
            text-align: center;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- TOP RED BAR -->
        <div class="top-red-bar"></div>

        <!-- KOP SURAT -->
        <table>
            <tr>
                <td width="55%" style="vertical-align: top;">
                    <div class="company-name">NEW CITRA INDONESIA</div>
                    <div class="company-info">
                        Jl. Rogojembangan Barat 1 No.31<br>
                        Telp: 081225096633, 082133326959, 085866228323
                    </div>
                </td>
                <td width="45%" style="text-align: right; vertical-align: top;">
                    <div class="doc-title">SURAT JALAN</div>
                    <div class="doc-number">{{ $consignment->shipment_number }}</div>
                    @if($consignment->invoice)
                    <div class="doc-subnumber">No. Invoice: {{ $consignment->invoice->invoice_number }}</div>
                    @endif
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- DETAIL & TUJUAN PENGIRIMAN -->
        <table class="info-section-table">
            <tr>
                <td width="50%" style="vertical-align: top; padding-right: 20px;">
                    <div class="info-label">Detail Pengiriman</div>
                    <table class="info-table">
                        <tr>
                            <td width="35%" class="label">Tanggal Kirim</td>
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
                        @if($consignment->store?->type)
                        <div><span class="mitra-badge">{{ strtoupper($consignment->store->type) }}</span></div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- INTRO BARANG -->
        <div class="items-intro">
            Bersama dengan ini kami kirimkan sejumlah produk dengan rincian sebagai berikut:
        </div>

        <!-- DAFTAR BARANG -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="8%" class="text-center">NO</th>
                    <th width="44%">NAMA BARANG / PRODUK</th>
                    <th width="12%" class="text-center">QTY</th>
                    <th width="18%" class="text-right">HARGA JUAL (RP)</th>
                    <th width="18%" class="text-right">SUBTOTAL (RP)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consignment->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ strtoupper($item->product->name) }}</td>
                    <td class="text-center font-bold">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- KETERANGAN (TANPA BACKGROUND) -->
        <div class="notice-box">
            * Barang-barang di atas telah diterima dalam kondisi baik dan cukup.<br>
            * Surat jalan ini sah sebagai dokumen penagihan sesuai dengan nilai barang yang terjual.<br>
            * Barang yang tidak terjual dapat dikembalikan sesuai perjanjian yang berlaku.
        </div>

        <!-- TANDA TANGAN -->
        <table class="signature-table">
            <tr>
                <td>
                    <div class="sig-title">Penerima / Toko</div>
                    <div style="margin-top: 50px;">
                        <div class="sig-line"></div>
                        <div class="sig-name">( Nama Terang & Cap Toko )</div>
                    </div>
                </td>
                <td>
                    <div class="sig-title">Pengirim / Gudang</div>
                    <div style="margin-top: 50px;">
                        <div class="sig-line"></div>
                        <div class="sig-name">( Nama Terang )</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- FOOTER -->
        <div class="footer-bar">
            <div class="footer-text">
                Dokumen ini dicetak secara otomatis oleh Sistem ERP New Citra Indonesia dan sah tanpa tanda tangan basah.<br>
                © {{ date('Y') }} New Citra Indonesia — Jl. Rogojembangan Barat 1 No.31, Semarang
            </div>
        </div>
    </div>
</body>
</html>