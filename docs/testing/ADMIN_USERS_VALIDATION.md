# H02 — kiểm tra admin quản lý tài khoản

17/10/2026, nhánh feature/admin-users dựa trên feature/auth-middleware.
AdminUsersTest: 8 test; toàn suite local SQLite 28 tests/222 assertions pass.
Pint pass. Không thay schema; không cần migration mới cho H02.

Các ca đã chạy:
- Guest/customer/sales bị chặn ở cả bốn endpoint admin.
- Admin tạo sales bất kể payload cố đẩy role admin/is_active; giữ session admin.
- Email trùng, phone/password sai bị validate, mật khẩu không flash.
- Danh sách search tên/email, role, pagination 15/trang, empty state, input sai.
- Khóa sales hoặc customer tăng version chỉ inquiry liên quan, không mất assignment.
- Gửi lại trạng thái khóa không bump lần nữa; mở lại giữ version mới.
- Không khóa admin, không xóa tài khoản, không nhận status text sai.
- Service đọc lại role/active trong transaction; mô phỏng lỗi ghi version rollback
  cả thay đổi trạng thái user và version để không dở dang.

Cách thử sau migrate/seed/build trên DB riêng: admin login → /admin/users →
tạo sales → tìm/lọc → khóa/mở một customer/sales. Phiên target sẽ bị chặn ở
request tiếp theo. Không nhận khóa HTTP/version là bằng chứng socket đã thu hồi.

Giới hạn: chưa kiểm trực quan responsive H02, chưa test hai kết nối cạnh tranh.
CI MySQL chạy suite tuần tự không chứng minh race/deadlock hay pubsub thu hồi tin.
H02 không gửi thông báo/mật khẩu qua email, không bổ sung audit UI/P2.
FE Đạt có thể đổi layout/style, giữ contract form/name route/CSRF và Policy.
