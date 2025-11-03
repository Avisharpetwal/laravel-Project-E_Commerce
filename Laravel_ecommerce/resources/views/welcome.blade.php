<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Shop | Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: #f8f9fa;
        }
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-card {
            background: #fff;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 6px 25px rgba(0,0,0,0.1);
            text-align: center;
        }
        .hero-card h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        .hero-card p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            color: #6c757d;
        }
        .hero-card .btn {
            min-width: 140px;
            margin: 5px;
        }
    </style>
</head>
<body>

<div class="hero">
    <div class="hero-card">
        <h1>Welcome to E-Shop</h1>
        <p>Find the best products at unbeatable prices.</p>

        <div>
            <a href="{{ route('products.list') }}" class="btn btn-primary btn-lg">Products</a>
            <a href="{{ route('login') }}" class="btn btn-success btn-lg">Login</a>
            <a href="{{ route('register') }}" class="btn btn-warning btn-lg">Register</a>
        </div>
    </div>
</div>

</body>
</html>
