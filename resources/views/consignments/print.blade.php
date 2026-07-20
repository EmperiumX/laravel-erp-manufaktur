<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $consignment->shipment_number }}</title>
    <style>
        @page {
            size: Letter;
            margin: 6mm 12mm;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            font-weight: bold;
            color: #000;
            background: #fff;
            padding: 4mm 10mm;
            box-sizing: border-box;
            line-height: 1.3;
        }
        .main-container {
            width: 100%;
            margin: 0 auto;
        }
        .top-red-bar {
            border-top: 3px solid #b91c1c;
            margin-bottom: 12px;
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
            line-height: 1.2;
        }
        .company-info {
            font-size: 13px;
            color: #000;
            font-weight: bold;
            margin-top: 4px;
            line-height: 1.3;
        }
        .doc-title {
            font-size: 22px;
            font-weight: bold;
            color: #b91c1c;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .doc-number {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            margin-top: 4px;
            line-height: 1.2;
        }
        .doc-subnumber {
            font-size: 12px;
            color: #000;
            font-weight: bold;
            margin-top: 3px;
            line-height: 1.2;
        }
        .divider {
            border-top: 1.5px solid #000;
            margin: 12px 0;
        }

        .info-label {
            color: #b91c1c;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 6px;
            line-height: 1.2;
        }
        .dest-box {
            border: 1.5px solid #b91c1c;
            border-radius: 6px;
            padding: 8px 12px;
            background: transparent;
        }
        .dest-name {
            font-size: 15px;
            font-weight: bold;
            color: #b91c1c;
            line-height: 1.2;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .dest-detail {
            font-size: 13px;
            color: #000;
            font-weight: bold;
            line-height: 1.3;
        }
        .mitra-badge {
            display: inline-block;
            background-color: #fef08a;
            color: #854d0e;
            font-size: 11px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            margin-top: 4px;
        }

        .items-intro {
            font-size: 13px;
            font-weight: bold;
            color: #000;
            margin: 14px 0 10px 0;
            line-height: 1.3;
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
            padding: 8px 6px;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
            text-align: left;
            border-top: 2px solid #b91c1c;
            border-bottom: 2px solid #b91c1c;
            line-height: 1.2;
        }
        .items-table tbody td {
            padding: 8px 6px;
            font-size: 13px;
            font-weight: bold;
            color: #000;
            vertical-align: middle;
            border-bottom: 1px solid #000;
            line-height: 1.3;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .notice-box {
            clear: both;
            margin: 20px 0;
            width: 100%;
            padding: 8px 12px;
            border-left: 3px solid #b91c1c;
            background: transparent;
            font-size: 12px;
            color: #000;
            font-weight: bold;
            line-height: 1.5;
        }

        .signature-table {
            width: 100%;
            margin: 25px 0 0 0;
            text-align: center;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            padding: 5px 10px;
            vertical-align: bottom;
            height: 75px;
        }
        .sig-title { font-size: 13px; color: #000; font-weight: bold; line-height: 1.2; }
        .sig-name { font-size: 12px; color: #000; font-weight: bold; line-height: 1.2; }
        .sig-line { border-bottom: 1.5px solid #000; width: 70%; margin: 0 auto 4px auto; }

        .footer-bar {
            width: 100%;
            margin: 20px 0 0 0;
            padding-top: 8px;
            clear: both;
            border-top: 1px solid #000;
        }
        .footer-text {
            font-size: 11px;
            color: #000;
            font-weight: bold;
            text-align: center;
            line-height: 1.4;
        }

        /* NO PRINT ACTION BAR */
        .no-print-bar {
            background: #1e293b;
            color: #fff;
            padding: 12px 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-print {
            background: #b91c1c;
            color: #fff;
            border: none;
            padding: 10px 24px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-close {
            background: #475569;
            color: #fff;
            border: none;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }

        @media print {
            .no-print { display: none !important; }
            body {
                padding: 0 !important;
                margin: 0 !important;
                background: #fff !important;
            }
            .main-container {
                width: 100% !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>
    <!-- NO PRINT ACTION BAR -->
    <div class="no-print-bar no-print">
        <div>
            <span style="font-size: 16px; font-weight: bold;">🖨️ Cetak Surat Jalan (Langsung HTML)</span>
            <span style="font-size: 13px; color: #cbd5e1; margin-left: 10px;">{{ $consignment->shipment_number }}</span>
        </div>
        <div>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen</button>
            <button onclick="window.close()" class="btn-close" style="margin-left: 8px;">Tutup</button>
        </div>
    </div>

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
                <td width="50%" style="vertical-align: top; padding-right: 15px;">
                    <div class="info-label">Detail Pengiriman</div>
                    <table class="info-table">
                        <tr>
                            <td width="35%">Tanggal Kirim</td>
                            <td>: {{ \Carbon\Carbon::parse($consignment->shipment_date)->format('d F Y') }}</td>
                        </tr>
                    </table>
                </td>
                <td width="50%" style="vertical-align: top;">
                    <div class="info-label">Tujuan Pengiriman</div>
                    <div class="dest-box">
                        <div class="dest-name">
                            {{ strtoupper($consignment->store?->name ?? 'Toko Dihapus') }}
                            @if($consignment->store?->address)
                             - {{ strtoupper($consignment->store->address) }}
                            @endif
                        </div>
                        <div class="dest-detail">
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

        <!-- DAFTAR BARANG (TANPA HARGA & SUBTOTAL KHUSUS SURAT JALAN) -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="10%" class="text-center">NO</th>
                    <th width="70%">NAMA BARANG / PRODUK</th>
                    <th width="20%" class="text-center">QTY</th>
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

        <!-- KETERANGAN -->
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
                    <div style="margin-top: 45px;">
                        <div class="sig-line"></div>
                        <div class="sig-name">( Nama Terang & Cap Toko )</div>
                    </div>
                </td>
                <td>
                    <div class="sig-title">Pengirim / Gudang</div>
                    <div style="margin-top: 45px;">
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

    <!-- AUTOMATIC PRINT TRIGGER -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>
</body>
</html>