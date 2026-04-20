<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Penjualan - {{ $directSale->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }
        .header {
            width: 100%;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #b91c1c; /* Merah gelap khas New Citra */
        }
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
        }
        .table-info {
            width: 100%;
            margin-bottom: 20px;
        }
        .table-info td {
            vertical-align: top;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-items th, .table-items td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table-items th {
            background-color: #f3f4f6;
            text-align: left;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer {
            margin-top: 50px;
            width: 100%;
        }
        .signature {
            width: 200px;
            text-align: center;
            float: right;
        }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td width="50%">
                <div class="company-name">CV. NEW CITRA INDONESIA</div>
                <div>Jl. Kedungmundu Raya No. 161A Tembalang</div>
                <div>Semarang, 50273</div>
                <div>Telp: 085866228323</div>
            </td>
            <td width="50%" class="invoice-title">
                NOTA PENJUALAN<br>
                <span style="font-size: 14px; font-weight: normal;">No: {{ $directSale->invoice_number }}</span>
            </td>
        </tr>
    </table>

    <hr style="border: 1px solid #ddd; margin-bottom: 20px;">

    <table class="table-info">
        <tr>
            <td width="15%"><strong>Tanggal</strong></td>
            <td width="35%">: {{ \Carbon\Carbon::parse($directSale->sale_date)->format('d F Y') }}</td>
            <td width="15%"><strong>Kepada</strong></td>
            <td width="35%">: 
                @if($directSale->store_id)
                    {{ $directSale->store->name }} (Mitra)
                @else
                    {{ $directSale->customer_name }} (Umum)
                @endif
            </td>
        </tr>
        <tr>
            <td><strong>Catatan</strong></td>
            <td>: {{ $directSale->notes ?? '-' }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table class="table-items">
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="45%">Deskripsi Barang</th>
                <th class="text-center" width="10%">Qty</th>
                <th class="text-right" width="20%">Harga (Rp)</th>
                <th class="text-right" width="20%">Subtotal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($directSale->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->product->name }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right" style="font-size: 16px;">GRAND TOTAL</th>
                <th class="text-right" style="font-size: 16px;">{{ number_format($directSale->total_amount, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Hormat Kami,</p>
            <br><br><br><br>
            <p>( ____________________ )</p>
        </div>
        <div style="clear: both;"></div>
        <p style="font-size: 11px; color: #777; margin-top: 30px;">* Nota ini adalah bukti pembayaran yang sah.</p>
    </div>

</body>
</html>