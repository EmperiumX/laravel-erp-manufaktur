<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $consignment->shipment_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #333;
        }
        .header {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #1e3a8a; /* Biru gelap untuk dokumen resmi */
        }
        .document-title {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .table-info {
            width: 100%;
            margin-bottom: 20px;
        }
        .table-info td {
            vertical-align: top;
            padding: 3px 0;
        }
        .box-tujuan {
            border: 1px solid #333;
            padding: 10px;
            min-height: 80px;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-items th, .table-items td {
            border: 1px solid #333;
            padding: 8px;
        }
        .table-items th {
            background-color: #e5e7eb;
            text-align: center;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Layout Tanda Tangan */
        .signature-table {
            width: 100%;
            margin-top: 40px;
            text-align: center;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 33.33%;
            padding: 10px;
            vertical-align: bottom;
            height: 120px;
        }
        .line {
            border-bottom: 1px solid #333;
            width: 80%;
            margin: 0 auto;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td width="50%">
                <div class="company-name">CV. NEW CITRA INDONESIA</div>
                <div>Jl. Kedungmundu Raya No. 161A Tembalang</div>
                <div>Semarang, Jawa Tengah 50273</div>
                <div>Telp: 085866228323</div>
            </td>
            <td width="50%" class="document-title">
                SURAT JALAN KONSINYASI<br>
                <span style="font-size: 14px; font-weight: normal;">No: {{ $consignment->shipment_number }}</span>
            </td>
        </tr>
    </table>

    <table class="table-info">
        <tr>
            <td width="50%">
                <table width="100%">
                    <tr>
                        <td width="30%"><strong>Tanggal Kirim</strong></td>
                        <td width="70%">: {{ \Carbon\Carbon::parse($consignment->shipment_date)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Supir / Kurir</strong></td>
                        <td>: ___________________________</td>
                    </tr>
                    <tr>
                        <td><strong>No. Kendaraan</strong></td>
                        <td>: ___________________________</td>
                    </tr>
                    <tr>
                        <td><strong>Catatan</strong></td>
                        <td>: {{ $consignment->notes ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                <div class="box-tujuan">
                    <strong>Kepada Yth. (Tujuan Pengiriman):</strong><br>
                    <span style="font-size: 15px; font-weight: bold;">{{ $consignment->store->name }}</span><br>
                    Kategori: {{ $consignment->store->category }}<br>
                    {{ $consignment->store->address ?? 'Alamat tidak tersedia' }}<br>
                    Telp: {{ $consignment->store->phone_number ?? '-' }}
                </div>
            </td>
        </tr>
    </table>

    <p style="margin-bottom: 10px;">Bersama dengan ini kami kirimkan sejumlah barang jadi (produk) sebagai titipan konsinyasi dengan rincian sebagai berikut:</p>

    <table class="table-items">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Nama Barang / Produk</th>
                <th width="15%">Kuantitas</th>
                <th width="15%">Harga Jual (Rp)</th>
                <th width="20%">Subtotal Titipan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consignment->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->product->name }}</td>
                <td class="text-center" style="font-size: 14px; font-weight:bold;">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right" style="font-size: 14px;">TOTAL NILAI BARANG DITITIPKAN</th>
                <th class="text-right" style="font-size: 14px;">{{ number_format($consignment->total_amount, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <p style="font-size: 11px; font-style: italic; margin-top: 10px;">
        * Barang-barang di atas telah diterima dalam kondisi baik dan cukup.<br>
        * Bukti surat jalan ini sah sebagai dokumen penagihan sesuai dengan nilai barang yang terjual nantinya.
    </p>

    <!-- Bagian Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td>
                <div>Penerima / Toko</div>
                <div style="margin-top: 60px;">
                    <div class="line"></div>
                    ( Nama Terang & Cap Toko )
                </div>
            </td>
            <td>
                <div>Pengantar / Supir</div>
                <div style="margin-top: 60px;">
                    <div class="line"></div>
                    ( Nama Terang )
                </div>
            </td>
            <td>
                <div>Pengirim / Bag. Gudang</div>
                <div style="margin-top: 60px;">
                    <div class="line"></div>
                    ( Nama Terang )
                </div>
            </td>
        </tr>
    </table>

</body>
</html>