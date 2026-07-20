<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ $invoice->invoice_number }}</title>
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
        .top-black-bar {
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
            font-weight: normal;
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
            font-weight: normal;
            color: #000;
            margin-top: 4px;
            text-align: right;
            line-height: 1.3;
        }
        .doc-subnumber {
            font-size: 13px;
            color: #000;
            font-weight: normal;
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
            margin: 2px 0 8px 0; /* NAIK LEBIH TINGGI */
        }
        .info-label {
            color: #000;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 3px;
            line-height: 1.2;
        }
        .info-row {
            display: flex;
            margin-bottom: 3px;
            font-size: 13px;
            line-height: 1.3;
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

        .party-box {
            border: 1.5px solid #000;
            border-radius: 4px;
            padding: 6px 12px;
            background: transparent;
        }
        .party-name {
            font-size: 13px;
            font-weight: bold;
            color: #000;
            line-height: 1.3;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .party-detail {
            font-size: 12px;
            color: #000;
            font-weight: normal;
            line-height: 1.4;
        }

        /* PURE DIV + CSS GRID DAFTAR BARANG */
        .items-header {
            display: grid;
            grid-template-columns: 8% 44% 12% 18% 18%;
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
            grid-template-columns: 8% 44% 12% 18% 18%;
            border-bottom: 1px solid #ddd;
            padding: 8px 0;
            font-size: 13px;
            font-weight: normal;
            color: #000;
            align-items: center;
        }
        .col-no { text-align: center; }
        .col-name { text-transform: uppercase; }
        .col-qty { text-align: center; }
        .col-price { text-align: right; }
        .col-subtotal { text-align: right; }

        /* TOTALS GRID (OPTIMIZED FOR LX-310 CRISP PRINTING) */
        .totals-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }
        .totals-box {
            width: 280px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 13px;
            font-weight: normal;
        }
        .total-row.grand-total {
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            padding: 5px 0;
            font-size: 13px;
            font-weight: normal; /* CRISP SINGLE STRIKE */
        }

        .notice-box {
            clear: both;
            margin: 24px 0;
            width: 100%;
            padding: 10px 14px;
            border-left: 3px solid #000;
            background: transparent;
            font-size: 13px;
            color: #000;
            font-weight: normal;
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
            font-weight: normal;
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
            <span style="font-size: 16px; font-weight: bold;">🖨️ Cetak {{ $title }} (Direct HTML Print)</span>
            <span style="font-size: 13px; color: #cbd5e1; margin-left: 10px;">{{ $invoice->invoice_number }}</span>
        </div>
        <div>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen</button>
            <button onclick="window.close()" class="btn-close" style="margin-left: 8px;">Tutup</button>
        </div>
    </div>

    <div class="main-container">
        <!-- TOP BLACK BAR -->
        <div class="top-black-bar"></div>

        <!-- KOP SURAT -->
        <div class="kop-container">
            <div>
                <div class="company-name">NEW CITRA INDONESIA</div>
                <div class="company-info">
                    Jl. Rogojembangan Barat 1 No.31<br>
                    Telp: 081225096633, 082133326959, 085866228323
                </div>
            </div>
            <div>
                <div class="doc-title">{{ $title }}</div>
                <div class="doc-number">{{ $invoice->invoice_number }}</div>
                @if($reference && $reference !== '-')
                <div class="doc-subnumber">{{ $reference }}</div>
                @endif
            </div>
        </div>

        <div class="divider"></div>

        <!-- DETAIL & TUJUAN -->
        <div class="info-grid">
            <div>
                <div class="info-label">Detail Invoice</div>
                <div class="info-row">
                    <div class="info-row-label">Tanggal</div>
                    <div class="info-row-val">: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d F Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-row-label">Jatuh Tempo</div>
                    <div class="info-row-val">: {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d F Y') : '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-row-label">Status</div>
                    <div class="info-row-val">: {{ strtoupper($invoice->payment_status ?? 'UNPAID') }}</div>
                </div>
            </div>
            <div>
                <div class="info-label">{{ $partyLabel }}</div>
                <div class="party-box">
                    @php
                        $pName = strtoupper($partyName);
                        $pAddr = strtoupper($partyAddress);
                        if ($pAddr && str_contains($pAddr, $pName)) {
                            $cleanAddr = trim(str_replace($pName, '', $pAddr), " -,\t\n\r\0\x0B");
                        } else {
                            $cleanAddr = $pAddr;
                        }
                    @endphp
                    <div class="party-name">
                        {{ $pName }}{{ $cleanAddr ? ' - ' . $cleanAddr : '' }}
                    </div>
                    <div class="party-detail">
                        Telp: {{ $partyPhone }}
                    </div>
                </div>
            </div>
        </div>

        <!-- DAFTAR BARANG -->
        <div class="items-header">
            <div class="col-no">NO</div>
            <div class="col-name">NAMA BARANG / DESKRIPSI</div>
            <div class="col-qty">QTY</div>
            <div class="col-price">HARGA (RP)</div>
            <div class="col-subtotal">SUBTOTAL (RP)</div>
        </div>
        @foreach($invoice->items as $index => $item)
        <div class="item-row">
            <div class="col-no">{{ $index + 1 }}</div>
            <div class="col-name">{{ strtoupper($item->product->name ?? ($item->material->name ?? $item->description)) }}</div>
            <div class="col-qty">{{ $item->quantity }}</div>
            <div class="col-price">{{ number_format($item->unit_price, 0, ',', '.') }}</div>
            <div class="col-subtotal">{{ number_format($item->subtotal, 0, ',', '.') }}</div>
        </div>
        @endforeach

        <!-- TOTALS -->
        <div class="totals-container">
            <div class="totals-box">
                <div class="total-row">
                    <div>Subtotal</div>
                    <div>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</div>
                </div>
                @if($invoice->tax_amount > 0)
                <div class="total-row">
                    <div>PPN / Pajak</div>
                    <div>Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</div>
                </div>
                @endif
                @if($invoice->discount_amount > 0)
                <div class="total-row">
                    <div>Diskon</div>
                    <div>- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</div>
                </div>
                @endif
                <div class="total-row grand-total">
                    <div>GRAND TOTAL</div>
                    <div>Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</div>
                </div>
                @if($invoice->paid_amount > 0)
                <div class="total-row">
                    <div>Sudah Dibayar</div>
                    <div>Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</div>
                </div>
                <div class="total-row">
                    <div>Sisa Tagihan</div>
                    <div>Rp {{ number_format($invoice->total_amount - $invoice->paid_amount, 0, ',', '.') }}</div>
                </div>
                @endif
            </div>
        </div>

        @if($invoice->notes)
        <div class="notice-box">
            * Catatan: {{ $invoice->notes }}
        </div>
        @endif

        <!-- TANDA TANGAN -->
        <div class="sig-container">
            <div class="sig-col">
                <div class="sig-title">Hormat Kami</div>
                <div class="sig-space"></div>
                <div class="sig-line"></div>
                <div class="sig-name">( New Citra Indonesia )</div>
            </div>
            <div class="sig-col">
                <div class="sig-title">Penerima</div>
                <div class="sig-space"></div>
                <div class="sig-line"></div>
                <div class="sig-name">( {{ $partyName }} )</div>
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
