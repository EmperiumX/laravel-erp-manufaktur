<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penjualan - {{ $directSale->invoice_number }}</title>
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

        /* PURE DIV + CSS GRID KOP SURAT — IDENTIK SURAT JALAN */
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

        .divider {
            border-top: 1.5px solid #000;
            margin: 14px 0;
        }

        /* PURE DIV + CSS GRID INFO SECTION — IDENTIK SURAT JALAN */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 14px 0 18px 0;
        }
        .info-label {
            color: #000;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 6px;
            line-height: 1.2;
        }
        .info-row {
            display: flex;
            margin-bottom: 4px;
            font-size: 13px;
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
            padding: 10px 14px;
            background: transparent;
        }
        .dest-name {
            font-size: 13px;
            font-weight: bold;
            color: #000;
            line-height: 1.3;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .dest-detail {
            font-size: 11px;
            color: #000;
            font-weight: normal; /* SINGLE STRIKE CRISP */
            line-height: 1.5;
        }

        .items-intro {
            font-size: 13px;
            font-weight: normal;
            color: #000;
            margin: 16px 0 10px 0;
            line-height: 1.4;
        }

        /* PURE DIV + CSS GRID DAFTAR BARANG — GARIS HANYA PADA HEADER */
        .items-header {
            display: grid;
            grid-template-columns: 10% 40% 14% 18% 18%;
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
            grid-template-columns: 10% 40% 14% 18% 18%;
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
        .col-price { text-align: right; }
        .col-subtotal { text-align: right; }

        /* TOTALS — RATA KANAN */
        .totals-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
            border-top: 1.5px solid #000;
            padding-top: 8px;
        }
        .totals-box {
            width: 260px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 13px;
            font-weight: normal; /* SINGLE STRIKE CRISP */
        }
        .total-row.grand-total {
            border-top: 1.5px solid #000;
            padding: 5px 0;
            font-size: 13px;
            font-weight: bold;
        }

        /* NOTICE BOX — IDENTIK SURAT JALAN */
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

        /* PURE DIV + CSS GRID SIGNATURES — IDENTIK SURAT JALAN */
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

        /* NO PRINT ACTION BAR — IDENTIK SURAT JALAN */
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

        /* PRINT MEDIA — IDENTIK SURAT JALAN */
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
            <span style="font-size: 16px; font-weight: bold;">🖨️ Cetak Nota Penjualan (Single-Strike Crisp Dot-Matrix)</span>
            <span style="font-size: 13px; color: #cbd5e1; margin-left: 10px;">{{ $directSale->invoice_number }}</span>
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
                <div class="doc-title">NOTA PENJUALAN</div>
                <div class="doc-number">{{ $directSale->invoice_number }}</div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- DETAIL & PELANGGAN (PURE DIV + CSS GRID) -->
        <div class="info-grid">
            <div>
                <div class="info-label">Detail Transaksi</div>
                <div class="info-row">
                    <div class="info-row-label">Tanggal</div>
                    <div class="info-row-val">: {{ \Carbon\Carbon::parse($directSale->sale_date)->format('d F Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-row-label">Pembayaran</div>
                    <div class="info-row-val">: {{ strtoupper($directSale->payment_method ?? 'CASH') }}</div>
                </div>
            </div>
            <div>
                <div class="info-label">Pelanggan</div>
                <div class="dest-box">
                    @php
                        $cName = strtoupper($directSale->customer_name ?? ($directSale->store?->name ?? 'PELANGGAN UMUM'));
                        $cAddr = strtoupper($directSale->store?->address ?? '-');
                        if ($cAddr && $cAddr !== '-' && str_contains($cAddr, $cName)) {
                            $cleanCAddr = trim(str_replace($cName, '', $cAddr), " -,\t\n\r\0\x0B");
                        } else {
                            $cleanCAddr = ($cAddr !== '-' ? $cAddr : '');
                        }
                    @endphp
                    <div class="dest-name">
                        {{ $cName }}{{ $cleanCAddr ? ' - ' . $cleanCAddr : '' }}
                    </div>
                    <div class="dest-detail">
                        Telp: {{ $directSale->store?->phone_number ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- INTRO BARANG -->
        <div class="items-intro">
            Bersama dengan ini kami sampaikan nota penjualan dengan rincian sebagai berikut:
        </div>

        <!-- DAFTAR BARANG (PURE DIV + CSS GRID — GARIS HANYA PADA HEADER) -->
        <div class="items-header">
            <div class="col-no">NO</div>
            <div class="col-name">NAMA BARANG</div>
            <div class="col-qty">QTY</div>
            <div class="col-price">HARGA</div>
            <div class="col-subtotal">SUBTOTAL</div>
        </div>
        @foreach($directSale->items as $index => $item)
        <div class="item-row">
            <div class="col-no">{{ $index + 1 }}</div>
            <div class="col-name">{{ strtoupper($item->product->name ?? 'Produk') }}</div>
            <div class="col-qty">{{ (int) $item->quantity }}</div>
            <div class="col-price">{{ number_format($item->unit_price, 0, ',', '.') }}</div>
            <div class="col-subtotal">{{ number_format($item->subtotal, 0, ',', '.') }}</div>
        </div>
        @endforeach

        <!-- TOTALS -->
        <div class="totals-container">
            <div class="totals-box">
                <div class="total-row grand-total">
                    <div>TOTAL BELANJA</div>
                    <div>Rp {{ number_format($directSale->total_amount, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- KETERANGAN -->
        <div class="notice-box">
            * Nota ini sah sebagai bukti transaksi penjualan langsung dari New Citra Indonesia.
            @if($directSale->notes)<br>* Catatan: {{ $directSale->notes }}@endif
        </div>

        <!-- TANDA TANGAN (PURE DIV + CSS GRID) -->
        <div class="sig-container">
            <div class="sig-col">
                <div class="sig-title">Kasir / Toko</div>
                <div class="sig-space"></div>
                <div class="sig-line"></div>
                <div class="sig-name">( New Citra Indonesia )</div>
            </div>
            <div class="sig-col">
                <div class="sig-title">Pembeli</div>
                <div class="sig-space"></div>
                <div class="sig-line"></div>
                <div class="sig-name">( {{ $cName }} )</div>
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