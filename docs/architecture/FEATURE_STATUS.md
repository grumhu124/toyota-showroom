# Trạng thái thực tế

28/10: nhánh giao diện đồng bộ lịch hẹn từ main, account customer có lịch sắp tới từ DB và liên kết /lich-hen. Trang lịch khách dùng layouts.customer, hủy theo endpoint TV5 có kiểm owner/time/state. Chat vẫn preview local/testing; chưa gộp TV4 vào bản này.

Ngày 20/10/2026. Đã gộp vào main: docs/review-v6-srs, H01 (feature/auth-middleware),
H02 (feature/admin-users), frontend home/catalog preview (feature/frontend-layout),
tích hợp auth vào giao diện frontend (feature/frontend-auth-integration) và nhánh
feature/appointment-schedule (TV5: đặt lịch, phân công, xác nhận, hủy, hoàn tất).
H03 mới có hợp đồng quyền/gói review. Chưa có nghiệp vụ tư vấn/chat hoàn chỉnh.

Trang chủ tối, video/canvas, hai ảnh chi tiết, /xe slider/card/lọc và /xe/{id} ID 1–9
vẫn dùng fixture local/testing (chỉ chạy ở local/testing). Header đã nối Auth thật,
form đăng nhập/đăng ký dùng ảnh xe chạy và nền tối; backend H01/H02 giữ nguyên.
Tư vấn/chat chưa nối backend; đặt lịch đã nối backend với form + quyền.

| Module | Đã có | P1 còn làm | Chủ trách nhiệm |
| --- | --- | --- | --- |
| Framework | Laravel/lockfile/Vite/Tailwind/CI | Cài/demo máy khác | Hoàng + nhóm |
| Dữ liệu | 8 bảng core + audit, enum/model | Seed Toyota; giữ brand=Toyota, internal_note | Võ Nguyễn Thành Đạt (TV3)/Phan Võ Chí Bảo (TV5) + Hoàng |
| Auth | H01; H02 UserPolicy, admin list/search/pagination/tạo sales/khóa-mở, transaction tăng version khi khóa; test HTTP pass | Review/kiểm trực quan H02, tích hợp thu hồi socket/job với TV4 | Hoàng |
| Catalog | Schema/model/thư mục | CRUD/ảnh/search/filter/pagination/UI | Võ Nguyễn Thành Đạt (TV3) + Đạt |
| Tư vấn | Inquiry model/enum | Form AJAX, phân công/trạng thái/internal_note/quyền | Nguyễn Huy Hoàng (TV4) + Đạt |
| Chat | Reverb/Echo cấu hình, private channel từ chối mặc định | Gửi/lưu/phát, lịch sử, retry/catch-up, thu hồi quyền, UI hai phiên | Nguyễn Huy Hoàng (TV4) + Đạt |
| Lịch/dịch vụ | Form + rights; CRUD dịch vụ admin; xác nhận khóa/trùng giờ; hủy/hoàn tất/no_show; tests pass | Kiểm trực quan trình duyệt và MySQL thật | Phan Võ Chí Bảo (TV5) |
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
