# Trạng thái thực tế

Cập nhật tài liệu 2026-10-16; mã vẫn baseline 2026-10-14. Dùng bảng này để hiểu hệ thống đã làm đến đâu.
Trạng thái nền tảng không có nghĩa nghiệp vụ đã hoàn tất.

| Module | Đã có | Chưa có | Chủ trách nhiệm |
| --- | --- | --- | --- |
| Framework | Laravel 12, lockfile, Vite/Tailwind, env mẫu, CI | Website showroom hoàn chỉnh | Hoàng + Đạt |
| Dữ liệu | Migration 8 core + audit, enum và quan hệ model | Đích SRS 10 bảng, InquiryNote/code/closed_at, Toyota-only và seed demo | TV3/TV5 |
| Auth/quyền | User role/active, middleware alias, limiter nền, seed user local | Login/register/logout, policies và lifecycle khóa user đầy đủ | Hoàng |
| Catalog | Bảng/model, thư mục controller/request/service/view/test | CRUD/upload/search/filter/pagination, public/admin UI | TV3 + Đạt |
| Inquiry | Bảng/model/enum, thư mục module | Tạo/phân công/chuyển/đóng, audit thao tác, policy, UI | TV4 |
| Chat | Reverb config/dependency, Echo factory, route channels từ chối mặc định | HTTP gửi tin, event sau commit, quyền kênh theo hồ sơ, reconnect/dedupe và UI | TV4 + Đạt |
| Appointment | Schema/quan hệ/trạng thái, thư mục module | Form, giao người, xác nhận có khóa hàng, đổi/hủy/no_show, UI | TV5 |
| Audit | Bảng/model/namespace | Service che dữ liệu, ghi các action, màn hình log | Hoàng |
| Kiểm tra | Test nền pass local SQLite; CI SQLite/MySQL và build frontend pass | Toàn bộ acceptance của các nghiệp vụ và chat hai phiên | Cả nhóm |

Chỉ đánh dấu Hoàn tất khi chức năng chạy từ đầu tới cuối, có quyền/validation,
test phù hợp và bằng chứng demo. Bộ khung không tự nhận chat đang hoạt động.
Kênh chat hiện từ chối tất cả để tránh mở dữ liệu trước khi TV4 viết policy đúng.


## Tài liệu mới chưa là chức năng mới

SRS 1.3/v6 đang ở PR tài liệu. Các thay đổi SRS mục 12 chưa triển khai.
Không dùng test nền làm bằng chứng đạt 26 tình huống SRS. TV4 làm ghi chú/chat/lifecycle;
TV3/Hoàng làm migration Toyota; TV5 làm other/lịch; Hoàng xử lý route và login failure-only.
