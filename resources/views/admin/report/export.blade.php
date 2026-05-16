<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Report Export</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h2>Revenue Report</h2>
    <p>Periode: {{ $start }} s/d {{ $end }} | Group: {{ $groupBy }}</p>
    <table>
        <thead>
            <tr>
                <th>Periode</th>
                <th>Jumlah Order</th>
                <th>Revenue</th>
                <th>Avg Order Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenueRows as $row)
                <tr>
                    <td>{{ $row['periode'] }}</td>
                    <td>{{ $row['jumlah_order'] }}</td>
                    <td>{{ number_format($row['revenue'], 0, ',', '.') }}</td>
                    <td>{{ number_format($row['avg_order_value'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
