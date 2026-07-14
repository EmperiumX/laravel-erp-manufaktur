<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $consignment->shipment_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: {!! $settings->invoice_font ?? "'Helvetica Neue', Helvetica, Arial, sans-serif" !!};
            font-size: 12px;
            color: #333;
            background: #fff;
            padding: 12mm 15mm;
        }

        /* NARROW TABLES FOR STEP 1 */
        .narrow-table {
            width: 75%;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #b91c1c;
            letter-spacing: 0.5px;
        }
        .company-info {
            font-size: 11px;
            color: #555;
            line-height: 1.5;
            margin-top: 4px;
        }
        .doc-title {
            font-size: 20px;
            font-weight: bold;
            color: #a81a1a;
            text-align: right;
            text-transform: uppercase;
        }
        .doc-number {
            font-size: 14px;
            font-weight: bold;
            color: #a81a1a;
            margin-top: 4px;
            text-align: right;
        }

        .info-section-table {
            width: 75%;
            table-layout: fixed;
            margin-top: 20px;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .info-label {
            color: #6b7280;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 11px;
        }
        .info-table .label {
            font-weight: bold;
            color: #555;
        }
        .dest-box {
            border-radius: 6px;
            padding: 10px 12px;
            background-color: #fefaf0;
        }
        .dest-name {
            font-size: 13px;
            font-weight: bold;
            color: #a81a1a;
            margin-bottom: 2px;
        }
        .dest-detail {
            font-size: 11px;
            color: #444;
            line-height: 1.4;
        }

        /* VERTICAL SECTIONS */
        .section {
            width: 75%;
            margin-bottom: 20px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 15px;
        }
        .section h2 {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .label {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT (TABEL LEBAR 75%) -->
    <table class="narrow-table" style="margin-bottom: 20px;">
        <tr>
            <td width="55%" style="vertical-align: top; word-wrap: break-word; overflow-wrap: break-word;">
                <div class="company-name">NEW CITRA INDONESIA</div>
                <div class="company-info">
                    Jl. Rogojembangan Barat 1 No.31<br>
                    Semarang<br>
                    Telp: 081225096633, 082133326959, 085866228323
                </div>
            </td>
            <td width="45%" style="text-align: right; vertical-align: top;">
                <div class="doc-title">Surat Jalan</div>
                <div class="doc-number">{{ $consignment->shipment_number }}</div>
                @if($consignment->invoice)
                    <div style="font-size: 11px; font-weight: bold; color: #333; margin-top: 4px; text-align: right;">
                        No. Invoice: {{ $consignment->invoice->invoice_number }}
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <!-- DETAIL & TUJUAN PENGIRIMAN (TABEL LEBAR 75%) -->
    <table class="info-section-table">
        <tr>
            <td width="50%" style="vertical-align: top; padding-right: 15px;">
                <div class="info-label">Detail Pengiriman</div>
                <table class="info-table">
                    <tr>
                        <td width="35%" class="label">Tanggal Kirim</td>
                        <td>: {{ \Carbon\Carbon::parse($consignment->shipment_date)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Supir / Kurir</td>
                        <td>: _________________</td>
                    </tr>
                    <tr>
                        <td class="label">No. Kendaraan</td>
                        <td>: _________________</td>
                    </tr>
                    <tr>
                        <td class="label">Catatan</td>
                        <td>: {{ $consignment->notes ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td width="50%" style="vertical-align: top;">
                <div class="info-label">Tujuan Pengiriman</div>
                <div class="dest-box">
                    <div class="dest-name">{{ $consignment->store?->name ?? 'Toko Dihapus' }}</div>
                    <div class="dest-detail">
                        {{ $consignment->store?->address ?? 'Alamat tidak tersedia' }}<br>
                        Telp: {{ $consignment->store?->phone_number ?? '-' }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- DAFTAR BARANG (FLOW VERTIKAL - LEBAR 75%) -->
    <div class="section">
        <h2>Daftar Barang</h2>
        @foreach($consignment->items as $index => $item)
            <p style="margin-bottom: 4px; font-size: 11px;">
                {{ $index + 1 }}. {{ $item->product->name }} |
                Qty: {{ $item->quantity }} |
                Harga: Rp {{ number_format($item->unit_price, 0, ',', '.') }} |
                Subtotal: Rp {{ number_format($item->subtotal, 0, ',', '.') }}
            </p>
        @endforeach
        <p style="margin-top: 10px; font-size: 12px;"><span class="label">TOTAL NILAI BARANG:</span> Rp {{ number_format($consignment->total_amount, 0, ',', '.') }}</p>
    </div>

    <!-- TANDA TANGAN (FLOW VERTIKAL - LEBAR 75%) -->
    <div class="section" style="border-bottom: none;">
        <h2>Tanda Tangan</h2>
        <p style="margin-bottom: 15px; font-size: 11px;">Penerima / Toko: _________________________ ( Nama Terang & Cap Toko )</p>
        <p style="font-size: 11px;">Pengirim / Gudang: _________________________ ( Nama Terang )</p>
    </div>

</body>
</html>