# f_travel_BE — Ftravel Backend (Laravel) + Web (FE2)

Hệ thống **backend + API + web Laravel (FE2)** cho dự án Ftravel.

- **FE1 (Flutter)** gọi **REST API** dưới tiền tố `/api`
- **FE2 (Web Laravel)** cung cấp giao diện người dùng + trang quản trị (admin)

---

## Mục lục

- [Thông tin nhóm](#thông-tin-nhóm)
- [Tính năng chính](#tính-năng-chính-đã-có-trong-code)
- [Công nghệ sử dụng](#công-nghệ-sử-dụng)
- [Cấu trúc thư mục](#cấu-trúc-thư-mục-rút-gọn)
- [Yêu cầu môi trường](#yêu-cầu-môi-trường)
- [Cài đặt nhanh (Local)](#cài-đặt-nhanh-local)
- [Tài khoản demo (Seeder)](#tài-khoản-demo-seeder)
- [Điền sẵn tài khoản khi test (Web login)](#điền-sẵn-tài-khoản-khi-test-web-login)
- [Tài liệu API](#tài-liệu-api)
- [Các script hữu ích](#các-script-hữu-ích)
- [License](#license)

---

## Thông tin nhóm


| STT | Họ và Tên          | MSSV     | Vị Trí      |
| --- | ------------------ | -------- | ----------- |
| 1   | Nguyễn Anh Quân    | 20220839 | Trưởng Nhóm |
| 2   | Nguyễn Văn Vũ      | 20220844 | Thành Viên  |
| 3   | Nguyễn Sỹ Quang    | 20220744 | Thành Viên  |
| 4   | Nguyễn Thị An Bình | 20220997 | Thành Viên  |
| 5   | Hoàng Minh Duy     | 20220794 | Thành Viên  |


---

## Tính năng chính (đã có trong code)

### User (Web + API)

- **Trang chủ**: hero + tìm kiếm, gợi ý tour, ưu đãi nổi bật, điểm đến, vì sao chọn Ftravel
- **Tour**: danh sách tour (lọc theo phạm vi/nội dung/ngày), chi tiết tour, gallery, lịch trình
- **Voucher/Coupon**: xem coupon public, thu thập coupon, xem coupon của tôi (API)
- **Đăng ký/Đăng nhập/Đăng xuất**: Web form + API (Sanctum)
- **Hồ sơ cá nhân**: cập nhật hồ sơ, xoá tài khoản (API), cập nhật thông tin (Web)
- **Booking/Checkout**: tạo đơn, xem đơn của tôi, thao tác thanh toán + VietQR (API)

### Admin (Web + API)

- **Quản lý Tour**: CRUD
- **Quản lý User**: cập nhật/xoá
- **Quản lý Coupon**: CRUD
- **Quản lý Role/Permission**: CRUD role, seed permission mặc định
- **Quản lý Review**: duyệt/cập nhật
- **Báo cáo**: dashboard/statistics, export CSV/PDF

---

## Công nghệ sử dụng

- **PHP**: 8.2+
- **Laravel**: 12.x
- **Auth API**: Laravel Sanctum (Bearer token)
- **DB**: **MySQL** (theo `.env` hiện tại của dự án)
- **UI (Web FE2)**:
  - CSS hiện tại: `public/css/web/`* (tokens/material-like)
  - Icon: Bootstrap Icons (CDN)
  - JS hiện tại: `public/js/web/app.js` (slider, hero search...)

---

## Cấu trúc thư mục (rút gọn)

- **API routes**: `routes/api.php`
- **Web routes**: `routes/web.php`
- **Controllers**: `app/Http/Controllers/`*
- **Views (Blade)**: `resources/views/web/`*, `resources/views/admin/*`
- **Static assets**:
  - CSS: `public/css/web/`*
  - JS: `public/js/web/app.js`
- **Seeder dữ liệu demo**: `database/seeders/FTravelSeeder.php`
- **Tài liệu API**: `API-document.md`

---

## Yêu cầu môi trường

- PHP 8.2+
- Composer

---

## Cài đặt nhanh (Local)

Trong thư mục `f_travel_BE`:

### 1) Cài dependencies

```bash
composer install
```

### 2) Tạo file `.env`

```bash
copy .env.example .env
php artisan key:generate
```

### 3) Chuẩn bị database

Mặc định dùng **MySQL** theo `.env` hiện tại.

```bash
php artisan migrate --seed
```

Seeder sẽ tạo dữ liệu demo (tour/category/banner/coupon/booking mẫu tuỳ seed).

### 4) Chạy server

```bash
php artisan serve
```

Mặc định: `http://127.0.0.1:8000`

---

## Tài khoản demo (Seeder)

### User demo

- **Email**: `test@gmail.com`
- **Mật khẩu**: `12345678`

### Admin demo

- **Email**: `admin@ftravel.test`
- **Mật khẩu**: `123456`

---

## Điền sẵn tài khoản khi test (Web login)

Trong `.env` bạn có thể set để form đăng nhập web tự điền (giống FE1):

```env
DEMO_LOGIN_EMAIL=test@gmail.com
DEMO_LOGIN_PASSWORD=12345678
```

Production nên để trống 2 biến này.

---

## Tài liệu API

Xem chi tiết tại:

- `API-document.md`

Base URL local:

- `http://127.0.0.1:8000/api`

Một số endpoint yêu cầu Bearer token (Sanctum), ví dụ:

```http
Authorization: Bearer <token>
Accept: application/json
```

---

## Các script hữu ích

### Composer scripts

- `composer run test`: chạy test

### Artisan

- `php artisan optimize:clear`: xoá cache config/routes/views

---

## License

Dự án phục vụ mục đích học tập/nội bộ. Nếu cần công bố mã nguồn, hãy liên hệ 0962784293.