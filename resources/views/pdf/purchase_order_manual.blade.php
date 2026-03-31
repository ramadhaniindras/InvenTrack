<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order - {{ $po_number }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
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
        .notes { margin-top: 20px; font-style: italic; border-top: 1px dashed #ccc; padding-top: 10px; }
    </style>
</head>

<body>
    <div class="header" style="padding: 20px;">
        <table width="100%">
            <tr>
                <td class="title">Purchase Order (PO)</td>
                <td align="right">No: {{ $po_number }}<br>Tanggal: {{ $date }}</td>
            </tr>
        </table>
    </div>

    <div style="padding: 0 20px;">
        <table class="info-table">
            <tr>
                <td width="50%">
                    <strong>Kepada Supplier:</strong><br>
                    {{ $supplier->name }}<br>
                    WA: {{ $supplier->phone ?? '-' }}
                </td>
                <td align="right">
                    <strong>Pemesan:</strong><br>
                    InvenTrack<br>
                    Admin: {{ auth()->user()->name }}
                </td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="45%">Nama Barang</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="20%" class="text-right">Harga Satuan</th>
                    <th width="20%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach($items as $index => $item)
                    @php 
                        // Karena ini Manual, kita pake data quantity & price yang diinput di form
                        $subtotal = $item->quantity * $item->unit_price;
                        $grandTotal += $subtotal; 
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->product->name }}</strong><br>
                            <small>SKU: {{ $item->product->sku }}</small>
                        </td>
                        <td class="text-center" style="font-weight: bold;">{{ $item->quantity }}</td>
                        <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-right">TOTAL PEMBAYARAN</td>
                    <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        @if($notes)
        <div class="notes">
            <strong>Catatan:</strong> {{ $notes }}
        </div>
        @endif

        <div class="footer">
            <p>Hormat Kami,</p>
            <div class="signature">
                ( {{ auth()->user()->name }} )
            </div>
        </div>
    </div>
</body>
</html>