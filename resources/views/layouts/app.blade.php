<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gear Store')</title>

    {{-- CSS Bootstrap hoặc Tailwind (tùy bạn chọn) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font + Icon --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        header {
            background-color: #111;
            color: #fff;
        }
        header .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        header .nav-link {
            color: #ddd !important;
            font-weight: 500;
        }
        header .nav-link:hover {
            color: #fff !important;
        }
        footer {
            background-color: #222;
            color: #ccc;
            padding: 40px 0;
            margin-top: 60px;
        }
        footer a {
            color: #ccc;
            text-decoration: none;
        }
        footer a:hover {
            color: #fff;
        }
        .search-bar input {
            border-radius: 50px 0 0 50px;
            border-right: none;
        }
        .search-bar button {
            border-radius: 0 50px 50px 0;
            background: #ff4c00;
            color: white;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark px-3 py-2">
            <a class="navbar-brand" href="/">🖥️ GearZone</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item"><a class="nav-link" href="/home">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="/products">Sản phẩm</a></li>
                    <li class="nav-item"><a class="nav-link" href="/news">Tin tức</a></li>
                    <li class="nav-item"><a class="nav-link" href="/contact">Liên hệ</a></li>
                </ul>
                <form class="d-flex search-bar">
                    <input class="form-control" type="search" placeholder="Tìm kiếm gear, laptop, phụ kiện..." aria-label="Search">
                    <button class="btn" type="submit"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </nav>
    </header>

    {{-- CONTENT --}}
    <main class="container mt-4">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <h5>GearZone</h5>
                    <p>Chuyên cung cấp PC, laptop gaming và linh kiện chính hãng với giá tốt nhất.</p>
                </div>
                <div class="col-md-3 mb-3">
                    <h6>Sản phẩm</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">PC Gaming</a></li>
                        <li><a href="#">Laptop</a></li>
                        <li><a href="#">Màn hình</a></li>
                        <li><a href="#">Phụ kiện</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-3">
                    <h6>Hỗ trợ</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">Chính sách bảo hành</a></li>
                        <li><a href="#">Hướng dẫn mua hàng</a></li>
                        <li><a href="#">Chính sách đổi trả</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-3">
                    <h6>Liên hệ</h6>
                    <p><i class="bi bi-telephone"></i> 19009999</p>
                    <p><i class="bi bi-envelope"></i> support@gearzone.vn</p>
                    <p><i class="bi bi-geo-alt"></i> Đ.Nguyễn Đình Chiểu, Phường Sài Gòn, TP.HCM</p>
                </div>
            </div>
            <hr class="border-secondary">
            <p class="text-center mb-0">© 2025 GearZone. All rights reserved.</p>
        </div>
    </footer>

    {{-- JS Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
