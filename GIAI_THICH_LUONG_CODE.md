# Giải thích luồng code — Website Quản lý Nhà cung cấp

Tài liệu này dùng để thuyết trình hoặc trả lời khi giảng viên hỏi về cách chương trình hoạt động.

## 1. Mục đích hệ thống

Website hỗ trợ quản lý thông tin nhà cung cấp: xem danh sách, tìm kiếm, lọc theo trạng thái, thêm, sửa và xóa dữ liệu. Dữ liệu được lưu trong MySQL; phần giao diện và xử lý được viết bằng PHP thuần, không dùng framework.

## 2. Cấu trúc thư mục

```text
NCC/
├── assets/
│   ├── style.css             # Toàn bộ CSS giao diện
│   └── app.js                # Xác nhận trước khi xóa
├── config/
│   ├── auth.php              # Session, kiểm tra đăng nhập, thông báo
│   └── database.php          # Kết nối MySQL bằng PDO
├── partials/
│   ├── header.php            # Menu trái, tiêu đề trang
│   └── footer.php            # Đóng HTML, nạp JavaScript
├── database.sql              # Tạo CSDL, bảng và dữ liệu mẫu
├── login.php                 # Trang đăng nhập
├── logout.php                # Đăng xuất
├── index.php                 # Dashboard
├── suppliers.php             # Danh sách, tìm kiếm, lọc NCC
├── supplier_form.php         # Form thêm và sửa NCC
└── delete_supplier.php       # Xóa NCC
```

## 3. Cơ sở dữ liệu

Trong file `database.sql`, chương trình tạo CSDL tên `quan_ly_nha_cung_cap` và bảng `suppliers`.

| Trường | Ý nghĩa |
|---|---|
| `id` | Khóa chính, tự tăng, xác định duy nhất mỗi bản ghi |
| `code` | Mã nhà cung cấp, có ràng buộc `UNIQUE` để không bị trùng |
| `name` | Tên nhà cung cấp |
| `contact_name` | Người liên hệ |
| `phone`, `email`, `address` | Thông tin liên lạc |
| `category` | Nhóm ngành hàng |
| `tax_code` | Mã số thuế |
| `status` | Trạng thái: `active`, `pause` hoặc `stop` |
| `created_at` | Thời điểm tạo bản ghi |

Khóa chính là `id`. Khi cần sửa hoặc xóa, chương trình nhận `id` của bản ghi cần thao tác.

## 4. Luồng tổng quát của website

```text
Người dùng mở website
        ↓
login.php kiểm tra tài khoản
        ↓
Lưu thông tin đăng nhập vào $_SESSION
        ↓
Chuyển đến index.php (Dashboard)
        ↓
Người dùng chọn xem / thêm / sửa / xóa nhà cung cấp
        ↓
PHP kết nối MySQL, xử lý yêu cầu và trả giao diện HTML
```

`$_SESSION` là vùng nhớ tạm của PHP theo từng người dùng. Khi đăng nhập đúng, chương trình lưu `$_SESSION['user']`; các trang chức năng dựa vào biến này để biết người dùng đã đăng nhập chưa.

## 5. Luồng đăng nhập

File xử lý: `login.php`.

1. Khi người dùng mở trang, nếu `$_SESSION['user']` đã tồn tại thì chuyển thẳng đến `index.php`.
2. Người dùng nhập tên đăng nhập và mật khẩu, rồi gửi form bằng phương thức `POST`.
3. PHP lấy `$_POST['username']` và `$_POST['password']` để kiểm tra.
4. Với bản demo, tài khoản hợp lệ là `admin` / `admin123`.
5. Nếu đúng, PHP lưu tên người dùng vào `$_SESSION['user']`, sau đó dùng `header('Location: index.php')` để chuyển trang.
6. Nếu sai, trang hiện thông báo lỗi.

> Khi triển khai thực tế, tài khoản nên được lưu trong một bảng `users`; mật khẩu cần được mã hóa bằng `password_hash()` và kiểm tra bằng `password_verify()`. Bài này dùng tài khoản cứng để đơn giản hóa việc demo.

## 6. Kiểm tra đăng nhập ở các trang

File `config/auth.php` có hàm `require_login()`.

