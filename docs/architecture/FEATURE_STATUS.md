# Trạng thái thực tế

Ngày 16/10/2026. Đã triển khai H01 tài khoản trên nhánh feature/auth-middleware.
Chưa có website showroom hoặc nghiệp vụ chạy hoàn chỉnh.

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
Suite H02 hiện 28 test/222 assertions: 6 nền, 12 Auth, 8 admin user và 2 inventory;
chưa nghiệm thu các module khác hoặc race trên hai kết nối MySQL.
Xem AUTH_VALIDATION và docs/team/HOANG_TASKS.md. Chưa kiểm trực quan responsive;
build/Blade render pass không thay việc Đạt review giao diện.
