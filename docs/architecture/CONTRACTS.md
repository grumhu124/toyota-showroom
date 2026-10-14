# Hợp đồng route và giao tiếp

**Các endpoint nghiệp vụ dưới đây là kế hoạch, chưa được đăng ký trong baseline.**
Khi làm route thật, module owner cập nhật bảng và FEATURE_STATUS.
Route chạy hiện tại xem php artisan route:list: welcome, health và broadcast auth.

| Nhóm | Route dự kiến | Quyền |
| --- | --- | --- |
| Public | GET /xe, /xe/{slug}, /dich-vu; POST /tu-van, /lich-hen | Khách vãng lai; chỉ dữ liệu công khai |
| Auth | GET/POST /dang-ky, /dang-nhap; POST /dang-xuat | guest/auth tùy route; active; CSRF |
| Customer | /customer/yeu-cau, /customer/lich-hen; đổi/hủy lịch mình | auth + ensure.active + role:customer + Policy |
| Chat | GET/POST /chat/{inquiry}/messages | auth + ensure.active + Inquiry/Message Policy |
| Staff | /staff/yeu-cau, /staff/lich-hen | auth + ensure.active + role:sales,admin + Policy assigned |
| Admin | /admin/xe, /admin/dong-xe, /admin/dich-vu, /admin/users, /admin/logs | auth + ensure.active + role:admin |

Public /xe/{slug} dùng cars.slug unique; code quản trị cũng unique.
HTML trả Blade; AJAX yêu cầu Accept: application/json.
FormRequest trả validation 422 có errors theo trường; 401 cần login, 403 thiếu quyền,
404 public ẩn/không có, 409 tranh chấp hoặc UUID trùng payload khác, 429 vượt limiter.
Sau POST form HTML redirect với flash; AJAX trả id + trạng thái, không lộ nội bộ.

## Gửi và tải tin

POST: content + client_message_id; không nhận sender_id hay role từ client.
Trả id, inquiry_id, sender (id/name), content, created_at. Không gửi email/phone,
note nội bộ, password hoặc toàn bộ serialized User. History GET after_id và limit
giới hạn tối đa 50; các trang cũ dùng cursor, thứ tự theo message ID.
UUID trùng sender được kiểm tra với inquiry/content để trả đúng idempotent response.

Kênh Echo logical name inquiry.{id}.v{version}; Echo tự thêm private- prefix.
Authorized khi account active và là customer owner/sales đang assigned/admin,
và version khớp DB. Event chỉ mang thông tin tin nhắn tối thiểu sau commit.
Khi version đổi, client còn quyền lấy version mới qua HTTP có Policy;
rời kênh cũ, tham gia kênh mới, rồi tải tin thiếu và dedupe theo ID.
Event queue phải xác định quyền/version hiện tại lúc phát, không dùng phiên cũ đã chụp.
reconnect/catch-up là HTTP đồng bộ khi có sự kiện nối lại, không polling định kỳ thay chat.

## Hợp đồng xác nhận lịch

assigned_to phải là sales active; confirmed_at tương lai, đủ điều kiện xe/dịch vụ.
Khóa row users của sales trong transaction trước khi truy vấn conflict.
Lịch A chồng B khi A.start < B.end và A.end > B.start, khung 60 phút.
Cùng giao thức khóa dùng khi confirm/reschedule đổi người để tránh race.
Chặn đổi người phụ trách lịch confirmed mà chưa kiểm tra lịch của người mới.
Khi xe bán/ẩn sau khi confirmed, không tự xóa lịch: nhân viên liên hệ và đổi/hủy.
