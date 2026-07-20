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
                    <div style="font-size: 22px; font-weight: bold; white-space: nowrap;">SURAT JALAN</div>
                    <div style="font-size: 14px; font-weight: bold; margin-top: 6px;">No: {{ $consignment->shipment_number }}</div>
                    <div style="font-size: 13px; font-weight: bold; margin-top: 2px;">Tgl: {{ \Carbon\Carbon::parse($consignment->shipment_date)->format('d F Y') }}</div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- ADDRESS & OUTLET (2 COLUMNS) -->
        <table class="info-table">
            <tr>
                <td width="48%" style="vertical-align: top; padding-right: 15px;">
                    <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 6px;">No. Pesanan / PO</div>
                    <div style="font-size: 14px; font-weight: bold; margin-bottom: 10px;">{{ $consignment->invoice?->invoice_number ?? '-' }}</div>

                    <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 4px;">Pengirim / Outlet</div>
                    <div style="line-height: 1.4; font-size: 13px;">
                        <div><strong>NEW CITRA INDONESIA</strong></div>
                        <div>Semarang</div>
                    </div>
                </td>
                <td width="52%" style="vertical-align: top; padding-left: 10px;">
                    <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 6px;">Dikirimkan Ke Alamat</div>
                    <div style="line-height: 1.4; font-size: 14px;">
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
                    <th width="8%">No.</th>
                    <th width="18%">SKU</th>
                    <th width="44%">Deskripsi Barang</th>
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

        <div style="text-align: right; font-weight: bold; font-size: 14px; margin-top: 6px;">
            Total Jumlah: {{ $totalQty }}
        </div>

        <!-- SIGNATURES -->
        <table class="sig-table">
            <tr>
                <td>
                    <div>Penerima / Toko</div>
                    <div style="margin-top: 45px;">
                        <div class="sig-line"></div>
                        <div>( Nama Terang & Cap )</div>
                    </div>
                </td>
                <td>
                    <div>Pengirim / Gudang</div>
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