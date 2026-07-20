<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Penjualan - {{ $directSale->invoice_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            color: #000;
            background: #fff;
            padding: 8mm 12mm;
        }
        .main-container {
            width: 100%;
            max-width: 100%;
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
            font-size: 14px;
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
            font-size: 14px;
        }
        .items-table th {
            text-align: left;
            padding: 8px 4px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            font-size: 13px;
            font-weight: bold;
        }
        .items-table td {
            padding: 7px 4px;
            font-size: 14px;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .sig-table {
            margin-top: 35px;
            width: 100%;
            text-align: center;
        }
        .sig-table td {
            width: 50%;
            vertical-align: bottom;
            height: 70px;
            font-size: 14px;
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
                <td width="26%">
                    <div class="label-title">NO NOTA</div>
                    <div class="val-text">{{ $directSale->invoice_number }}</div>
                </td>
                <td width="26%">
                    <div class="label-title">TANGGAL</div>
                    <div class="val-text">{{ \Carbon\Carbon::parse($directSale->sale_date)->format('d M Y') }}</div>
                </td>
                <td width="22%">
                    <div class="label-title">PEMBAYARAN</div>
                    <div class="val-text">{{ strtoupper($directSale->payment_method ?? 'TUNAI') }}</div>
                </td>
                <td width="26%" style="text-align: right;">
                    <div style="font-size: 18px; font-weight: bold; white-space: nowrap;">NOTA</div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- ADDRESS & OUTLET (2 COLUMNS) -->
        <table class="info-table">
            <tr>
                <td width="48%" style="vertical-align: top; padding-right: 25px;">
                    <div class="label-title" style="margin-bottom: 8px;">Penjual / Outlet</div>
                    <div style="line-height: 1.4;">
                        <div><strong>NEW CITRA INDONESIA</strong></div>
                        <div>Jl. Rogojembangan Barat 1 No.31, Semarang</div>
                        <div>Telp: 081225096633</div>
                    </div>
                </td>
                <td width="52%" style="vertical-align: top; padding-left: 15px;">
                    <div class="label-title" style="margin-bottom: 8px;">Pembeli / Customer</div>
                    <div style="line-height: 1.4;">
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
                    <th width="8%">No.</th>
                    <th width="42%">Deskripsi Barang</th>
                    <th width="12%" class="text-center">QTY</th>
                    <th width="18%" class="text-right">Harga</th>
                    <th width="20%" class="text-right">Subtotal</th>
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
                <td width="60%"></td>
                <td width="40%">
                    <table style="width: 100%;">
                        <tr>
                            <td><strong style="font-size: 14px;">Total:</strong></td>
                            <td class="text-right"><strong style="font-size: 14px;">Rp {{ number_format($directSale->total_amount, 0, ',', '.') }}</strong></td>
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
                    <div>Kasir / Pengirim</div>
                    <div style="margin-top: 45px;">
                        <div class="sig-line"></div>
                        <div>( Nama Terang )</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>