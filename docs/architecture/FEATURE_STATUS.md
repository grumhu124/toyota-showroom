# Trạng thái thực tế

Ngày 16/10/2026. Mã vẫn baseline, SRS 1.4 chỉ thay yêu cầu/tài liệu.
Chưa có website showroom hoặc nghiệp vụ chạy hoàn chỉnh.

| Module | Đã có | P1 còn làm | Chủ trách nhiệm |
| --- | --- | --- | --- |
| Framework | Laravel/lockfile/Vite/Tailwind/CI | Cài/demo máy khác | Hoàng + nhóm |
| Dữ liệu | 8 bảng core + audit, enum/model | Seed Toyota; giữ brand=Toyota, internal_note | TV3/TV5 + Hoàng |
| Auth | Role/active middleware, limiter nền, seed user | Register/login/logout, Policy, khóa phiên/kênh, failure-only limit | Hoàng |
| Catalog | Schema/model/thư mục | CRUD/ảnh/search/filter/pagination/UI | TV3 + Đạt |
| Tư vấn | Inquiry model/enum | Form AJAX, phân công/trạng thái/internal_note/quyền | TV4 + Đạt |
| Chat | Reverb/Echo cấu hình, private channel từ chối mặc định | Gửi/lưu/phát, lịch sử, retry/catch-up, thu hồi quyền, UI hai phiên | TV4 + Đạt |
| Lịch/dịch vụ | Schema/model/enum | Form, phân công, xác nhận khóa/trùng giờ, hủy/hoàn tất/no_show | TV5 |
| Audit | Bảng/model/namespace | Không buộc làm module riêng P1; giữ nền | Hoàng |
| Bàn giao | Kiểm tra nền từng pass local và CI | Test nghiệp vụ, responsive, báo cáo, demo cả 5 người | Cả nhóm |

Chưa triển khai InquiryNote/code/closed_at/other; các mục này là P2, không còn
chặn hoàn thành P1. Không cần migration bỏ brand trong P1. Kênh users control,
khách tự đổi lịch/mở lại cũng P2. Chưa có chức năng nào tự hoàn tất vì sửa SRS.
Chỉ đánh dấu hoàn tất khi chạy end-to-end, có quyền/validation và bằng chứng kiểm tra.
Test nền 6 test/19 assertions không nghiệm thu chức năng ở bảng trên.
