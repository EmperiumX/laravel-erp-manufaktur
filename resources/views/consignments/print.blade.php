<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $consignment->shipment_number }}</title>
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

        /* NARROW TABLES FOR STEP 1 */
        .narrow-table {
            width: 100%;
            margin: 0;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #b91c1c;
            letter-spacing: 0.5px;
        }
        .company-info {
            font-size: 14px;
            color: #000;
            font-weight: bold;
            line-height: 1.5;
            margin-top: 4px;
        }
        .doc-title {
            font-size: 26px;
            font-weight: bold;
            color: #a81a1a;
            letter-spacing: 1px;
        }
        .doc-number {
            font-size: 15px;
            font-weight: bold;
            color: #000;
            margin-top: 4px;
            text-align: right;
        }

        .info-section-table {
            width: 100%;
            margin: 20px 0 25px 0;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .info-label {
            color: #000;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 13px;
            color: #000;
            font-weight: bold;
        }
        .info-table .label {
            font-weight: bold;
            color: #000;
        }
        .dest-box {
            border: 2px solid #000;
            border-radius: 6px;
            padding: 10px 12px;
        }
        .dest-name {
            font-size: 15px;
            font-weight: bold;
            color: #000;
            margin-bottom: 2px;
        }
        .dest-detail {
            font-size: 13px;
            color: #000;
            font-weight: bold;
            line-height: 1.4;
        }

        /* TABLE ITEMS FOR STEP 2 (LEBAR 75%) */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .items-table thead th {
            background-color: #fefaf0;
            color: #000;
            padding: 8px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
            text-align: left;
            border-bottom: 2px solid #000;
        }
        .items-table tbody td {
            padding: 8px;
            font-size: 13px;
            font-weight: bold;
            color: #000;
            vertical-align: top;
            border-bottom: 1px solid #ddd;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        /* SIGNATURE TABLE FOR STEP 2 (LEBAR 75%) */
        .signature-table {
            width: 100%;
            margin: 30px 0 0 0;
            text-align: center;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            padding: 10px 15px;
            vertical-align: bottom;
            height: 90px;
        }
        .sig-title { font-size: 13px; color: #000; font-weight: bold; }
        .sig-name { font-size: 12px; color: #000; font-weight: bold; }
        .sig-line { border-bottom: 2px solid #000; width: 80%; margin: 0 auto 5px auto; }

        /* NOTICE BOX & FOOTER */
        .notice-box {
            clear: both;
            margin: 20px 0;
            width: 100%;
            padding: 10px 12px;
            border-left: 4px solid #b91c1c;
            border-radius: 0 4px 4px 0;
            font-size: 12px;
            color: #000;
            font-weight: bold;
            font-style: italic;
            line-height: 1.6;
        }
        .footer-bar {
            width: 100%;
            margin: 25px 0 0 0;
            padding-top: 10px;
            clear: both;
            border-top: 1px solid #000;
        }
        .footer-text {
            font-size: 12px;
            color: #000;
            font-weight: bold;
            text-align: center;
            line-height: 1.5;
        .print-container {
            width: 100%;
            max-width: 630px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="print-container">

    <!-- KOP SURAT (TABEL LEBAR 75%) -->
    <table class="narrow-table" style="margin-bottom: 20px;">
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
            </td>
        </tr>
    </table>

    <!-- DETAIL & TUJUAN PENGIRIMAN (TABEL LEBAR 75%) -->
    <table class="info-section-table">
        <tr>
            <td width="50%" style="vertical-align: top; padding-right: 15px;">
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
                </div>
            </td>
        </tr>
    </table>

    <!-- DAFTAR BARANG (TABEL LEBAR 100%) -->
    <div style="width: 100%; margin: 10px 0 25px 0;">
        <div class="info-label">Daftar Barang</div>
        <div style="font-size: 13px; color: #000; font-weight: bold; margin-bottom: 8px;">
            Bersama dengan ini kami kirimkan sejumlah produk dengan rincian sebagai berikut:
        </div>
        <table class="items-table">
            <thead>
                <tr>
                    <th width="10%" class="text-center">No</th>
                    <th width="70%">Nama Barang / Produk</th>
                    <th width="20%" class="text-center">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consignment->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ strtoupper($item->product->name) }}</td>
                    <td class="text-center font-bold">{{ $item->quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- KETERANGAN / PENJELASAN (TABEL LEBAR 80%) -->
    <div class="notice-box">
        * Barang-barang di atas telah diterima dalam kondisi baik dan cukup.<br>
        * Surat jalan ini sah sebagai dokumen penagihan sesuai dengan nilai barang yang terjual.<br>
        * Barang yang tidak terjual dapat dikembalikan sesuai perjanjian yang berlaku.
    </div>

    <!-- TANDA TANGAN (TABEL LEBAR 75%) -->
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

    <!-- FOOTER (TABEL LEBAR 80%) -->
    <div class="footer-bar">
        <div class="footer-text">
            Dokumen ini dicetak secara otomatis oleh Sistem ERP New Citra Indonesia dan sah tanpa tanda tangan basah.<br>
            © {{ date('Y') }} New Citra Indonesia — Jl. Rogojembangan Barat 1 No.31, Semarang
        </div>
    </div>

    </div>
</body>
</html>