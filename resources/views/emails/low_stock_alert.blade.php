<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Alert</title>
</head>
<body>
    <p>Dear Guardian,</p>
    <p>The following medicines for {{ $emailData['user_name'] }} have low stock:</p>
    <ul>
        @foreach ($emailData['low_stock_medicines'] as $medicine)
            <li>{{ $medicine->medicine_name }} (Remaining: {{ $medicine->total_count }})</li>
        @endforeach
    </ul>
    <p>Please take the necessary steps to restock these medicines.</p>
    <p>Thank you.</p>
</body>
</html>

