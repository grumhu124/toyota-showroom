# Trạng thái thực tế

Ngày 19/10/2026. Đã gộp vào main: docs/review-v6-srs, H01 (feature/auth-middleware),
H02 (feature/admin-users), frontend home/catalog preview (feature/frontend-layout) và
tích hợp auth vào giao diện frontend (feature/frontend-auth-integration). H03 mới có
hợp đồng quyền/gói review. Chưa có nghiệp vụ tư vấn/lịch/chat hoàn chỉnh.

Frontend home local/testing: / có hero Camry theo Figma, cuộn video/canvas,
ảnh fallback, dịch vụ/showroom và link sang /xe/1. Chưa có dịch vụ/đặt lịch backend.
Xem docs/development/HOME_PREVIEW.md về giới hạn xử lý nền video.

## Cập nhật frontend local 16/10/2026

Có thêm /xe/{id} cho 9 xe fixture: ảnh phóng to, thông tin, thông số chưa xác nhận,
CTA theo available/reserved/sold và xe liên quan. Card/hero đã nối chi tiết.
Đây vẫn là UI minh họa, không phải Catalog/đặt lịch/tư vấn backend hoàn chỉnh.

Có trang xem trước /xe (chỉ local/testing): layout/header/footer, slider 3 xe
dịch ngang, card, bộ lọc fixture và dialog báo chức năng chưa nối backend.
Tên xe chuyển dọc, xe chuyển ngang; 6 card sản phẩm, 3 cột desktop, bộ lọc fixture.
Chưa có CRUD, dữ liệu xe thật, form đặt lịch hoặc auth. Xem docs/development/COLLECTION_PREVIEW.md.

Cập nhật baseline 2026-10-14. Dùng bảng này để hiểu hệ thống đã làm đến đâu.
Trạng thái nền tảng không có nghĩa nghiệp vụ đã hoàn tất.

Trang auth dùng layout frontend (feature/frontend-auth-integration): header hiện
Tài khoản/Đăng xuất khi đã đăng nhập, liên kết Đăng nhập khi là khách.

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
| H03 | Ma trận quyền theo bản ghi, vị trí shared chat route, checklist review/ca test đích | Owner review; Policy/endpoints của TV3/4/5 và nghiệm thu tích hợp | Hoàng + các owner |
| Bàn giao | Kiểm tra nền từng pass local và CI | Test nghiệp vụ, responsive, báo cáo, demo cả 5 người | Cả nhóm |

Chưa triển khai InquiryNote/code/closed_at/other; các mục này là P2, không còn
chặn hoàn thành P1. Không cần migration bỏ brand trong P1. Kênh users control,
khách tự đổi lịch/mở lại cũng P2. Chưa có chức năng nào tự hoàn tất vì sửa SRS.
Chỉ đánh dấu hoàn tất khi chạy end-to-end, có quyền/validation và bằng chứng kiểm tra.
Suite H02 hiện 28 test/222 assertions: 6 nền, 12 Auth, 8 admin user và 2 inventory;
chưa nghiệm thu các module khác hoặc race trên hai kết nối MySQL.
Xem AUTH_VALIDATION và docs/team/HOANG_TASKS.md. Chưa kiểm trực quan responsive;
build/Blade render pass không thay việc Đạt review giao diện.
