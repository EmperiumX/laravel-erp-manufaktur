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
            font-size: 14px;
            font-weight: bold;
            color: #000;
            background: #fff;
            padding: 6mm 0;
            line-height: 1.5;
            letter-spacing: 0.5px;
        }
        .main-container {
            width: 185mm;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .divider {
            border-top: 2px solid #000;
            margin: 10px 0;
        }
        .info-table td {
            vertical-align: top;
            padding: 5px 0;
            font-size: 14px;
            line-height: 1.5;
        }
        .items-table th {
            text-align: left;
            padding: 8px 4px;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .items-table td {
            padding: 7px 4px;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.5px;
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
            border-bottom: 2px solid #000;
            width: 75%;
            margin: 0 auto 6px auto;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- HEADER (2 COLUMNS) -->
        <table>
            <tr>
                <td width="58%" style="vertical-align: top;">
                    <div style="font-size: 20px; font-weight: bold; letter-spacing: 0.8px;">NEW CITRA INDONESIA</div>
                    <div style="font-size: 13px; font-weight: bold; margin-top: 4px; line-height: 1.4;">
                        Jl. Rogojembangan Barat 1 No.31, Semarang<br>
                        Telp: 081225096633, 082133326959
                    </div>
                </td>
                <td width="42%" style="vertical-align: top; text-align: right;">
                    <div style="font-size: 22px; font-weight: bold; white-space: nowrap; letter-spacing: 1px;">INVOICE</div>
                    <div style="font-size: 14px; font-weight: bold; margin-top: 5px; white-space: nowrap;">No: {{ $invoice->invoice_number }}</div>
                    <div style="font-size: 13px; font-weight: bold; margin-top: 3px; white-space: nowrap;">Tempo: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- ADDRESS & OUTLET (2 COLUMNS) -->
        <table class="info-table">
            <tr>
                <td width="48%" style="vertical-align: top; padding-right: 15px;">
                    <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; letter-spacing: 0.5px;">Referensi / DO</div>
                    <div style="font-size: 14px; font-weight: bold; margin-bottom: 12px;">{{ $invoice->reference ?? '-' }}</div>

                    <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 3px; letter-spacing: 0.5px;">Penjual / Outlet</div>
                    <div style="line-height: 1.4; font-size: 13px;">
                        <div><strong>NEW CITRA INDONESIA</strong></div>
                        <div>Semarang</div>
                    </div>
                </td>
                <td width="52%" style="vertical-align: top; padding-left: 10px;">
                    <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; letter-spacing: 0.5px;">Pembeli / Customer</div>
                    <div style="line-height: 1.4; font-size: 14px;">
                        <div><strong>{{ $invoice->store?->name ?? 'Pelanggan Umum' }}</strong></div>
                        <div>{{ $invoice->store?->address ?? '-' }}</div>
                        <div>Telp: {{ $invoice->store?->phone_number ?? '-' }}</div>
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
                @foreach($invoice->items as $index => $item)
                @php $totalQty += $item->quantity; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ strtoupper($item->product->name ?? $item->description) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align: right; font-weight: bold; padding: 8px 4px; border-top: 2px solid #000; border-bottom: 2px solid #000;">Total Jumlah</td>
                    <td style="text-align: center; font-weight: bold; padding: 8px 4px; border-top: 2px solid #000; border-bottom: 2px solid #000;">{{ $totalQty }}</td>
                    <td style="text-align: right; font-weight: bold; padding: 8px 4px; border-top: 2px solid #000; border-bottom: 2px solid #000;">Total:</td>
                    <td style="text-align: right; font-weight: bold; padding: 8px 4px; border-top: 2px solid #000; border-bottom: 2px solid #000;">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
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
                    <div>Hormat Kami</div>
                    <div style="margin-top: 40px;">
                        <div class="sig-line"></div>
                        <div>( New Citra Indonesia )</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
