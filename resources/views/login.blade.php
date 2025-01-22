<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e8f5e9;
            font-family: Arial, sans-serif;
        }

        .login-card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .login-header {
            background-color: #388e3c;
            color: #fff;
            padding: 20px;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .btn-green {
            background-color: #388e3c;
            color: #fff;
            border: none;
        }

        .btn-green:hover {
            background-color: #2e7d32;
        }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card login-card w-100" style="max-width: 400px;">
            <div class="login-header text-center">
                <h2>SignIn</h2>
            </div>
            <div class="card-body">
            <form action="/login" method="POST">
                @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label">Email </label>
                        <input type="email" class="form-control" name="email" placeholder="Enter your email" value="admin@epharma.com" required>
                    </div>
                    <div class="mb-5">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Enter your password" value="admin123" required>
                    </div>
                  
                    <button type="submit" class="btn btn-green w-100 mb-2">Login</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
