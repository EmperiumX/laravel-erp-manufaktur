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
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            padding: 8mm 0 8mm 15mm;
        }
        .main-container {
            width: 170mm;
            margin: 0;
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
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .val-text {
            font-size: 12px;
            font-weight: bold;
            margin-top: 2px;
        }
        .divider {
            border-top: 1px solid #000;
            margin: 6px 0;
        }
        .info-table td {
            vertical-align: top;
            padding: 4px 0;
            font-size: 12px;
        }
        .items-table th {
            text-align: left;
            padding: 6px 3px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            font-size: 11px;
            font-weight: bold;
        }
        .items-table td {
            padding: 5px 3px;
            font-size: 12px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .sig-table {
            margin-top: 25px;
            width: 100%;
            text-align: center;
        }
        .sig-table td {
            width: 50%;
            vertical-align: bottom;
            height: 60px;
            font-size: 12px;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            width: 75%;
            margin: 0 auto 4px auto;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- HEADER GRID (4 COLUMNS) -->
        <table class="header-table">
            <tr>
                <td width="27%">
                    <div class="label-title">NO NOTA</div>
                    <div class="val-text">{{ $directSale->invoice_number }}</div>
                </td>
                <td width="27%">
                    <div class="label-title">TANGGAL</div>
                    <div class="val-text">{{ \Carbon\Carbon::parse($directSale->sale_date)->format('d M Y') }}</div>
                </td>
                <td width="23%">
                    <div class="label-title">PEMBAYARAN</div>
                    <div class="val-text">{{ strtoupper($directSale->payment_method ?? 'TUNAI') }}</div>
                </td>
                <td width="23%" style="text-align: right;">
                    <div style="font-size: 15px; font-weight: bold; white-space: nowrap;">NOTA</div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- ADDRESS & OUTLET (2 COLUMNS) -->
        <table class="info-table">
            <tr>
                <td width="50%" style="vertical-align: top; padding-right: 15px;">
                    <div class="label-title" style="margin-bottom: 4px;">Penjual / Outlet</div>
                    <div style="line-height: 1.3;">
                        <div><strong>NEW CITRA INDONESIA</strong></div>
                        <div>Jl. Rogojembangan Barat 1 No.31</div>
                        <div>Semarang</div>
                        <div>Telp: 081225096633</div>
                    </div>
                </td>
                <td width="50%" style="vertical-align: top; padding-left: 10px;">
                    <div class="label-title" style="margin-bottom: 4px;">Pembeli / Customer</div>
                    <div style="line-height: 1.3;">
                        <div><strong>{{ $directSale->customer_name ?? $directSale->store?->name ?? 'Pelanggan Langsung' }}</strong></div>
                        <div>{{ $directSale->customer_address ?? '-' }}</div>
                        <div>Telp: {{ $directSale->customer_phone ?? '-' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- ITEMS TABLE -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="7%">No.</th>
                    <th width="41%">Deskripsi Barang</th>
                    <th width="12%" class="text-center">QTY</th>
                    <th width="18%" class="text-right">Harga</th>
                    <th width="22%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($directSale->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ strtoupper($item->product->name ?? $item->description) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- TOTALS SUMMARY -->
        <table style="width: 100%; margin-top: 4px;">
            <tr>
                <td width="50%"></td>
                <td width="50%">
                    <table style="width: 100%;">
                        <tr>
                            <td><strong style="font-size: 12px;">Total:</strong></td>
                            <td class="text-right"><strong style="font-size: 12px;">Rp {{ number_format($directSale->total_amount, 0, ',', '.') }}</strong></td>
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
                    <div style="margin-top: 40px;">
                        <div class="sig-line"></div>
                        <div>( Nama Terang & Cap )</div>
                    </div>
                </td>
                <td>
                    <div>Kasir / Pengirim</div>
                    <div style="margin-top: 40px;">
                        <div class="sig-line"></div>
                        <div>( Nama Terang )</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>