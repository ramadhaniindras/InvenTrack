<!DOCTYPE html>
<html>
<head>
    <title>Purchase Order</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #333; }
        .header { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; color: #1976D2; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { background: #444; color: #fff; border: 1px solid #333; padding: 8px; text-align: left; }
        .data-table td { border: 1px solid #ccc; padding: 8px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #f2f2f2; font-weight: bold; font-size: 13px; }
        .footer { margin-top: 50px; text-align: right; }
        .signature { margin-top: 60px; font-weight: bold; text-decoration: underline; }
    </style>
</head>

<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td class="title">Purchase Order (PO)</td>
                <td align="right">No: {{ $poNumber }}<br>Tanggal: {{ $date }}</td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <strong>Kepada Supplier:</strong><br>
                {{ $supplier->name }}<br>
                WA: {{ $supplier->phone }}
            </td>
            <td align="right">
                <strong>Pemesan:</strong><br>
                Bengkel InvenTrack<br>
                Admin: {{ auth()->user()->name }}
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="35%">Nama Barang</th>
                <th width="10%" class="text-center">Sisa</th>
                <th width="10%" class="text-center">Order</th>
                <th width="20%" class="text-right">Harga Satuan</th>
                <th width="20%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($items as $index => $item)
                @php 
                    // Rumus Napas Panjang: 2x Limit stok dikurangi stok sekarang
                    $idealStock = $item->min_stock * 2; 
                    $qtyOrder = $idealStock - $item->stock;
                    
                    // Pastikan order tidak minus (jika stok tiba-tiba masuk tapi PO belum update)
                    if($qtyOrder < 0) $qtyOrder = 0;

                    $subtotal = $qtyOrder * $item->price;
                    $grandTotal += $subtotal; // Tambahkan ke Grand Total
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->name }}</strong><br>
                        <small>SKU: {{ $item->sku }}</small>
                    </td>
                    <td class="text-center">{{ $item->stock }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ $qtyOrder }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL ESTIMASI PEMBAYARAN</td>
                <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; font-style: italic;">
        * Harga di atas adalah estimasi berdasarkan data sistem terakhir.
    </div>

    <div class="footer">
        <p>Hormat Kami,</p>
        <div class="signature">
            ( {{ auth()->user()->name }} )
        </div>
    </div>
</body>
</html>