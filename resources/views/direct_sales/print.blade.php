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
            font-size: 13px;
            color: #000;
            background: #fff;
            padding: 6mm 0 6mm 10mm;
        }
        .main-container {
            width: 152mm;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .divider {
            border-top: 2px solid #000;
            margin: 6px 0;
        }
        .info-table td {
            vertical-align: top;
            padding: 3px 0;
            font-size: 13px;
        }
        .items-table th {
            text-align: left;
            padding: 6px 3px;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            font-size: 12px;
            font-weight: bold;
        }
        .items-table td {
            padding: 5px 3px;
            font-size: 13px;
            font-weight: bold;
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
            font-size: 13px;
            font-weight: bold;
        }
        .sig-line {
            border-bottom: 2px solid #000;
            width: 75%;
            margin: 0 auto 4px auto;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- HEADER (2 COLUMNS) -->
        <table>
            <tr>
                <td width="56%" style="vertical-align: top;">
                    <div style="font-size: 18px; font-weight: bold;">NEW CITRA INDONESIA</div>
                    <div style="font-size: 12px; font-weight: bold; margin-top: 3px; line-height: 1.3;">
                        Jl. Rogojembangan Barat 1 No.31, Semarang<br>
                        Telp: 081225096633, 082133326959
                    </div>
                </td>
                <td width="44%" style="vertical-align: top; text-align: right;">
                    <div style="font-size: 20px; font-weight: bold; white-space: nowrap;">NOTA PENJUALAN</div>
                    <div style="font-size: 13px; font-weight: bold; margin-top: 4px; white-space: nowrap;">No: {{ $directSale->invoice_number }}</div>
                    <div style="font-size: 12px; font-weight: bold; margin-top: 2px; white-space: nowrap;">Tgl: {{ \Carbon\Carbon::parse($directSale->sale_date)->format('d M Y') }}</div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- ADDRESS & OUTLET (2 COLUMNS) -->
        <table class="info-table">
            <tr>
                <td width="48%" style="vertical-align: top; padding-right: 10px;">
                    <div style="font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 4px;">Pembayaran</div>
                    <div style="font-size: 13px; font-weight: bold; margin-bottom: 8px;">{{ strtoupper($directSale->payment_method ?? 'TUNAI') }}</div>

                    <div style="font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 2px;">Penjual / Outlet</div>
                    <div style="line-height: 1.3; font-size: 12px;">
                        <div><strong>NEW CITRA INDONESIA</strong></div>
                        <div>Semarang</div>
                    </div>
                </td>
                <td width="52%" style="vertical-align: top; padding-left: 8px;">
                    <div style="font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 4px;">Pembeli / Customer</div>
                    <div style="line-height: 1.3; font-size: 13px;">
                        <div><strong>{{ $directSale->customer_name ?? $directSale->store?->name ?? 'Pelanggan Langsung' }}</strong></div>
                        <div>{{ $directSale->customer_address ?? '-' }}</div>
                        <div>Telp: {{ $directSale->customer_phone ?? '-' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- ITEMS TABLE WITH INTEGRATED TOTALS -->
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
                @php $totalQty = 0; @endphp
                @foreach($directSale->items as $index => $item)
                @php $totalQty += $item->quantity; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ strtoupper($item->product->name ?? $item->description) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align: right; font-weight: bold; padding: 6px 4px; border-top: 1px solid #000; border-bottom: 1px solid #000;">Total Jumlah</td>
                    <td style="text-align: center; font-weight: bold; padding: 6px 4px; border-top: 1px solid #000; border-bottom: 1px solid #000;">{{ $totalQty }}</td>
                    <td style="text-align: right; font-weight: bold; padding: 6px 4px; border-top: 1px solid #000; border-bottom: 1px solid #000;">Total:</td>
                    <td style="text-align: right; font-weight: bold; padding: 6px 4px; border-top: 1px solid #000; border-bottom: 1px solid #000;">Rp {{ number_format($directSale->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- SIGNATURES -->
        <table class="sig-table">
            <tr>
                <td>
                    <div>Pembeli</div>
                    <div style="margin-top: 35px;">
                        <div class="sig-line"></div>
                        <div>( Nama Terang & Cap )</div>
                    </div>
                </td>
                <td>
                    <div>Kasir / Pengirim</div>
                    <div style="margin-top: 35px;">
                        <div class="sig-line"></div>
                        <div>( Nama Terang )</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>