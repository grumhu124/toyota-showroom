# Kiểm tra H01 — tài khoản

Ngày: 16/10/2026. Nhánh feature/auth-middleware.

## Đã kiểm

- tests/Feature/Auth/AuthenticationTest.php: 11 test chức năng.
- Toàn bộ suite local SQLite: 17 test / 135 assertions pass.
- Pint pass, npm run build pass; không đổi dependency/schema.
- GET form render Blade qua HTTP test; session, email normalize/unique,
  giả role, validation/array input, mật khẩu không flash vào session.
- Login sai lần 6 bị 429, hết 60 giây có thể thử lại, thành công xóa bộ đếm;
  nhiều lần đăng nhập thành công không bị tính là thất bại.
- Session ID đổi sau đăng ký/login; logout POST xóa dữ liệu session và đổi CSRF.
- Khóa user đã login: request công khai cũng thu hồi phiên, tránh guest redirect loop;
  request JSON/broadcast auth bị 403. Đây chưa là thu hồi socket đã mở.
- Customer/sales/admin kiểm role; chưa có Policy hồ sơ của module khác.

## Cách xem thử

Sau khi cài/cấu hình theo README, migrate/seed DB riêng và build:
1. Mở /dang-ky, thử thiếu dữ liệu/sai phone/mật khẩu xác nhận.
2. Tạo customer demo, kiểm /tai-khoan; đăng xuất rồi đăng nhập lại.
3. Dùng user demo local; đăng nhập sai 5 lần, lần tiếp theo bị 429.
4. Kiểm màn hình /dang-nhap, /dang-ky, /tai-khoan trên mobile và desktop.
5. Khi H02 hoàn thành, thử admin khóa user trong phiên khác.

## Giới hạn

Chưa kiểm trực quan bằng trình duyệt: công cụ mở browser lỗi môi trường.
Không coi build/HTTP tests là xác nhận layout responsive đã hoàn thiện.
Chưa có bằng chứng local MySQL trong vòng này; CI có matrix MySQL riêng.
Chưa có UI admin tạo sales/khóa-mở, tăng version khi khóa, tư vấn/chat/lịch
hoặc module dashboard. TV4 còn phải chặn tin mới trên socket/job khi khóa.
H01 có layout tài khoản tối thiểu riêng để Đạt thay giao diện mà giữ form contract.
