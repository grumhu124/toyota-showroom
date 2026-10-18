# Review và tích hợp H01 với giao diện — 18/10/2026

Nhánh feature/frontend-auth-integration từ frontend c35d682, ghép H01 668b65e
(PR #2, mang theo nền tài liệu PR #1). Không lấy H02/PR #4; chưa merge main.
PR #1/#2/#3 vẫn cần quy trình review/merge riêng của nhóm. Đây là bản tích hợp thử,
không thay việc thành viên khác phê duyệt và không tự gửi approval nhân danh Đạt.

## Rà soát mã

- Controllers/Auth, LoginRequest/RegisterRequest và PasswordInput giữ nguyên của Hoàng.
- Đăng ký dùng allowlist, role customer/active mặc định; không tin payload quyền.
- Login đổi session ID; logout invalidate + đổi CSRF; guest/auth/active middleware.
- Email chuẩn hóa, bcrypt byte/NUL validation, failure-only limiter; account view escape dữ liệu.
- Private chat vẫn từ chối mặc định. Khóa HTTP không đồng nghĩa thu hồi socket hoàn chỉnh.
- Không thấy lỗi chặn tích hợp H01 trong phạm vi đã đọc; đây không là chứng nhận an toàn toàn hệ thống.

## Xử lý điểm giao nhau

- routes/web.php giữ home và catalog preview của Đạt, thêm Auth routes/name từ Hoàng.
- Header khách nối login thật; đã đăng nhập nối account và form POST logout có CSRF.
- Giữ trang chủ, hai ảnh, video/timeline và card. Không thiết kế lại các form Auth.
- Giữ contracts/status của H01 đồng thời ghi rõ fixture frontend chưa nối catalog backend.
- Inventory dùng format 2 từ nhánh nền; regenerate sau resolve, không chọn hash một phía.
- Không đổi migration, schema, dependency, .env hoặc dữ liệu MySQL trên máy.

## Kiểm tra

Suite H01 và các test frontend cũ được chạy cùng FrontendIntegrationTest:
guest từ home/catalog/detail đến Auth; ba role trở lại frontend; login/logout;
POST logout thiếu CSRF phải 419, token đúng hợp lệ (tắt bypass bằng env local).
Test dùng SQLite :memory: và session array, không reset database demo.
Kết quả: composer check đạt 27 tests/263 assertions và Pint; npm run build đạt.
route:list có home, hai route Catalog preview và sáu route Auth/account; diff check đạt.
Build còn cảnh báo đường dẫn font tĩnh: được giữ để tải từ public/assets/fonts lúc chạy.
Chưa thử toàn bộ trên thiết bị thật hoặc local MySQL; H02/chat không thuộc bản tích hợp.
