<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $consignment->shipment_number }}</title>
    <style>
        @page {
            size: Letter;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
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
            margin: 8px 0;
        }
        .info-table td {
            vertical-align: top;
            padding: 4px 0;
            font-size: 13px;
            line-height: 1.5;
        }
        .items-table th {
            text-align: left;
            padding: 7px 4px;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .items-table td {
            padding: 6px 4px;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .sig-table {
            margin-top: 35px;
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
                    <div style="font-size: 22px; font-weight: bold; white-space: nowrap; letter-spacing: 1px;">SURAT JALAN</div>
                    <div style="font-size: 14px; font-weight: bold; margin-top: 5px; white-space: nowrap;">No: {{ $consignment->shipment_number }}</div>
                    <div style="font-size: 13px; font-weight: bold; margin-top: 3px; white-space: nowrap;">Tgl: {{ \Carbon\Carbon::parse($consignment->shipment_date)->format('d M Y') }}</div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- ADDRESS & OUTLET (2 COLUMNS) -->
        <table class="info-table">
            <tr>
                <td width="48%" style="vertical-align: top; padding-right: 15px;">
                    <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; letter-spacing: 0.5px;">No. Pesanan / PO</div>
                    <div style="font-size: 14px; font-weight: bold; margin-bottom: 12px;">{{ $consignment->invoice?->invoice_number ?? '-' }}</div>

                    <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 3px; letter-spacing: 0.5px;">Pengirim / Outlet</div>
                    <div style="line-height: 1.4; font-size: 13px;">
                        <div><strong>NEW CITRA INDONESIA</strong></div>
                        <div>Semarang</div>
                    </div>
                </td>
                <td width="52%" style="vertical-align: top; padding-left: 10px;">
                    <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; letter-spacing: 0.5px;">Dikirimkan Ke Alamat</div>
                    <div style="line-height: 1.4; font-size: 14px;">
                        <div><strong>{{ $consignment->store?->name ?? 'Toko Dihapus' }}</strong></div>
                        <div>{{ $consignment->store?->address ?? '-' }}</div>
                        <div>Telp: {{ $consignment->store?->phone_number ?? '-' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- ITEMS TABLE WITH INTEGRATED TOTAL JML ROW -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="7%">No.</th>
                    <th width="16%">SKU</th>
                    <th width="45%">Deskripsi Barang</th>
                    <th width="16%" style="text-align: center;">QTY</th>
                    <th width="16%" style="text-align: center;">Unit</th>
                </tr>
            </thead>
            <tbody>
                @php $totalQty = 0; @endphp
                @foreach($consignment->items as $index => $item)
                @php $totalQty += $item->quantity; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->sku ?? '-' }}</td>
                    <td>{{ strtoupper($item->product->name) }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: center;">Pcs</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold; padding: 8px 4px; border-top: 2px solid #000; border-bottom: 2px solid #000;">Total Jumlah</td>
                    <td style="text-align: center; font-weight: bold; padding: 8px 4px; border-top: 2px solid #000; border-bottom: 2px solid #000;">{{ $totalQty }}</td>
                    <td style="border-top: 2px solid #000; border-bottom: 2px solid #000;"></td>
                </tr>
            </tfoot>
        </table>

        <!-- SIGNATURES -->
        <table class="sig-table">
            <tr>
                <td>
                    <div>Penerima / Toko</div>
                    <div style="margin-top: 40px;">
                        <div class="sig-line"></div>
                        <div>( Nama Terang & Cap )</div>
                    </div>
                </td>
                <td>
                    <div>Pengirim / Gudang</div>
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