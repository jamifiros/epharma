<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Alert</title>
</head>
<body>
    <p>Dear Guardian,</p>
    <p>The following medicines for {{ $user_name }} have low stock:</p>
    <ul>
        @foreach ($low_stock_medicines as $medicine)
            <li>{{ $medicine->name }} (Remaining: {{ $medicine->total_count }})</li>
        @endforeach
    </ul>
    <p>Please take the necessary steps to restock these medicines.</p>
    <p>Thank you.</p>
</body>
</html>
