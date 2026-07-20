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
                    <div class="label-title">NO PESANAN</div>
                    <div class="val-text">{{ $consignment->invoice?->invoice_number ?? '-' }}</div>
                </td>
                <td width="27%">
                    <div class="label-title">NO PENGIRIMAN</div>
                    <div class="val-text">{{ $consignment->shipment_number }}</div>
                </td>
                <td width="23%">
                    <div class="label-title">TANGGAL DIBUAT</div>
                    <div class="val-text">{{ \Carbon\Carbon::parse($consignment->shipment_date)->format('d M Y') }}</div>
                </td>
                <td width="23%" style="text-align: right;">
                    <div style="font-size: 15px; font-weight: bold; white-space: nowrap;">Surat Jalan</div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- ADDRESS & OUTLET (2 COLUMNS) -->
        <table class="info-table">
            <tr>
                <td width="50%" style="vertical-align: top; padding-right: 15px;">
                    <div class="label-title" style="margin-bottom: 4px;">Outlet</div>
                    <div style="line-height: 1.3;">
                        <div><strong>NEW CITRA INDONESIA</strong></div>
                        <div>Jl. Rogojembangan Barat 1 No.31</div>
                        <div>Semarang</div>
                        <div>Telp: 081225096633</div>
                    </div>
                </td>
                <td width="50%" style="vertical-align: top; padding-left: 10px;">
                    <div class="label-title" style="margin-bottom: 4px;">Dikirimkan ke Alamat</div>
                    <div style="line-height: 1.3;">
                        <div><strong>{{ $consignment->store?->name ?? 'Toko Dihapus' }}</strong></div>
                        <div>{{ $consignment->store?->address ?? '-' }}</div>
                        <div>Telp: {{ $consignment->store?->phone_number ?? '-' }}</div>
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
                    <th width="17%">SKU</th>
                    <th width="46%">Deskripsi</th>
                    <th width="15%" style="text-align: center;">QTY</th>
                    <th width="15%" style="text-align: center;">Unit</th>
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
        </table>

        <div class="divider"></div>

        <div style="text-align: right; font-weight: bold; font-size: 12px; margin-top: 4px; padding-right: 15%;">
            Total Jumlah: {{ $totalQty }}
        </div>

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