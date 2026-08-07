<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe Order</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet"
      href="{{ asset('css/customer/cafe.css') }}">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <div class="container">

        <a class="navbar-brand d-flex align-items-center" href="/">
             <img src="{{ asset('images/logo.png') }}?v=5" alt="Essensia Koffie" style="height: 48px;">
        </a>

        <div class="navbar-nav">

            <a class="nav-link" href="/">Dashboard</a>

            <a class="nav-link" href="{{ route('categories.index') }}">
                Kategori
            </a>

            <a class="nav-link" href="{{ route('menus.index') }}"> 
                Menu
            </a>

            <a class="nav-link" href="{{ route('tables.index') }}">
                Meja
            </a>

            <li>

            <a href="{{ route('orders.index') }}">

                Pesanan

            </a>

</li>

        </div>

    </div>

</nav>

<div class="container mt-4">

    @yield('content')

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>