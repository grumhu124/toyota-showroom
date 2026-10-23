# Trạng thái thực tế

23/10: customer có giao diện tài khoản riêng tại /tai-khoan; chat preview local/testing tại /tai-khoan/tin-nhan dùng fixture, chưa có gửi/nhận WebSocket hoặc lịch sử thật. Admin/sales giữ màn tài khoản H01/H02. Xem docs/development/CUSTOMER_ACCOUNT_CHAT_UI.md.

Ngày 19/10/2026. Đã gộp vào main: docs/review-v6-srs, H01 (feature/auth-middleware),
H02 (feature/admin-users), frontend home/catalog preview (feature/frontend-layout) và
tích hợp auth vào giao diện frontend (feature/frontend-auth-integration). H03 mới có
hợp đồng quyền/gói review. Chưa có nghiệp vụ tư vấn/lịch/chat hoàn chỉnh.

Trang chủ tối, video/canvas, hai ảnh chi tiết, /xe slider/card/lọc và /xe/{id} ID 1–9
vẫn dùng fixture local/testing (chỉ chạy ở local/testing). Header đã nối Auth thật,
form đăng nhập/đăng ký dùng ảnh xe chạy và nền tối; backend H01/H02 giữ nguyên.
Tư vấn/đặt lịch/chat chưa nối backend.

| Module | Đã có | P1 còn làm | Chủ trách nhiệm |
| --- | --- | --- | --- |
| Framework | Laravel/lockfile/Vite/Tailwind/CI | Cài/demo máy khác | Hoàng + nhóm |
| Dữ liệu | 8 bảng core + audit, enum/model | Seed Toyota; giữ brand=Toyota, internal_note | Võ Nguyễn Thành Đạt (TV3)/Phan Võ Chí Bảo (TV5) + Hoàng |
| Auth | H01; H02 UserPolicy, admin list/search/pagination/tạo sales/khóa-mở, transaction tăng version khi khóa; test HTTP pass | Review/kiểm trực quan H02, tích hợp thu hồi socket/job với TV4 | Hoàng |
| Catalog | Schema/model/thư mục | CRUD/ảnh/search/filter/pagination/UI | Võ Nguyễn Thành Đạt (TV3) + Đạt |
| Tư vấn | Inquiry model/enum | Form AJAX, phân công/trạng thái/internal_note/quyền | Nguyễn Huy Hoàng (TV4) + Đạt |
| Chat | Reverb/Echo cấu hình, private channel từ chối mặc định | Gửi/lưu/phát, lịch sử, retry/catch-up, thu hồi quyền, UI hai phiên | Nguyễn Huy Hoàng (TV4) + Đạt |
| Lịch/dịch vụ | Schema/model/enum | Form, phân công, xác nhận khóa/trùng giờ, hủy/hoàn tất/no_show | Phan Võ Chí Bảo (TV5) |
| Audit | Bảng/model/namespace | Không buộc làm module riêng P1; giữ nền | Hoàng |
| Bàn giao | Kiểm tra nền từng pass local và CI | Test nghiệp vụ, responsive, báo cáo, demo cả 5 người | Cả nhóm |

Chưa triển khai InquiryNote/code/closed_at/other; các mục này là P2, không còn
chặn hoàn thành P1. Không cần migration bỏ brand trong P1. Kênh users control,
khách tự đổi lịch/mở lại cũng P2. Chưa có chức năng nào tự hoàn tất vì sửa SRS.
Chỉ đánh dấu hoàn tất khi chạy end-to-end, có quyền/validation và bằng chứng kiểm tra.
Suite sau tích hợp main gồm nền, Auth, admin user, frontend tích hợp, preview và inventory;
chưa nghiệm thu các module khác hoặc race trên hai kết nối MySQL.
Xem AUTH_VALIDATION và docs/team/HOANG_TASKS.md. Chưa kiểm trực quan responsive;
build/Blade render pass không thay việc Đạt review giao diện.
