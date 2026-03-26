# MVC Sinh Viên

## Các chức năng đã làm
- MVC đúng cấu trúc `controller/` + `model/` + `view/`
- Hiển thị danh sách sinh viên (ID, Họ tên, Ngành học)
- CRUD: Thêm / Sửa / Xóa
- Routing theo `action` trong `index.php`:
  - `?action=list`
  - `?action=add`
  - `?action=edit&id=1`
  - `?action=delete&id=1`
- Validation:
  - Không để trống dữ liệu
  - Tên **>= 3 ký tự**
  - Hiển thị thông báo lỗi trên giao diện

## Phần nâng cao (JSON “database”)
- Lưu dữ liệu bằng JSON (mỗi file JSON là 1 “bảng”):
  - `data/students.json`
- Dữ liệu mẫu: **5 sinh viên** có sẵn trong `data/students.json`
- Tìm kiếm theo tên: tham số `q` (ví dụ `?action=list&q=an`)
- Phân trang: **5 sinh viên / trang**

