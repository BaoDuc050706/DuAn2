<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gear Store')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            font-size: 2rem;
            letter-spacing: .3px;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }
        header .navbar-brand .brand-icon {
            font-size: 2.2rem;
            line-height: 1;
        }
        header .navbar-brand .brand-text {
            font-size: 2rem;
            line-height: 1;
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

        /* Global UI polish */
        .hero {
            background: linear-gradient(135deg, #1b1b1b 0%, #2a2a2a 60%, #111 100%);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.36);
        }
        .hero .badge {
            background: #ff4c00;
        }
        .product-card {
            transition: transform .15s ease, box-shadow .15s ease;
            border: 1px solid #eee;
        }
        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(0,0,0,.08);
        }
        .product-card .price {
            color: #e53935;
            font-weight: 700;
        }
        .section-title {
            font-weight: 700;
        }

        /* Fly to cart + toast */
        .fly-img {
            position: fixed;
            z-index: 1055;
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0,0,0,.15);
            transition: transform .6s cubic-bezier(.2,.8,.2,1), opacity .6s ease;
            pointer-events: none;
        }
        .toast-fixed {
            position: fixed;
            right: 16px;
            bottom: 16px;
            z-index: 1060;
            min-width: 260px;
        }

        /* Header like reference */
        .topbar {
            background: #d71920; /* red */
        }
        .topbar .navbar-brand {
            color: #fff !important;
        }
        .category-btn {
            background: rgba(255,255,255,.12);
            color: #fff;
            border: 1px solid rgba(255,255,255,.2);
            padding: .5rem .9rem;
            border-radius: .5rem;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-weight: 600;
        }
        .category-btn i { font-size: 1.1rem; }
        .category-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            background: #fff;
            color: #111;
            border: 1px solid #eee;
            border-radius: .5rem;
            box-shadow: 0 10px 24px rgba(0,0,0,.08);
            min-width: 260px;
            display: none;
        }
        .category-item { padding: .65rem .9rem; display: flex; align-items: center; gap: .5rem; text-decoration: none; color: #222; }
        .category-item:hover { background: #f6f7f8; }
        .search-wrap .form-control {
            border-radius: 2rem 0 0 2rem;
            border: none;
        }
        .search-wrap .btn {
            border-radius: 0 2rem 2rem 0;
        }
        .info-item {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            color: #fff;
            white-space: nowrap;
        }
        .info-item .label { opacity: .9; font-size: .9rem; line-height: 1; }
        .info-item .value { font-weight: 700; line-height: 1; }

        .subbar {
            background: #f8f9fa;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }
        .quick-item {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .6rem .8rem;
            color: #444;
            font-size: .95rem;
        }
        /* === DARK MODE === */
        .dark-mode body { background-color: #111; color: #ddd; }
        .dark-mode header { background-color: #000; color: #fff; }
        .dark-mode .subbar { background-color: #222; border-color: #333; }
        .dark-mode footer { background-color: #000; color: #aaa; }
        .dark-mode .product-card { background-color: #1a1a1a; border-color: #333; }
        .dark-mode .product-card .price { color: #ff6b6b; }
        .dark-mode .hero {
            background: linear-gradient(135deg, #3a3a3a 0%, #1f1f1f 60%, #111 100%);
            color: #fff;
        }
        .dark-mode .subbar {
            background-color: #222;
            border-color: #333;
        }

        .dark-mode .subbar .quick-item {
            color: #fff; 
        }

        /* === DARK/LIGHT MODE SWITCH === */
        .theme-switch { width: 60px; height: 30px; cursor: pointer; position: relative; display: inline-block; }
        .theme-switch .switch-track { width: 100%; height: 100%; background: #ccc; border-radius: 30px; position: relative; transition: background 0.3s; }
        .theme-switch.dark-mode .switch-track { background: #555; }
        .theme-switch .switch-thumb { width: 26px; height: 26px; background: white; border-radius: 50%; position: absolute; top: 2px; left: 2px; display: flex; align-items: center; justify-content: center; transition: left 0.3s, background 0.3s, color 0.3s; font-size: 14px; color: #333; }
        .theme-switch.dark-mode .switch-thumb { left: 32px; background: #333; color: #ffd700; }
    .categories-card {
        background-color: #f8f9fa;
        transition: background-color 0.3s, color 0.3s;
    }

    .dark-mode .categories-card {
        background-color: #2a2a2a;
        color: #fff;
    }

    /* Nút xem sản phẩm */
    .category-btn {
    transition: transform 0.15s ease, background-color 0.2s ease;
}
.category-btn:hover {
    transform: scale(1.05);           /* phóng to nhẹ */
    background-color: rgba(255,255,255,0.2);  /* đổi màu nền nhẹ */
}

/* dropdown ẩn mặc định */
.category-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    min-width: 260px;
    background: #fff;
    border: 1px solid #eee;
    border-radius: .5rem;
    box-shadow: 0 10px 24px rgba(0,0,0,.08);
}

/* dropdown xổ xuống khi click (toggle class active) */
.category-wrap.active .category-dropdown {
    display: block;
}
.topbar .d-flex {
    flex-wrap: nowrap !important;
}

.topbar .search-wrap {
    flex: 1 1 auto;
    min-width: 300px;
}

.topbar .info-item {
    white-space: nowrap;
}

    </style>
</head>
<body

    {{-- HEADER --}}
    <header>
        {{-- TOP BAR --}}
        <div class="topbar">
            <div class="container py-2">
                <div class="d-flex align-items-center justify-content-between flex-nowrap position-relative w-100">
                    <a class="navbar-brand m-0" href="/"><span class="brand-icon">🖥️</span><span class="brand-text">CAEKT</span></a>

                    <div class="category-wrap position-relative">
                        <button class="category-btn" id="categoryToggle">
                            <i class="bi bi-list"></i> Danh mục
                        </button>
                        <div class="category-dropdown mt-2" id="categoryDropdown">
                            @forelse(($featuredCategories ?? collect()) as $cat)
                                <a class="category-item" href="{{ url('/category/' . $cat->slug) }}">
                                    <i class="bi bi-tag"></i> <span>{{ $cat->name }}</span>
                                </a>
                            @empty
                                <div class="p-3 text-muted small">Chưa có danh mục</div>
                            @endforelse
                        </div>
                    </div>
                    <form class="ms-0 ms-md-2 flex-grow-1 search-wrap d-none d-md-flex" role="search">
                        <input class="form-control" type="search" placeholder="Bạn cần tìm gì?">
                        <button class="btn btn-dark" type="submit"><i class="bi bi-search"></i></button>
                    </form>

                    <div class="ms-auto d-none d-lg-flex align-items-center gap-4">
                        <div class="info-item"><i class="bi bi-headphones"></i> <span class="label">Hotline</span> <span class="value">1900.5301</span></div>
                        <div class="info-item"><i class="bi bi-receipt"></i> <span class="label">Tra cứu</span> <span class="value">Đơn hàng</span></div>
                        <a href="{{ route('cart.index') }}" class="info-item text-decoration-none position-relative" id="cartLink">
                            <i class="bi bi-cart" id="cartIcon"></i> <span class="value">Giỏ hàng</span>
                            @php
                                $cart = session('cart', []);
                                $cartQty = collect($cart)->sum('qty');
                            @endphp
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark {{ $cartQty > 0 ? '' : 'd-none' }}" id="cartQtyBadge">{{ $cartQty }}</span>
                        </a>
                            <!-- Toggle Dark/Light Mode Switch -->
                            <div id="theme-toggle" class="theme-switch">
                                <div class="switch-track">
                                    <div class="switch-thumb">
                                        <i class="bi bi-moon"></i>
                                    </div>
                                </div>
                            </div>
                        @auth
                            <div class="dropdown">
                                <a class="info-item text-decoration-none dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle"></i> <span class="value">{{ auth()->user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><span class="dropdown-item-text"><i class="bi bi-envelope"></i> {{ auth()->user()->email }}</span></li>
                                    <li><span class="dropdown-item-text"><i class="bi bi-telephone"></i> {{ auth()->user()->phone ?? 'Chưa có SĐT' }}</span></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('home') }}">Trang chủ</a></li>
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Thông tin cá nhân</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="post" action="{{ route('logout') }}" class="px-3 py-1">
                                            @csrf
                                            <button class="btn btn-link p-0 text-danger">Đăng xuất</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="info-item text-decoration-none"><i class="bi bi-person"></i> <span class="value">Đăng nhập</span></a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        {{-- SUB BAR --}}
        <div class="subbar">
            <div class="container d-flex flex-wrap gap-3 py-2">
                <span class="quick-item"><i class="bi bi-bag-check"></i> Mua PC tặng màn 240Hz</span>
                <span class="quick-item"><i class="bi bi-fire"></i> Hot Deal</span>
                <span class="quick-item"><i class="bi bi-laptop"></i> Laptop</span>
                <span class="quick-item"><i class="bi bi-gear"></i> Dịch vụ kỹ thuật tại nhà</span>
                <span class="quick-item"><i class="bi bi-arrow-left-right"></i> Thu cũ đổi mới</span>
                <span class="quick-item"><i class="bi bi-shield-check"></i> Tra cứu bảo hành</span>
            </div>
        </div>
    </header>

    {{-- CONTENT --}}
    <main class="container mt-4">
        @php $flashSuccess = session()->pull('success'); $flashError = session()->pull('error'); @endphp
        @if ($flashSuccess)
            <div class="alert alert-success">{{ $flashSuccess }}</div>
        @endif
        @if ($flashError)
            <div class="alert alert-danger">{{ $flashError }}</div>
        @endif
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <h5>CAEKT</h5>
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
    <script>
        (function(){
            const badge = document.getElementById('cartQtyBadge');
            const cartIcon = document.getElementById('cartIcon');

            function showToast(message) {
                const wrap = document.createElement('div');
                wrap.className = 'toast-fixed alert alert-success shadow';
                wrap.textContent = message;
                document.body.appendChild(wrap);
                setTimeout(() => { wrap.remove(); }, 3000);
            }

            function updateBadge(byQty) {
                if (!badge) return;
                const current = parseInt(badge.textContent || '0', 10) || 0;
                const next = Math.max(0, current + byQty);
                badge.textContent = String(next);
                badge.classList.toggle('d-none', next === 0);
            }

            function flyToCart(fromImg) {
                if (!fromImg || !cartIcon) return;
                const imgRect = fromImg.getBoundingClientRect();
                const cartRect = cartIcon.getBoundingClientRect();
                const clone = document.createElement('img');
                clone.src = fromImg.src;
                clone.className = 'fly-img';
                clone.style.left = imgRect.left + 'px';
                clone.style.top = imgRect.top + 'px';
                document.body.appendChild(clone);
                const dx = cartRect.left - imgRect.left;
                const dy = cartRect.top - imgRect.top;
                requestAnimationFrame(() => {
                    clone.style.transform = `translate(${dx}px, ${dy}px) scale(.2)`;
                    clone.style.opacity = '0.2';
                });
                setTimeout(() => clone.remove(), 650);
            }

            function handleAddToCartSubmit(e){
                const form = e.target.closest('form');
                if (!form || !form.classList.contains('add-to-cart-form')) return;
                e.preventDefault();
                const action = form.getAttribute('action');
                const formData = new FormData(form);
                const qty = parseInt(formData.get('qty') || '1', 10) || 1;
                const token = form.querySelector('input[name=_token]')?.value;
                const fromImg = form.dataset.img ? document.querySelector(form.dataset.img) : form.closest('.product-card')?.querySelector('img');
                fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token || ''
                    },
                    body: formData
                }).then(() => {
                    updateBadge(qty);
                    flyToCart(fromImg);
                    showToast('Đã thêm vào giỏ hàng');
                }).catch(() => {
                    showToast('Không thể thêm vào giỏ. Vui lòng thử lại.');
                });
            }

            document.addEventListener('submit', handleAddToCartSubmit);
        })();
    </script>
    <script>
(function(){
    const toggle = document.getElementById('theme-toggle');
    if (!toggle) return;
    const html = document.documentElement;
    const thumb = toggle.querySelector('.switch-thumb');

    // Áp dụng theme khi load
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme === 'dark') {
        html.classList.add('dark-mode');
        toggle.classList.add('dark-mode');
        thumb.innerHTML = '<i class="bi bi-sun"></i>';
    } else {
        thumb.innerHTML = '<i class="bi bi-moon"></i>';
    }

    toggle.addEventListener('click', () => {
        const isDark = html.classList.toggle('dark-mode');
        toggle.classList.toggle('dark-mode');
        if (isDark) {
            thumb.innerHTML = '<i class="bi bi-sun"></i>';
            localStorage.setItem('theme', 'dark');
        } else {
            thumb.innerHTML = '<i class="bi bi-moon"></i>';
            localStorage.setItem('theme', 'light');
        }
    });
})();
</script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const wrap = document.querySelector('.category-wrap');
    const btn = document.getElementById('categoryToggle');

    // click nút để mở/đóng dropdown
    btn.addEventListener('click', function(e){
        e.stopPropagation(); // tránh click ngoài đóng
        wrap.classList.toggle('active');
    });

    // click ra ngoài đóng dropdown
    document.addEventListener('click', function(){
        wrap.classList.remove('active');
    });
});
</script>

</body>
</html>
