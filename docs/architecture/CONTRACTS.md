# Hợp đồng route và giao tiếp — P1

Theo [SRS 1.4](../../SRS.md)/ADR 0003, chưa phải endpoint đã triển khai.
Baseline chỉ welcome/health/broadcast auth và private channel deny-by-default.

| Nhóm | Route đích | Quyền |
| --- | --- | --- |
| Public | GET /xe, /xe/{id}, /dich-vu; POST /tu-van, /dat-lich/xem-xe, /dat-lich/bao-duong | Dữ liệu public, validate, throttle |
| Auth | GET/POST /dang-ky, /dang-nhap; POST /dang-xuat | guest/auth, active, CSRF |
| Customer | GET /yeu-cau, /yeu-cau/{inquiry}, /lich-hen; POST /lich-hen/{id}/huy | Own/active |
| Chat | GET/POST /yeu-cau/{inquiry}/tin-nhan | Own/assigned/admin; gửi chỉ assigned/in_progress |
| Staff/admin | /staff/... và /admin/... | Role/active + Policy từng hồ sơ |

Không có route đổi lịch/mở lại/audit UI/API status riêng trong P1.
Bootstrap còn nhóm /customer rỗng; chủ module sửa khi làm endpoint.
Giữ slug/source/brand; Toyota cố định phía server. Category lọc bằng slug,
chi tiết xe dùng ID, xe ẩn 404. Car code là mã chiếc xe, không là mã cả dòng xe.

## Form AJAX và lỗi

Form tư vấn Accept JSON trả 201 với id, thông báo tiếp nhận; không trả cả model.
Không dùng mã/id public để đọc hồ sơ; guest liên hệ qua phone.
Email form ánh xạ contact_email. Không nhận customer_id/role/assigned_to từ khách.
422 validation/trùng lịch; 401 chưa đăng nhập; 403 sai quyền; 404 không tồn tại/ẩn;
429 throttle; 409 UUID dùng lại khác payload. Web dùng redirect/flash phù hợp.
Form public 3 lần/10 phút/IP/endpoint; login 5 thất bại/phút/IP+email.
Không nhận limiter đếm request hiện có là failure-only login đã chạy.

## Chat

POST content/client_message_id, sender từ session; trả id/inquiry_id,
sender(id,name), content, created_at; không serialize user/inquiry/internal_note.
History tối đa 50/lần, after_id/cursor; client dedupe ID, kết nối lại bù lịch sử.
Unique sender/UUID; retry cùng payload trả bản cũ 200, khác trả 409.
Lưu rồi phát sau commit, chỉ lần tạo mới phát. Subscribe trước catch-up.
Private inquiry.{id}.v{version} kiểm active/role/ownership/assigned/version.
Chuyển sales/khóa tài khoản tăng version; kiểm quyền/version hiện tại khi gửi/phát,
không chuyển tin của job cũ sang kênh cũ. Dữ liệu đã nhận không thu lại được.
Người còn quyền có thể tải lại để lấy version mới; kênh users.{id} tự chuyển là P2.
New/closed chỉ đọc; không có reopen trong P1. UI chưa gửi được phải nói rõ.

## Lịch

Validate type/giờ/xe/dịch vụ; admin phân công sales active.
Xác nhận transaction khóa appointment rồi sales, đọc lại pending và quyền,
kiểm giờ/xe/service và lịch confirmed khác cách <60 phút, rồi ghi.
Mọi confirmation dùng cùng service/protocol, tránh hai request ghi đè/trùng.
Đúng 60 phút hợp lệ; đây là quy ước tiếp nhận demo, không mô hình xưởng.
Customer hủy pending/confirmed chưa tới giờ, server kiểm own và lưu lý do.
Nhân viên/admin hủy kèm lý do; hoàn tất/no_show sau giờ confirmed_at.
Không sửa trạng thái cuối, không tự giữ xe từ lịch; không gán inquiry khác customer.
Giờ không phù hợp: hủy, tạo yêu cầu mới; đổi lịch chuyên biệt là P2.
