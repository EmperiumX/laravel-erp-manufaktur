<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $consignment->shipment_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 6mm 12mm;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            font-weight: normal;
            color: #000;
            background: #fff;
            padding: 8mm 14mm;
            box-sizing: border-box;
            line-height: 1.4;
        }
        .main-container {
            width: 100%;
            margin: 0 auto;
        }
        .top-red-bar {
            border-top: 3px solid #000;
            margin-bottom: 14px;
        }

        /* PURE DIV + CSS GRID KOP SURAT */
        .kop-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 14px;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #000;
            line-height: 1.2;
        }
        .company-info {
            font-size: 13px;
            color: #000;
            font-weight: normal; /* SINGLE STRIKE CRISP */
            margin-top: 4px;
            line-height: 1.5;
        }
        .doc-title {
            font-size: 22px;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            text-align: right;
            line-height: 1.2;
        }
        .doc-number {
            font-size: 14px;
            font-weight: normal; /* SINGLE STRIKE CRISP */
            color: #000;
            margin-top: 4px;
            text-align: right;
            line-height: 1.3;
        }
        .doc-subnumber {
            font-size: 13px;
            color: #000;
            font-weight: normal; /* SINGLE STRIKE CRISP */
            margin-top: 4px;
            text-align: right;
            line-height: 1.3;
        }

        .divider {
            border-top: 1.5px solid #000;
            margin: 14px 0;
        }

        /* PURE DIV + CSS GRID INFO SECTION */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 6px 0 12px 0; /* RAISED UPWARDS SLIGHTLY */
        }
        .info-label {
            color: #000;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
            line-height: 1.3;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
            font-size: 13px;
            line-height: 1.5;
        }
        .info-row-label {
            width: 110px;
            color: #000;
            font-weight: normal;
        }
        .info-row-val {
            color: #000;
            font-weight: normal;
        }

        .dest-box {
            border: 1.5px solid #000;
            border-radius: 4px;
            padding: 8px 12px;
            background: transparent;
        }
        .dest-name {
            font-size: 13px;
            font-weight: bold;
            color: #000;
            line-height: 1.4;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .dest-detail {
            font-size: 12px;
            color: #000;
            font-weight: normal;
            line-height: 1.5;
        }
        .mitra-badge {
            display: inline-block;
            border: 1px solid #000;
            color: #000;
            font-size: 11px;
            font-weight: bold;
            padding: 1px 6px;
            border-radius: 3px;
            margin-top: 6px;
        }

        .items-intro {
            font-size: 13px;
            font-weight: normal;
            color: #000;
            margin: 16px 0 10px 0;
            line-height: 1.4;
        }

        /* PURE DIV + CSS GRID DAFTAR BARANG (GARIS HANYA PADA HEADER) */
        .items-header {
            display: grid;
            grid-template-columns: 10% 70% 20%;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 8px 0;
            color: #000;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .item-row {
            display: grid;
            grid-template-columns: 10% 70% 20%;
            border-bottom: none !important; /* TIDAK ADA GARIS PADA BARIS PRODUK */
            padding: 8px 0;
            font-size: 13px;
            font-weight: normal; /* SINGLE STRIKE CRISP */
            color: #000;
            align-items: center;
        }
        .col-no { text-align: center; }
        .col-name { text-transform: uppercase; }
        .col-qty { text-align: center; }

        .notice-box {
            clear: both;
            margin: 24px 0;
            width: 100%;
            padding: 10px 14px;
            border-left: 3px solid #000;
            background: transparent;
            font-size: 13px;
            color: #000;
            font-weight: normal; /* SINGLE STRIKE CRISP */
            line-height: 1.6;
        }

        /* PURE DIV + CSS GRID SIGNATURES */
        .sig-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 30px;
            text-align: center;
        }
        .sig-col {
            width: 100%;
        }
        .sig-title { font-size: 13px; color: #000; font-weight: bold; line-height: 1.2; }
        .sig-space { height: 55px; }
        .sig-line { border-bottom: 1.5px solid #000; width: 75%; margin: 0 auto 6px auto; }
        .sig-name { font-size: 12px; color: #000; font-weight: normal; line-height: 1.2; }

        .footer-bar {
            width: 100%;
            margin: 30px 0 0 0;
            padding-top: 10px;
            border-top: 1.5px solid #000;
        }
        .footer-text {
            font-size: 13px;
            color: #000;
            font-weight: normal; /* SINGLE STRIKE CRISP */
            text-align: center;
            line-height: 1.5;
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
            * {
                color: #000000 !important;
                border-color: #000000 !important;
                background: transparent !important;
                box-shadow: none !important;
                text-shadow: none !important;
            }
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
            <span style="font-size: 16px; font-weight: bold;">🖨️ Cetak Surat Jalan (Single-Strike Crisp Dot-Matrix)</span>
            <span style="font-size: 13px; color: #cbd5e1; margin-left: 10px;">{{ $consignment->shipment_number }}</span>
        </div>
        <div>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen</button>
            <button onclick="window.close()" class="btn-close" style="margin-left: 8px;">Tutup</button>
        </div>
    </div>

    <div class="main-container">
        <!-- TOP BLACK BAR -->
        <div class="top-red-bar"></div>

        <!-- KOP SURAT (PURE DIV + FLEX) -->
        <div class="kop-container">
            <div>
                <div class="company-name">NEW CITRA INDONESIA</div>
                <div class="company-info">
                    Jl. Rogojembangan Barat 1 No.31<br>
                    Telp: 081225096633, 082133326959, 085866228323
                </div>
            </div>
            <div>
                <div class="doc-title">SURAT JALAN</div>
                <div class="doc-number">{{ $consignment->shipment_number }}</div>
                @if($consignment->invoice)
                <div class="doc-subnumber">No. Invoice: {{ $consignment->invoice->invoice_number }}</div>
                @endif
            </div>
        </div>

        <div class="divider"></div>

        <!-- DETAIL & TUJUAN PENGIRIMAN (PURE DIV + CSS GRID) -->
        <div class="info-grid">
            <div>
                <div class="info-label">Detail Pengiriman</div>
                <div class="info-row">
                    <div class="info-row-label">Tanggal Kirim</div>
                    <div class="info-row-val">: {{ \Carbon\Carbon::parse($consignment->shipment_date)->format('d F Y') }}</div>
                </div>
            </div>
            <div>
                <div class="info-label">Tujuan Pengiriman</div>
                <div class="dest-box">
                    @php
                        $storeName = strtoupper($consignment->store?->name ?? 'TOKO DIHAPUS');
                        $rawAddress = strtoupper($consignment->store?->address ?? '');
                        if ($rawAddress && str_contains($rawAddress, $storeName)) {
                            $cleanAddress = trim(str_replace($storeName, '', $rawAddress), " -,\t\n\r\0\x0B");
                        } else {
                            $cleanAddress = $rawAddress;
                        }
                    @endphp
                    <div class="dest-name">
                        {{ $storeName }}{{ $cleanAddress ? ' - ' . $cleanAddress : '' }}
                    </div>
                    <div class="dest-detail">
                        Telp: {{ $consignment->store?->phone_number ?? '-' }}
                    </div>
                    @if($consignment->store?->type)
                    <div><span class="mitra-badge">{{ strtoupper($consignment->store->type) }}</span></div>
                    @endif
                </div>
            </div>
        </div>

        <!-- INTRO BARANG -->
        <div class="items-intro">
            Bersama dengan ini kami kirimkan sejumlah produk dengan rincian sebagai berikut:
        </div>

        <!-- DAFTAR BARANG (PURE DIV + CSS GRID - GARIS HANYA PADA HEADER) -->
        <div class="items-header">
            <div class="col-no">NO</div>
            <div class="col-name">NAMA BARANG / PRODUK</div>
            <div class="col-qty">QTY</div>
        </div>
        @foreach($consignment->items as $index => $item)
        <div class="item-row">
            <div class="col-no">{{ $index + 1 }}</div>
            <div class="col-name">{{ strtoupper($item->product->name) }}</div>
            <div class="col-qty">{{ $item->quantity }}</div>
        </div>
        @endforeach

        <!-- KETERANGAN -->
        <div class="notice-box">
            * Barang-barang di atas telah diterima dalam kondisi baik dan cukup.<br>
            * Surat jalan ini sah sebagai dokumen penagihan sesuai dengan nilai barang yang terjual.<br>
            * Barang yang tidak terjual dapat dikembalikan sesuai perjanjian yang berlaku.
        </div>

        <!-- TANDA TANGAN (PURE DIV + CSS GRID) -->
        <div class="sig-container">
            <div class="sig-col">
                <div class="sig-title">Penerima / Toko</div>
                <div class="sig-space"></div>
                <div class="sig-line"></div>
                <div class="sig-name">( Nama Terang & Cap Toko )</div>
            </div>
            <div class="sig-col">
                <div class="sig-title">Pengirim / Gudang</div>
                <div class="sig-space"></div>
                <div class="sig-line"></div>
                <div class="sig-name">( Nama Terang )</div>
            </div>
        </div>

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