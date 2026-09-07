# CAEKT Gear Store

> **VI** — Website thương mại điện tử Laravel 12 bán laptop gaming, PC gear và phụ kiện (tai nghe, chuột, bàn phím, màn hình, loa). Có catalogue, giỏ hàng, đặt hàng, quản trị, chatbot tư vấn và dữ liệu mẫu.
> **EN** — A Laravel 12 e-commerce store for gaming laptops and PC peripherals, with catalogue, cart, checkout, admin, a support chatbot, and seed data.

[Tiếng Việt](#tiếng-việt) · [English](#english)

---

## Tiếng Việt

### Tổng quan

GearZone (giao diện chatbot gọi **CAEKT Gear Store**) là website bán laptop gaming và phụ kiện. Tầng hiển thị Blade tách khỏi routing; schema nằm trong migration; dữ liệu mẫu nằm trong seeder; Vite build CSS/JS. Apache chỉ trỏ vào `public/`.

### Tính năng đã có

**Khách / thành viên**

- Trang chủ `/home`: sản phẩm mới, lọc theo danh mục, danh mục nổi bật (không hiện Laptop/Loa ở block nổi bật; menu vẫn có đủ danh mục).
- Chi tiết sản phẩm `/product/{slug}` (ảnh chính từ `product_images` hoặc `products.image`).
- Trang danh mục `/category/{slug}`.
- Tìm kiếm `/search?q=` theo tên và mô tả.
- Đăng ký / đăng nhập / đăng xuất; hồ sơ (tên, email, SĐT, địa chỉ).
- Giỏ hàng session: thêm, tăng/giảm, xóa, xóa hết; tối đa 20 món/dòng; biến thể (RAM, SSD, màu, switch) khi có trên form.
- Đăng nhập: gộp giỏ session với `users.cart_json`.
- Checkout (cần đăng nhập): COD, chuyển khoản, thẻ (mô phỏng); phí ship 60.000đ, miễn phí nếu đơn ≥ 2.000.000đ; kiểm tra tồn kho; QR demo; lưu `orders`; gửi mail xác nhận (mặc định `MAIL_MAILER=log`).
- Lịch sử đơn `/orders` và tra cứu `/orders/lookup` — **theo session** sau khi đặt (admin xem đơn trên MySQL).
- Chatbot widget: `POST /chatbot/ask` (throttle 20/phút). Có `OPENAI_API_KEY` thì GPT; không có thì trả lời theo intent (danh mục, bán chạy, giá rẻ, ship, bảo hành, đặt hàng).

**Quản trị** (`auth` + middleware `admin`, prefix `/admin`)

- Dashboard: doanh thu đơn `delivered`, số đơn/user/sản phẩm, doanh thu 6 tháng, đơn gần đây, thống kê trạng thái.
- CRUD sản phẩm (upload ảnh `public/image`, slug, danh mục, giá, tồn, giảm giá).
- Danh sách / chi tiết đơn, lọc trạng thái, tìm mã/tên/email.
- Đổi trạng thái: `pending` → `processing` → `shipped` → `delivered` / `cancelled`. Khi chuyển sang **delivered** lần đầu: trừ `products.stock`.

**Chưa gắn route (có file, chưa dùng trên web)**

- `CheckoutController` — checkout thật đi `PaymentController`.
- `OrderTrackingController` — tra cứu DB (`order_code`); schema hiện dùng `order_number`. Tra cứu khách đang là `OrderController::lookup`.

### Công nghệ

| Lớp | Công nghệ | Vai trò |
| --- | --- | --- |
| Back end | PHP 8.2+, Laravel 12 | HTTP, routing, Blade, Eloquent, mail, middleware |
| Database | MySQL / MariaDB | Catalogue, user, đơn, session/cache/queue |
| Front end | Blade, JS, CSS | SSR + chatbot fetch JSON |
| Asset | Vite 7, Tailwind CSS 4 | Build / HMR |
| UI | Bootstrap 5, Bootstrap Icons, Google Fonts | Layout CDN |
| Tích hợp | `openai-php/client`, `simplesoftwareio/simple-qrcode` | Chat GPT (tùy chọn), QR demo |
| Local | XAMPP (Apache + MySQL) hoặc `php artisan serve` | Chạy local |
| Test | PHPUnit 11 | `php artisan test` (skeleton mặc định) |

### Kiến trúc

```text
Browser → Apache/XAMPP hoặc artisan serve → public/index.php
       → middleware (web, auth, admin) → routes/web.php
       → Controller → Eloquent / session → Blade

Chatbot: POST /chatbot/ask → ChatBotController → ChatBotService
         → OpenAI (nếu có key) hoặc trả lời theo từ khóa + Product query

Checkout: Cart session → PaymentController → orders (MySQL)
         → Mail::to(email) → xóa cart
```

- Document root Apache: **`public/`** — không trỏ thư mục gốc (lộ `.env`).
- Role: `users.role` = `user` \| `admin`; `User::isAdmin()`; alias middleware `admin`.
- Schema chỉ đổi bằng migration.

### Database

Nguồn: `database/migrations/`.

**users**

| Cột | Ý nghĩa |
| --- | --- |
| name, email, password | Auth |
| phone, address, city, district, ward, address_line | Liên hệ / địa chỉ |
| cart_json | Giỏ lưu DB, gộp khi login |
| role | `user` (mặc định) / `admin` |

Kèm Laravel: `password_reset_tokens`, `sessions`.

**categories** — `name`, `slug` (unique), `featured`, `parent_id` (cây, seeder đang để `null`).

**products** — `name`, `slug`, `category_id`, `price`, `stock`, `discount`, `connection`, `rgb`, `image`, `description`.

**product_images** — `product_id` (cascade), `image_url`, `is_primary`.

**orders**

| Cột | Ý nghĩa |
| --- | --- |
| user_id | Nullable, `onDelete set null` |
| order_number | Unique (`ORD` + timestamp + random) |
| full_name, email, phone, address | Người nhận |
| payment_method | `cod` \| `bank` \| `card` |
| subtotal, shipping, total | Tiền |
| status | `pending`, `processing`, `shipped`, `delivered`, `cancelled` |
| items | JSON dòng giỏ |

Hạ tầng: `cache`, `jobs`, `job_batches`, `failed_jobs`.

Quan hệ Eloquent: `Product` belongsTo `Category`, hasMany `ProductImage`; `Order` belongsTo `User`.

### Seeding

`php artisan db:seed` (hoặc `migrate --seed` / `migrate:fresh --seed`):

1. **DatabaseSeeder** — user `test@example.com` / mật khẩu factory **`password`**; insert danh mục rồi gọi seeder con.
2. **CategorySeeder** — `updateOrCreate` theo slug: Tai nghe, Chuột, Bàn phím, Màn hình, Loa, Laptop.
3. **AdminSeeder** — admin demo (đổi mật khẩu nếu repo public).
4. **ProductSeeder** — ~13 sản phẩm (bàn phím, chuột, tai nghe, laptop ASUS/HP/Dell), giá VND, tồn, giảm giá, file ảnh trong `public/`.

Tài khoản demo (local):

| Vai trò | Email | Mật khẩu |
| --- | --- | --- |
| User | `test@example.com` | `password` |
| Admin | `adminproject2@gmail.com` | `caekt2006` |

Admin: [http://gearzone.test/admin/dashboard](http://gearzone.test/admin/dashboard) (hoặc URL `artisan serve`).

### Yêu cầu

- XAMPP: Apache + MySQL **hoặc** PHP CLI + MySQL.
- PHP 8.2+ (`pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `xml`, `ctype`, `curl`).
- Composer 2.x, Node.js LTS, npm.
- Chatbot GPT: `OPENAI_API_KEY` (không bắt buộc).
- Mail thật: cấu hình SMTP; mặc định ghi log.

```powershell
php -v
composer --version
node -v
npm -v
```

### Cài đặt MySQL + XAMPP

1. Bật Apache và MySQL.
2. Tạo DB:

```sql
CREATE DATABASE gearzone
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

3. Trong thư mục dự án:

```powershell
Copy-Item .env.example .env
composer install
npm install
```

4. `.env` — MySQL (không dùng SQLite nếu chạy theo stack này):

```dotenv
APP_NAME="GearZone"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://gearzone.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gearzone
DB_USERNAME=root
DB_PASSWORD=

# Tùy chọn — chatbot GPT
# OPENAI_API_KEY=
# OPENAI_MODEL=gpt-4o-mini
```

Không commit `.env`.

5. Key, schema, seed, asset:

```powershell
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
```

`storage:link` nếu sau này dùng disk `public`; ảnh admin hiện copy vào `public/image`.

### Apache Virtual Host

DocumentRoot = `.../DuAn2/public`. Ví dụ `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    ServerName gearzone.test
    DocumentRoot "C:/xampp/htdocs/DuAn2/public"

    <Directory "C:/xampp/htdocs/DuAn2/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

`httpd.conf`: bật `mod_rewrite` và `Include conf/extra/httpd-vhosts.conf`. Hosts (Admin):

```text
127.0.0.1 gearzone.test
```

Restart Apache → [http://gearzone.test/home](http://gearzone.test/home). Không dùng vhost: `php artisan serve`.

### Workflow

```text
Migration → migrate --seed → Model / Controller / Validation / Route → Blade
→ npm run dev | build → php artisan test
```

| Mục tiêu | Lệnh |
| --- | --- |
| Asset dev | `npm run dev` |
| Build | `npm run build` |
| Migrate | `php artisan migrate` |
| Seed | `php artisan db:seed` |
| Schema + seed sạch (local) | `php artisan migrate:fresh --seed` |
| Rollback batch | `php artisan migrate:rollback` |
| Test | `php artisan test` |
| Xóa cache | `php artisan optimize:clear` |

`migrate:fresh` xóa **mọi bảng** của DB đang cấu hình — chỉ dùng local.

### Cấu trúc

```text
app/Http/Controllers/     Storefront + Admin
app/Http/Middleware/      AdminMiddleware
app/Models/               User, Category, Product, ProductImage, Order
app/Services/ChatBotService.php
app/Mail/                 Mail xác nhận đơn
database/migrations/
database/seeders/         Database, Category, Admin, Product
resources/views/          layouts, home, product, cart, checkout, auth, admin, emails
routes/web.php
public/image/             Ảnh upload / catalogue
tests/
```

- Validate trước khi ghi; Eloquent / binding, không nối SQL từ input.
- Logic ở controller/service, không nhồi Blade.
- UTF-8 source, MySQL `utf8mb4`.

---

## English

### Overview

GearZone is a Laravel 12 store for gaming laptops and peripherals. Blade is separate from routing; MySQL schema is versioned with migrations; seeders load demo catalogue and an admin user. Optional OpenAI powers the store chatbot.

### Implemented scope

- Home, category, product detail, search.
- Register/login/logout, profile, session cart with variant keys; merge into `cart_json` on login.
- Auth checkout: COD / bank / card (simulated), shipping rule, stock cap, demo QR, persist `orders`, confirmation mail.
- Customer order list/lookup via **session**; admin orders via **MySQL**.
- Admin dashboard, product CRUD, order status + stock decrement on first `delivered`.
- Chatbot JSON API with GPT or keyword fallback.
- Unused in `web.php`: `CheckoutController`, `OrderTrackingController`.

### Stack

PHP 8.2+ / Laravel 12, MySQL, Blade, Vite 7, Tailwind 4, Bootstrap 5, `openai-php/client`, Simple QR Code, PHPUnit 11, XAMPP or `artisan serve`.

### Database & seed

See the Vietnamese **Database** and **Seeding** sections (same schema and demo accounts). Run `php artisan migrate --seed` after MySQL `gearzone` + `.env`.

### Setup

1. Create `gearzone` (`utf8mb4` / `utf8mb4_unicode_ci`).
2. Copy `.env.example` → `.env`; `composer install`; `npm install`.
3. Set `APP_NAME`, `APP_URL`, MySQL `DB_*`. Optional `OPENAI_API_KEY`.
4. `php artisan key:generate`, `php artisan migrate --seed`, `npm run build`.
5. Point Apache at `public/` or use `php artisan serve`.

Keep `.env` private. `migrate:fresh --seed` is for disposable local data only.

## License

Composer skeleton is MIT. The app has no extra project license; add one before public reuse.
