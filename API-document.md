# Tài liệu API — f_travel_BE (Laravel)

Phiên bản tài liệu phản ánh code hiện tại trong dự án. Mọi endpoint REST nằm dưới tiền tố **`/api`** (mặc định Laravel).

**Base URL ví dụ (môi trường local):** `http://127.0.0.1:8000/api`

**Định dạng:** JSON, UTF-8.

**Xác thực:** Hiện **không** yêu cầu header token; các route đều public (đọc dữ liệu).

**CORS:** Cấu hình mặc định cho phép gọi từ web (ví dụ Flutter Web) tới các đường dẫn `api/*`.

---

## Danh sách endpoint

| Phương thức | Đường dẫn | Mô tả |
|-------------|-----------|--------|
| `GET` | `/home` | Dữ liệu trang chủ: hero, tour khuyến mãi, gợi ý tour, điểm đến (highlights) |
| `GET` | `/tours` | Danh sách tour (có query `scope`) |
| `GET` | `/tours/{slug}` | Chi tiết một tour (theo `slug` hoặc `id` số) |
| `GET` | `/coupons` | Danh sách voucher/coupon public |
| `POST` | `/auth/login` | Đăng nhập (Sanctum token) |
| `POST` | `/auth/register` | Đăng ký (Sanctum token) |
| `GET` | `/auth/me` | Lấy thông tin user hiện tại |
| `PUT` | `/auth/profile` | Cập nhật hồ sơ |
| `DELETE` | `/auth/account` | Xoá tài khoản |
| `POST` | `/auth/logout` | Đăng xuất |
| `POST` | `/coupons/collect` | Thu thập coupon |
| `GET` | `/coupons/my` | Coupon của tôi |
| `POST` | `/bookings/checkout` | Tạo booking/checkout |
| `GET` | `/bookings/my` | Booking của tôi |
| `POST` | `/bookings/payments/{payment}/confirm` | Xác nhận thanh toán |
| `POST` | `/bookings/payments/{payment}/cancel` | Huỷ thanh toán |
| `GET` | `/bookings/payments/{payment}/vietqr` | Lấy dữ liệu VietQR |
| `GET` | `/admin/categories` | (Admin) danh sách danh mục |
| `GET` | `/admin/tours` | (Admin) danh sách tour |
| `POST` | `/admin/tours` | (Admin) tạo tour |
| `PUT` | `/admin/tours/{tour}` | (Admin) cập nhật tour |
| `DELETE` | `/admin/tours/{tour}` | (Admin) xoá tour |
| `GET` | `/admin/users` | (Admin) danh sách user |
| `PUT` | `/admin/users/{user}` | (Admin) cập nhật user |
| `DELETE` | `/admin/users/{user}` | (Admin) xoá user |
| `GET` | `/admin/coupons` | (Admin) danh sách coupon |
| `POST` | `/admin/coupons` | (Admin) tạo coupon |
| `PUT` | `/admin/coupons/{coupon}` | (Admin) cập nhật coupon |
| `DELETE` | `/admin/coupons/{coupon}` | (Admin) xoá coupon |
| `GET` | `/admin/reviews` | (Admin) danh sách review |
| `PUT` | `/admin/reviews/{review}` | (Admin) duyệt/cập nhật review |
| `GET` | `/admin/roles` | (Admin) danh sách role |
| `POST` | `/admin/roles` | (Admin) tạo role |
| `PUT` | `/admin/roles/{role}` | (Admin) cập nhật role |
| `DELETE` | `/admin/roles/{role}` | (Admin) xoá role |
| `GET` | `/admin/reports/dashboard` | (Admin) số liệu dashboard |
| `GET` | `/admin/reports/statistics` | (Admin) thống kê |
| `GET` | `/admin/reports/export/bookings-csv` | (Admin) xuất CSV |
| `GET` | `/admin/reports/export/revenue-pdf` | (Admin) xuất PDF |

Dưới đây là mô tả chi tiết từng API.

---

## 1. GET `/api/home`

Trả về nội dung phục vụ trang chủ: thương hiệu, hero, danh sách tour **khuyến mãi** (có giảm giá so với giá gốc), và các **category** dùng làm “điểm đến nổi bật”.

