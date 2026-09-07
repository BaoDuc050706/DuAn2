Exit code: 0
Wall time: 0.4 seconds
Output:
Exit code: 0
Wall time: 0.5 seconds
Output:
# GearZone

> **VI** — Nền tảng web GearZone xây dựng bằng Laravel, định hướng giới thiệu và kinh doanh thiết bị công nghệ.
> **EN** — A Laravel web application for showcasing and selling technology equipment.

[Tiếng Việt](#tiếng-việt) · [English](#english)

---

## Tiếng Việt

### Tổng quan

GearZone là nền tảng khởi đầu cho website về PC gaming, laptop và phụ kiện. Dự án tách phần hiển thị Blade khỏi định tuyến, quản lý schema bằng migration và dùng Vite cho asset front-end. Cấu trúc này sẵn sàng mở rộng catalogue, giỏ hàng, đơn hàng và quản trị mà không làm thay đổi nền tảng.

### Phạm vi hiện tại

- Trang GearZone tại `/home`, dùng layout header, content và footer chung.
- Laravel routing và Blade inheritance: `layouts.app` → `home`.
- Migration hạ tầng cho `users`, `sessions`, `cache`, `jobs`, `job_batches` và `failed_jobs`.
- Vite + Tailwind CSS đã được cấu hình; Bootstrap 5, Bootstrap Icons và Google Fonts đang được nạp qua CDN trong layout.

> Các liên kết Sản phẩm, Tin tức, Liên hệ, tìm kiếm và nghiệp vụ thương mại điện tử hiện mới là định hướng giao diện. Route, model, controller và logic tương ứng chưa được triển khai.

### Công nghệ

| Lớp | Công nghệ | Vai trò |
| --- | --- | --- |
| Back end | PHP 8.2+, Laravel 12 | HTTP lifecycle, routing, Blade, ORM và migration |
| Database | MySQL / MariaDB | Dữ liệu ứng dụng qua XAMPP |
| Front end | Blade, JavaScript, CSS | Giao diện render phía server |
| Asset | Vite 7, Tailwind CSS 4 | Build và theo dõi asset |
| UI | Bootstrap 5, Bootstrap Icons, Google Fonts | Thành phần giao diện hiện có |
| Local stack | XAMPP: Apache + MySQL | Web server và database cục bộ |
| Test | PHPUnit 11 | Kiểm thử Laravel |

### Kiến trúc & logic

```text
Browser → Apache/XAMPP → public/index.php → Laravel middleware/router
        → routes/web.php → Blade view → layouts/app.blade.php → HTML response

.env (DB_*) → config/database.php → Eloquent / migrations → MySQL
resources/css + resources/js → Vite → public/build
```

- `public/` là **web root duy nhất** của Apache; không trỏ đến thư mục gốc vì có thể lộ `.env` và mã nguồn.
- `routes/web.php` là entry point giao diện; route `/home` trả về `home.blade.php`.
- Layout chung chứa khung trang; view con đưa nội dung vào `@yield('content')`.
- Migration là nguồn chân lý cho database: mọi thay đổi schema dùng migration, không sửa thủ công nếu cần chia sẻ giữa máy.
- Cấu hình mặc định dùng database cho session, cache và queue; các bảng migration đi kèm cần tồn tại khi chạy MySQL.

### Yêu cầu

- XAMPP có **Apache** và **MySQL/MariaDB** đang chạy.
- PHP CLI 8.2+ với `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `xml`, `ctype`, `curl`.
- Composer 2.x, Node.js LTS, npm; Git là tùy chọn.
- Nên dùng PHP CLI cùng phiên bản PHP của XAMPP.

```powershell
php -v
composer --version
node -v
npm -v
```

### Cài đặt MySQL + XAMPP

1. Start **Apache** và **MySQL** trong XAMPP Control Panel.
2. Tạo database qua phpMyAdmin hoặc MySQL CLI:

```sql
CREATE DATABASE gearzone
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

3. Tại thư mục dự án:

```powershell
Copy-Item .env.example .env
composer install
npm install
```

4. Sửa `.env`: thay cấu hình SQLite bằng MySQL.

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
```

Đổi `DB_PORT` / `DB_PASSWORD` theo MySQL local của bạn. Không commit `.env`.

5. Khởi tạo ứng dụng, schema và asset:

```powershell
php artisan key:generate
php artisan migrate
npm run build
```

### Chạy qua Apache Virtual Host

Đặt source tại vị trí Apache có thể đọc (ví dụ `C:\\xampp\\htdocs\\DuAn2`). Trong `C:\\xampp\\apache\\conf\\extra\\httpd-vhosts.conf`, thêm:

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

Trong `C:\\xampp\\apache\\conf\\httpd.conf`, đảm bảo hai dòng sau đang bật (bỏ `#` nếu cần):

```apache
LoadModule rewrite_module modules/mod_rewrite.so
Include conf/extra/httpd-vhosts.conf
```

Thêm dòng này vào `C:\\Windows\\System32\\drivers\\etc\\hosts` với quyền quản trị:

```text
127.0.0.1 gearzone.test
```

Khởi động lại Apache và mở [http://gearzone.test/home](http://gearzone.test/home). Nếu không dùng virtual host, chạy `php artisan serve` và mở URL Laravel trả về.

### Workflow

```text
Migration → migrate → Model/Controller/Validation/Route → Blade UI
→ npm run dev/build → php artisan test
```

| Mục tiêu | Lệnh |
| --- | --- |
| Theo dõi asset khi phát triển | `npm run dev` |
| Build asset tối ưu | `npm run build` |
| Tạo migration | `php artisan make:migration create_products_table` |
| Chạy migration | `php artisan migrate` |
| Hoàn tác batch gần nhất | `php artisan migrate:rollback` |
| Tạo lại toàn bộ schema local | `php artisan migrate:fresh` |
| Test | `php artisan test` |
| Dọn cache | `php artisan optimize:clear` |

`php artisan migrate:fresh` xóa toàn bộ bảng của database đang cấu hình, chỉ dùng cho môi trường local có thể mất dữ liệu.

### Cấu trúc & quy ước

```text
app/                    Controllers, models và logic ứng dụng
database/migrations/    Lịch sử thay đổi schema MySQL
public/                 Apache web root và asset đã build
resources/views/        Blade layouts và trang giao diện
resources/css, js/      Asset nguồn cho Vite
routes/web.php          Route web
config/                 Cấu hình Laravel
tests/                  Unit và feature tests
```

- Validate request trước khi ghi dữ liệu; dùng Eloquent/query builder, không nối SQL từ input.
- Đặt nghiệp vụ trong controller/service/model, không nhồi logic vào Blade.
- Dùng UTF-8 cho file nguồn và `utf8mb4` cho MySQL để hiển thị tiếng Việt/Unicode chính xác.
- Chạy `php artisan test` trước khi chia sẻ thay đổi.

---

## English

### Overview

GearZone is a Laravel foundation for a gaming PC, laptop, and accessories website. Blade presentation is kept separate from routing, migrations version the database schema, and Vite manages front-end assets. The structure can grow into catalogue, cart, ordering, and administration features without replacing its foundation.

### Current scope

- A GearZone page at `/home` built from a shared header/content/footer layout.
- Laravel web routing and Blade inheritance: `layouts.app` → `home`.
- Infrastructure migrations for users, sessions, cache, jobs, job batches, and failed jobs.
- Vite + Tailwind CSS are configured; Bootstrap 5, Bootstrap Icons, and Google Fonts are loaded through the existing layout CDN.

> Product, News, Contact, search, and e-commerce links are UI placeholders. Their routes, models, controllers, and business rules have not been implemented yet.

### Stack

| Layer | Technology | Responsibility |
| --- | --- | --- |
| Back end | PHP 8.2+, Laravel 12 | HTTP lifecycle, routing, Blade, ORM, migrations |
| Database | MySQL / MariaDB | Application data through XAMPP |
| Front end | Blade, JavaScript, CSS | Server-rendered UI |
| Asset tooling | Vite 7, Tailwind CSS 4 | Build and dev asset pipeline |
| UI | Bootstrap 5, Bootstrap Icons, Google Fonts | Existing UI components |
| Local stack | XAMPP: Apache + MySQL | Local web server and database |
| Testing | PHPUnit 11 | Laravel testing |

### Architecture & execution logic

```text
Browser → Apache/XAMPP → public/index.php → Laravel middleware/router
        → routes/web.php → Blade view → layouts/app.blade.php → HTML response

.env (DB_*) → config/database.php → Eloquent / migrations → MySQL
resources/css + resources/js → Vite → public/build
```

- Apache must use `public/` as its only document root; never expose the project root.
- `routes/web.php` is the web entry point. `/home` returns `home.blade.php`.
- The shared layout owns the page shell; child views supply `@yield('content')`.
- Migrations are the schema source of truth. Version every database structure change.
- Sessions, cache, and queues are configured to use the database, so their migration tables are required with MySQL.

### Requirements & setup

Run Apache and MySQL/MariaDB in XAMPP. Install PHP CLI 8.2+ with standard Laravel/MySQL extensions, Composer 2.x, Node.js LTS, and npm. Using the same PHP CLI version as XAMPP is recommended.

1. Create the `gearzone` database with `utf8mb4` and `utf8mb4_unicode_ci`.
2. Copy `.env.example` to `.env`, then run `composer install` and `npm install`.
3. Set `APP_NAME="GearZone"`, `APP_URL=http://gearzone.test`, and the `DB_*` values to the MySQL configuration shown in the Vietnamese section.
4. Run:

```powershell
php artisan key:generate
php artisan migrate
npm run build
```

### Apache virtual host

Set Apache `DocumentRoot` to the project’s `public` directory; enable `mod_rewrite` and the virtual-host include; map `gearzone.test` to `127.0.0.1` in the Windows hosts file; restart Apache; then visit [http://gearzone.test/home](http://gearzone.test/home). A ready-to-copy configuration is in the Vietnamese section above.

For a quick fallback without Apache, run `php artisan serve`.

### Development workflow & safeguards

```text
Migration → migrate → Model/Controller/Validation/Route → Blade UI
→ npm run dev/build → php artisan test
```

- Use `npm run dev` during asset development and `npm run build` for optimized output.
- Use `php artisan migrate` for schema updates; use `php artisan migrate:rollback` to reverse the latest batch.
- `php artisan migrate:fresh` drops every table in the configured database. Use it only for disposable local data.
- Keep `.env` private, validate input, use Eloquent/query binding rather than concatenated SQL, and keep business rules out of Blade views.
- Keep source in UTF-8 and MySQL in `utf8mb4` for correct Vietnamese and Unicode text.

## License

No project-specific license is currently declared. Add one before distributing or reusing the project outside its intended team.
