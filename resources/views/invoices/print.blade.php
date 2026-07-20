<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: Letter;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            color: #000;
            background: #fff;
            padding: 6mm 5mm;
        }
        .main-container {
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
            padding-bottom: 6px;
        }
        .label-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .val-text {
            font-size: 13px;
            font-weight: bold;
            margin-top: 2px;
        }
        .divider {
            border-top: 1px solid #000;
            margin: 8px 0;
        }
        .info-table td {
            vertical-align: top;
            padding: 4px 0;
            font-size: 13px;
        }
        .items-table th {
            text-align: left;
            padding: 7px 4px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            font-size: 12px;
            font-weight: bold;
        }
        .items-table td {
            padding: 6px 4px;
            font-size: 13px;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .sig-table {
            margin-top: 30px;
            width: 100%;
            text-align: center;
        }
        .sig-table td {
            width: 50%;
            vertical-align: bottom;
            height: 65px;
            font-size: 13px;
            font-weight: bold;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            width: 70%;
            margin: 0 auto 4px auto;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- HEADER GRID (4 COLUMNS) -->
        <table class="header-table">
            <tr>
                <td width="28%">
                    <div class="label-title">NO FAKTUR</div>
                    <div class="val-text">{{ $invoice->invoice_number }}</div>
                </td>
                <td width="28%">
                    <div class="label-title">REFERENSI</div>
                    <div class="val-text">{{ $invoice->reference ?? '-' }}</div>
                </td>
                <td width="26%">
                    <div class="label-title">TANGGAL TEMPO</div>
                    <div class="val-text">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</div>
                </td>
                <td width="18%" style="text-align: right;">
                    <div style="font-size: 17px; font-weight: bold;">INVOICE</div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- ADDRESS & OUTLET (2 COLUMNS) -->
        <table class="info-table">
            <tr>
                <td width="50%">
                    <div class="label-title" style="margin-bottom: 4px;">Penjual / Outlet</div>
                    <div><strong>NEW CITRA INDONESIA</strong></div>
                    <div>Jl. Rogojembangan Barat 1 No.31, Semarang</div>
                    <div>Telp: 081225096633</div>
                </td>
                <td width="50%">
                    <div class="label-title" style="margin-bottom: 4px;">Pembeli / Customer</div>
                    <div><strong>{{ $invoice->store?->name ?? 'Pelanggan Umum' }}</strong></div>
                    <div>{{ $invoice->store?->address ?? '-' }}</div>
                    <div>Telp: {{ $invoice->store?->phone_number ?? '-' }}</div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- ITEMS TABLE -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="8%">No.</th>
                    <th width="42%">Deskripsi Barang</th>
                    <th width="12%" class="text-center">QTY</th>
                    <th width="18%" class="text-right">Harga</th>
                    <th width="20%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ strtoupper($item->product->name ?? $item->description) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- TOTALS SUMMARY -->
        <table style="width: 100%; margin-top: 4px;">
            <tr>
                <td width="60%"></td>
                <td width="40%">
                    <table style="width: 100%;">
                        <tr>
                            <td><strong style="font-size: 13px;">Total Tagihan:</strong></td>
                            <td class="text-right"><strong style="font-size: 13px;">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- SIGNATURES -->
        <table class="sig-table">
            <tr>
                <td>
                    <div>Pembeli</div>
                    <div style="margin-top: 45px;">
                        <div class="sig-line"></div>
                        <div>( Nama Terang & Cap )</div>
                    </div>
                </td>
                <td>
                    <div>Hormat Kami</div>
                    <div style="margin-top: 45px;">
                        <div class="sig-line"></div>
                        <div>( New Citra Indonesia )</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