### Logic nghiệp vụ (tóm tắt)

- **promo_tours:** Lấy từ bảng `tours`, `status = active`, có `discount_price` và `discount_price < price`, sắp xếp theo mức giảm giảm dần, tối đa **6** bản ghi.
- **highlights:** Lấy `categories` có `slug` thuộc `domestic`, `europe`, `asia`, `international`, thứ tự cố định: domestic → europe → asia → international.
- **hero.background_image_url:** Ảnh banner `banners` có `placement = hero` nếu có; không thì dùng URL mặc định trong code.

### Response 200 — cấu trúc

```json
{
  "brand_name": "Ftravel",
  "hero": {
    "title_line1": "string",
    "title_line2": "string",
    "subtitle": "string",
    "background_image_url": "string (URL ảnh)"
  },
  "promo_tours": [
    {
      "slug": "string | null",
      "title": "string",
      "duration": "string",
      "image_url": "string",
      "badge": "string | null",
      "remaining_label": "string | null",
      "old_price": "string",
      "new_price": "string"
    }
  ],
  "suggested_tours": [
    {
      "slug": "string | null",
      "title": "string",
      "duration": "string",
      "image_url": "string",
      "badge": "string | null",
      "remaining_label": "string | null",
      "old_price": "string",
      "new_price": "string"
    }
  ],
  "highlights": [
    {
      "title": "string",
      "subtitle": "string",
      "image_url": "string",
      "is_large": true,
      "pill_label": "string | null"
    }
  ]
}
```

### Ý nghĩa một số trường

| Trường | Mô tả |
|--------|--------|
| `promo_tours[].duration` | Chuỗi dạng `"X Ngày Y Đêm"` tính từ `duration` (ngày) trên tour. |
| `promo_tours[].badge` | Ví dụ `"-25% OFF"` khi có giảm giá; có thể `null`. |
| `promo_tours[].remaining_label` | Ví dụ `"05 chỗ"` từ `max_people`; có thể `null`. |
| `promo_tours[].old_price` / `new_price` | Chuỗi định dạng tiền VNĐ (dấu chấm ngăn cách nghìn, kết thúc bằng `đ`). |
| `highlights[].is_large` | `true` với slug `domestic` và `international` (layout ô lớn trên UI). |
| `highlights[].pill_label` | Hiện chỉ gán `"Nội địa"` khi category là `domestic`; các ô khác có thể `null`. |

### Lỗi

- Không định nghĩa mã lỗi đặc biệt; lỗi server trả 5xx theo Laravel.

---

## 2. GET `/api/tours`

Trả về danh sách tour đang **active**, kèm thông tin category (load quan hệ phía server).

### Query parameters

| Tham số | Kiểu | Mặc định | Mô tả |
|---------|------|----------|--------|
| `scope` | string | `all` | `all` — tất cả tour active; `domestic` — tour thuộc category `slug = domestic`; `international` — `slug = international`. |

Ví dụ:

- `GET /api/tours?scope=domestic`
- `GET /api/tours?scope=international`
- `GET /api/tours` hoặc `GET /api/tours?scope=all`

### Response 200

```json
{
  "data": [
    {
      "id": 0,
      "slug": "string | null",
      "name": "string | null",
      "thumbnail": "string | null",
      "duration": 0,
      "duration_label": "string",
      "rating": 0.0,
      "price": 0.0,
      "discount_price": 0.0,
      "price_from": "string",
      "badge_label": "string | null",
      "badge_variant": "string | null",
      "meta_icon1": "string",
      "meta_text1": "string",
      "meta_icon2": "string",
      "meta_text2": "string"
    }
  ]
}
```

### Ý nghĩa trường (card tour)