```php
function require_login(): void
{
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}
```

Mỗi trang chức năng gọi hàm này trước khi xử lý. Nếu chưa đăng nhập, người dùng không được xem dữ liệu mà bị chuyển về trang đăng nhập. Đây là cách bảo vệ cơ bản bằng session.

## 7. Luồng kết nối MySQL

File `config/database.php` chứa thông số máy chủ như `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.

Hàm `db()` tạo và trả về một đối tượng PDO:

```php
$pdo = new PDO($dsn, DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
```

- `PDO` là lớp PHP để làm việc với cơ sở dữ liệu.
- `ERRMODE_EXCEPTION` giúp PHP báo lỗi dưới dạng ngoại lệ để có thể xử lý lỗi.
- `FETCH_ASSOC` giúp mỗi dòng dữ liệu trả về ở dạng mảng có khóa là tên cột, ví dụ `$supplier['name']`.
- Biến `static $pdo` giúp trong cùng một lần tải trang chỉ dùng một kết nối.

## 8. Luồng Dashboard

File xử lý: `index.php`.

Sau khi gọi `require_login()`, trang thực hiện các câu lệnh `SELECT` để lấy số liệu:

```text
COUNT(*)                         → tổng nhà cung cấp
COUNT(*) WHERE status='active'   → số đang hợp tác
COUNT(DISTINCT category)         → số nhóm ngành hàng
SELECT ... ORDER BY created_at   → 5 nhà cung cấp gần đây
GROUP BY category                → số NCC của từng ngành hàng
```

Kết quả được đưa vào các thẻ thống kê và bảng “Nhà cung cấp mới cập nhật”. Phần “Phân bố ngành hàng” dùng `GROUP BY category` để gom các nhà cung cấp cùng ngành hàng, sau đó tính tỉ lệ thanh biểu đồ:

```php
round($row['total'] / $total * 100)
```

## 9. Luồng xem, tìm kiếm và lọc

File xử lý: `suppliers.php`.

1. Trang nhận dữ liệu từ URL bằng `$_GET['q']` (từ khóa) và `$_GET['status']` (trạng thái).
2. Nếu có từ khóa, câu SQL thêm điều kiện tìm trong mã, tên, người liên hệ hoặc số điện thoại.
3. Nếu có trạng thái hợp lệ, câu SQL thêm điều kiện `status = ?`.
4. Câu lệnh được chạy bằng `prepare()` và `execute($params)`.
5. Dữ liệu lấy từ MySQL được lặp bằng `foreach` để tạo các dòng bảng HTML.

Ví dụ phần tìm kiếm dùng `LIKE`:

```php
$sql .= ' AND (code LIKE ? OR name LIKE ? OR contact_name LIKE ? OR phone LIKE ?)';
$like = "%$keyword%";
$params = [$like, $like, $like, $like];
```

Dấu `%` nghĩa là có thể có bất kỳ ký tự nào trước hoặc sau từ khóa. Ví dụ tìm `An` có thể ra “An Phú”.

## 10. Luồng thêm nhà cung cấp (Create)

File xử lý: `supplier_form.php`, khi URL không có `id`.

1. Người dùng điền form và nhấn nút **Thêm nhà cung cấp**.
2. Form gửi dữ liệu bằng `POST` về chính `supplier_form.php`.
3. PHP kiểm tra các trường bắt buộc: mã, tên, người liên hệ, điện thoại, nhóm ngành hàng.
4. PHP kiểm tra định dạng email và trạng thái.
5. Nếu hợp lệ, chương trình dùng câu lệnh `INSERT INTO suppliers (...) VALUES (...)` để thêm bản ghi.
6. Thêm thành công, chương trình lưu thông báo vào session và chuyển về `suppliers.php`.

## 11. Luồng sửa nhà cung cấp (Update)

File xử lý: `supplier_form.php?id=...`.

1. Ở danh sách, nhấn **Sửa**. URL mang theo `id` của nhà cung cấp.
2. PHP dùng `SELECT * FROM suppliers WHERE id = ?` để lấy dữ liệu cũ và điền sẵn vào form.
3. Người dùng thay đổi thông tin, rồi gửi form bằng `POST`.
4. Sau khi kiểm tra dữ liệu, PHP tạo câu lệnh `UPDATE suppliers SET ... WHERE id = ?`.
5. Kết thúc, chương trình chuyển về danh sách và hiện thông báo “Đã cập nhật nhà cung cấp”.

## 12. Luồng xóa nhà cung cấp (Delete)

File xử lý: `delete_supplier.php`.

1. Người dùng nhấn nút **Xóa** tại bảng danh sách.
2. JavaScript trong `assets/app.js` hiển thị hộp xác nhận `confirm()`.
3. Nếu đồng ý, trình duyệt gửi `id` đến `delete_supplier.php`.
4. PHP kiểm tra `id` có phải số nguyên bằng `filter_input()`.
5. PHP thực hiện `DELETE FROM suppliers WHERE id = ?`.
6. Trang chuyển về danh sách và hiện thông báo kết quả.

## 13. Vì sao dùng `prepare()` và `execute()`?

Ví dụ:

```php
$stmt = $pdo->prepare('DELETE FROM suppliers WHERE id = ?');
$stmt->execute([$id]);
```

Đây là **prepared statement**. Dấu `?` là vị trí chờ dữ liệu; dữ liệu thật (`$id`) được gửi riêng qua `execute()`. Cách này giúp hạn chế SQL Injection — kiểu tấn công chèn câu lệnh SQL vào dữ liệu người dùng nhập.

## 14. Các phần bảo vệ dữ liệu cơ bản

- `require_login()` chặn truy cập trang quản trị khi chưa đăng nhập.
- `filter_input(..., FILTER_VALIDATE_INT)` chỉ nhận `id` là số nguyên.
- `filter_var(..., FILTER_VALIDATE_EMAIL)` kiểm tra định dạng email.
- `htmlspecialchars()` được đóng gói trong hàm `e()` để hiển thị dữ liệu an toàn trên HTML, hạn chế XSS.
- `prepare()` và `execute()` hạn chế SQL Injection.
- Cột `code` có `UNIQUE`; mã nhà cung cấp không thể bị trùng.

## 15. Cách giải thích ngắn khi demo

> “Hệ thống được xây bằng PHP thuần và MySQL. Người dùng đăng nhập thì PHP lưu session. Mỗi trang nghiệp vụ kiểm tra session trước khi cho truy cập. File database.php dùng PDO kết nối MySQL. Trang danh sách nhận dữ liệu tìm kiếm từ GET, còn form thêm/sửa nhận dữ liệu từ POST. Tất cả thao tác với CSDL đều dùng prepared statement. Sau khi thêm, sửa hoặc xóa, hệ thống chuyển về danh sách và hiển thị thông báo bằng session.”

## 16. Câu hỏi thường gặp khi bảo vệ bài

**Hỏi: Tại sao `id` lại để tự tăng?**  
Đáp: Vì `id` là khóa chính dùng để định danh duy nhất bản ghi. MySQL tự sinh giá trị mới nên không cần người dùng nhập và không sợ trùng.

**Hỏi: Tại sao có `code` khi đã có `id`?**  
Đáp: `id` phục vụ kỹ thuật và liên kết dữ liệu; `code` là mã nghiệp vụ dễ nhìn, ví dụ NCC001, để người dùng tra cứu. `code` cũng được đặt `UNIQUE`.

**Hỏi: GET và POST khác nhau thế nào?**  
Đáp: GET đưa dữ liệu trên URL, phù hợp tìm kiếm/lọc để có thể sao chép đường dẫn. POST gửi dữ liệu trong phần thân yêu cầu, phù hợp thêm/sửa dữ liệu.

**Hỏi: Tại sao sau khi lưu lại phải redirect?**  
Đáp: Để tránh việc người dùng bấm F5 làm gửi lại form và tạo/sửa dữ liệu thêm lần nữa. Đây là mô hình Post/Redirect/Get.

**Hỏi: Có thể mở rộng hệ thống thế nào?**  
Đáp: Có thể thêm bảng tài khoản, đơn nhập hàng, sản phẩm, hợp đồng; tạo quan hệ khóa ngoại giữa đơn nhập hàng và nhà cung cấp; phân quyền người dùng; xuất Excel/PDF và biểu đồ doanh thu.
