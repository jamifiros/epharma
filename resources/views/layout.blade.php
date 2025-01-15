<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        /* Sidebar Styling */
        .sidebar {
            height: 100vh;
            width: 250px;
            background-color: #388e3c;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
        }

        .sidebar h4 {
            background-color: #2e7d32;
            padding: 15px;
            text-align: center;
            margin: 0;
            font-size: 18px;
            border-bottom: 1px solid #66bb6a;
        }

        .sidebar a {
            color: #dcedc8;
            text-decoration: none;
            padding: 10px 20px;
            display: block;
            transition: background-color 0.3s, color 0.3s;
        }

        .sidebar a:hover {
            background-color: #66bb6a;
            color: white;
        }

        .sidebar .active {
            background-color: #66bb6a;
            font-weight: bold;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            background-color: #f1f8e9;
            min-height: 100vh;
        }

        .navbar {
            background-color: #81c784;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .navbar .navbar-brand {
            color: white;
        }

        .navbar .nav-link {
            color: white;
        }

        .navbar .nav-link:hover {
            color: #e8f5e9;
        }

        /* Cards Styling */
        .card {
            border: 1px solid #c8e6c9;
        }

        .card-title {
            color: #388e3c;
        }

        .btn-green {
            background-color: #388e3c;
            color: white;
        }

        .btn-green:hover {
            background-color: #2e7d32;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4>E-Pharma</h4>
        <a href="{{route('admin.dashboard')}}">Dashboard</a>
        <a href="{{route('admin.prescriptions')}}">Prescriptions</a>
        <a href="{{route('admin.prescriptions.history')}}">Prescriptions History</a>
        <a href="{{route('admin.users')}}">Users</a>
        <a href="#">Reports</a>
        <a href="#">Settings</a>
        <a href="{{route('logout')}}">Logout</a>
    </div>

    <!-- Main Content -->
    @yield('content')
    @if(session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
@endif
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>