| Trường | Mô tả |
|--------|--------|
| `duration` | Số ngày (integer), nguồn cột `tours.duration`. |
| `duration_label` | Chuỗi dạng `XN YĐ` (ví dụ `4N3Đ`). |
| `price` | Giá niêm yết (số). |
| `discount_price` | Giá sau giảm; có thể `null`. |
| `price_from` | Chuỗi hiển thị giá dùng cho UI (ưu tiên giá khuyến mãi nếu có). |
| `badge_variant` | Gợi ý UI: ví dụ `hot`, `bestseller`, `newest` (không cố định trong spec, theo dữ liệu seed). |
| `meta_icon1` / `meta_icon2` | Tên gợi ý icon phía client (ví dụ `flight`, `hotel`, `directions_bus`); client tự map sang icon. |

---

## 3. GET `/api/tours/{slug}`

Trả về **một** tour: đầy đủ thông tin card + mô tả, điểm khởi hành, gallery, lịch trình.

### Tham số đường dẫn `{slug}`

- **Theo slug:** ví dụ `hanh-trinh-di-san-mien-bac` — khớp cột `tours.slug`.
- **Theo id:** nếu toàn bộ `{slug}` là **chữ số** (ví dụ `12`), server tìm theo `tours.id`.

### Response 200

Gồm toàn bộ trường như object trong `GET /api/tours` (một phần tử `data`), **cộng thêm**:

```json
{
  "data": {
    "description": "string | null",
    "start_location": "string | null",
    "max_people": 0,
    "category": {
      "id": 0,
      "name": "string | null",
      "slug": "string | null"
    },
    "gallery": ["string"],
    "itineraries": [
      {
        "day_number": 0,
        "title": "string | null",
        "description": "string | null"
      }
    ]
  }
}
```

| Trường | Mô tả |
|--------|--------|
| `gallery` | Mảng URL ảnh từ bảng `tour_images.image_url` (có thể rỗng). |
| `itineraries` | Các ngày trong lịch trình (`itineraries`); có thể rỗng. |
| `category` | `null` nếu tour không gán category. |

### Response 404

Khi không tìm thấy tour:

```json
{
  "message": "Không tìm thấy tour"
}
```

---

## Ghi chú kỹ thuật

- Tiền tố route API mặc định là **`api`** → URL đầy đủ: `{APP_URL}/api/...`.
- Ứng dụng Flutter trong repo dùng biến build **`API_BASE_URL`** (mặc định `http://127.0.0.1:8000/api`) — xem `f_travel/lib/app/core/config/app_config.dart`.
- Dữ liệu mẫu: `php artisan migrate --seed` và seeder `FTravelSeeder`.
- Xác thực API dùng **Laravel Sanctum**:
  - Đăng nhập/đăng ký trả về token (Bearer).
  - Các endpoint trong nhóm `auth:sanctum` yêu cầu header:

```http
Authorization: Bearer <token>
Accept: application/json
```

---

## 4. GET `/api/coupons`

Trả về danh sách coupon/voucher public (không cần đăng nhập).

---

## 5. Auth (Sanctum)

### 5.1 POST `/api/auth/login`

Body (JSON):

```json
{
  "email": "test@gmail.com",
  "password": "12345678"
}
```

### 5.2 POST `/api/auth/register`

Body (JSON):

```json
{
  "name": "Nguyễn Văn A",
  "email": "a@example.com",
  "password": "12345678",
  "password_confirmation": "12345678",
  "phone": "0123456789"
}
```

### 5.3 GET `/api/auth/me`

Trả về thông tin user hiện tại (yêu cầu Bearer token).

---

## 6. Booking/Checkout

Các endpoint trong nhóm booking yêu cầu đăng nhập (Bearer token):

- `POST /api/bookings/checkout`
- `GET /api/bookings/my`
- `POST /api/bookings/payments/{payment}/confirm`
- `POST /api/bookings/payments/{payment}/cancel`
- `GET /api/bookings/payments/{payment}/vietqr`

---

## 7. Admin API

Nhóm `/api/admin/*` yêu cầu:

- `auth:sanctum`
- middleware `admin`

Các endpoint: categories/tours/users/coupons/reviews/roles/reports như bảng ở trên.

---

## Liên hệ / mở rộng

Các API mới (đăng nhập, đặt tour, thanh toán, v.v.) nên bổ sung vào tài liệu này khi được triển khai, kèm method, path, body, mã HTTP và ví dụ JSON.
