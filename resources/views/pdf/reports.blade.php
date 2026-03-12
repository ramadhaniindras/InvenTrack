<html>
<head>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .in { color: green; } .out { color: red; }
    </style>
</head>
<body>
    <h2>{{ $title }}</h2>
    <p>Dicetak pada: {{ $date }}</p>
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Produk</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $m)
            <tr>
                <td>{{ $m->created_at->format('H:i') }}</td>
                <td>{{ $m->product->name }}</td>
                <td class="{{ $m->type }}"><strong>{{ strtoupper($m->type) }}</strong></td>
                <td>{{ $m->quantity }}</td>
                <td>{{ $m->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>