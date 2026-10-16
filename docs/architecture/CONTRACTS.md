# Hợp đồng route và giao tiếp

Đích theo [SRS 1.3](../../SRS.md) mục 5, đề xuất qua ADR 0002.
Chưa có endpoint nghiệp vụ; baseline chỉ welcome/health/broadcast auth và kênh deny-by-default.

| Nhóm | Route đích | Kiểm quyền |
| --- | --- | --- |
| Public | GET /xe, /xe/{id}, /dich-vu; POST /tu-van, /dat-lich/xem-xe, /dat-lich/bao-duong | Chỉ dữ liệu public, validate/throttle |
| Auth | GET/POST /dang-ky, /dang-nhap; POST /dang-xuat | guest/auth tùy route, active/CSRF |
| Customer | /yeu-cau, /yeu-cau/{inquiry}, /lich-hen và hủy/đổi giờ theo SRS | auth/active/customer + Policy own |
| Chat | GET/POST /yeu-cau/{inquiry}/tin-nhan | auth/active/Policy; POST chỉ assigned/in_progress |
| Staff/Admin | /staff/... và /admin/... | role, active và Policy từng hồ sơ |

Bootstrap còn nhóm /customer rỗng; đổi khi làm endpoint, hiện không nhận đã có route mới.
Giữ cột slug hiện có; route xe ID không yêu cầu xóa slug. API status là mức nên có sau core.
AJAX Accept JSON: 422 validation/trùng lịch, 401 login, 403 quyền, 404 public ẩn/không có,
429 limiter; 409 UUID có payload khác. Web redirect/flash phù hợp.

## Chat

POST content/client_message_id, sender lấy từ session. Trả id/inquiry_id/sender(id,name)/
content/created_at, không serialize User/Inquiry. History after_id/cursor tối đa 50.
So inquiry/content khi UUID trùng; unique DB chặn race; chỉ lần tạo phát sau commit.
Echo.private tên inquiry.{id}.v{version}; kiểm active/enum/role/owner/assigned/version.
HTTP gửi còn kiểm trạng thái, queue job dùng version hiện tại khi phát.
Kênh users.{id} riêng đúng account active chỉ báo đổi quyền, không mang nội dung chat.
Client còn quyền lấy version HTTP, subscribe thành công rồi catch-up/dedupe.
New chỉ hiển thị nội dung form/chờ; reopen cần sales active và giữ kết quả cũ trong audit.
Các cơ chế trên chưa được lập trình trong baseline.

## Lịch hẹn

Validate theo type, sales active. Transaction khóa appointment, kiểm lại pending/quyền,
khóa sales, kiểm giờ/xe/dịch vụ/overlap rồi ghi và audit. Đổi sales khóa user theo ID
thống nhất; tất cả đường sửa lịch dùng chung giao thức khóa.
Khung [start,start+60 phút), chênh đúng 60 hợp lệ. Đổi giờ về pending/confirmed_at null;
server nối reason/giờ cũ, client không ghi đè lịch sử note. No_show chỉ sau giờ hẹn.
inquiry_id gắn lịch phải thuộc cùng customer, không nhận gán chéo từ payload.
