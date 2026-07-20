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
            color: #000;
            background: #fff;
            padding: 8mm 0 8mm 12mm;
        }
        .main-container {
            width: 175mm;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .divider {
            border-top: 2px solid #000;
            margin: 8px 0;
        }
        .info-table td {
            vertical-align: top;
            padding: 4px 0;
            font-size: 14px;
        }
        .items-table th {
            text-align: left;
            padding: 7px 4px;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            font-size: 13px;
            font-weight: bold;
        }
        .items-table td {
            padding: 6px 4px;
            font-size: 14px;
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
            font-size: 14px;
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
        <!-- HEADER (2 COLUMNS - NO SQUEEZING) -->
        <table>
            <tr>
                <td width="60%" style="vertical-align: top;">
                    <div style="font-size: 20px; font-weight: bold;">NEW CITRA INDONESIA</div>
                    <div style="font-size: 13px; font-weight: bold; margin-top: 4px; line-height: 1.4;">
                        Jl. Rogojembangan Barat 1 No.31, Semarang<br>
                        Telp: 081225096633, 082133326959
                    </div>
                </td>
                <td width="40%" style="vertical-align: top; text-align: right;">
                    <div style="font-size: 22px; font-weight: bold; white-space: nowrap;">INVOICE</div>
                    <div style="font-size: 14px; font-weight: bold; margin-top: 6px;">No: {{ $invoice->invoice_number }}</div>
                    <div style="font-size: 13px; font-weight: bold; margin-top: 2px;">Tempo: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d F Y') }}</div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- ADDRESS & OUTLET (2 COLUMNS) -->
        <table class="info-table">
            <tr>
                <td width="48%" style="vertical-align: top; padding-right: 15px;">
                    <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 6px;">Referensi / DO</div>
                    <div style="font-size: 14px; font-weight: bold; margin-bottom: 10px;">{{ $invoice->reference ?? '-' }}</div>

                    <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 4px;">Penjual / Outlet</div>
                    <div style="line-height: 1.4; font-size: 13px;">
                        <div><strong>NEW CITRA INDONESIA</strong></div>
                        <div>Semarang</div>
                    </div>
                </td>
                <td width="52%" style="vertical-align: top; padding-left: 10px;">
                    <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 6px;">Pembeli / Customer</div>
                    <div style="line-height: 1.4; font-size: 14px;">
                        <div><strong>{{ $invoice->store?->name ?? 'Pelanggan Umum' }}</strong></div>
                        <div>{{ $invoice->store?->address ?? '-' }}</div>
                        <div>Telp: {{ $invoice->store?->phone_number ?? '-' }}</div>
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
                <td width="50%"></td>
                <td width="50%">
                    <table style="width: 100%;">
                        <tr>
                            <td><strong style="font-size: 14px;">Total Tagihan:</strong></td>
                            <td class="text-right"><strong style="font-size: 14px;">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</strong></td>
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
