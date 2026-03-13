<!DOCTYPE html>
<html>
<head>
    <title>Laporan Inventory</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #f4f4f4; border: 1px solid #ddd; padding: 10px; text-align: left; }
        td { border: 1px solid #ddd; padding: 8px; }
        .text-center { text-align: center; }
        .badge-in { color: #2e7d32; font-weight: bold; }
        .badge-out { color: #d32f2f; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">INVENTRACK REPORT</h2>
        <p style="margin: 5px 0;">{{ $title }} - Periode: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu</th>
                <th>Produk</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>User</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <strong>{{ $item->product->name }}</strong><br>
                    <small>{{ $item->product->sku }}</small>
                </td>
                <td class="text-center">
                    <span class="{{ $item->type == 'in' ? 'badge-in' : 'badge-out' }}">
                        {{ strtoupper($item->type) }}
                    </span>
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td>{{ $item->user->name ?? 'System' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>