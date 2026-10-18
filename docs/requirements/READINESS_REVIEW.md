# Rà soát khả năng triển khai nhóm — 16/10/2026

Phạm vi: SRS/Word nguồn, schema/model/enum, Auth, routes/middleware, phân công,
CI/inventory và hướng dẫn môi trường. Không xác nhận chức năng chưa có code.
Không thể cam kết “hoàn hảo/không bao giờ lỗi”; mục tiêu là luật nhất quán và test được.

| Phát hiện | Ảnh hưởng | Xử lý |
| --- | --- | --- |
| CI PR #2 thất bại tại inventory, 11 hash Git khác local | Cản merge dù test local pass | Sửa CRLF/LF trong #1, đưa vào #2; CI #1 run 38884213819 đã success, #2 kiểm lại sau push |
| /customer prefix khác URL hợp đồng | TV4/TV5 và frontend dễ gọi sai | Bỏ URL prefix ở bootstrap, giữ middleware/route name; chưa có endpoint bị đổi |
| Code-first chưa giải thích rõ | Có thể sửa DB tay hoặc chỉ sửa model | ADR 0004 và quy trình migration/backfill/rollback |
| Nhiều người cùng route/layout/seed | Conflict và chờ frontend/integrator | PARALLEL_WORK ghi file owner, namespace, seeder riêng và fixture |
| Session vs JWT chưa ghi rõ trong architecture | Một module có thể tạo auth thứ hai | Giữ session database + cookie/CSRF, private channel dùng session |
| H01 thiếu chặn password NUL/quá 72 byte | Bcrypt có thể lỗi/cắt input | Validate trước hash/check, test NUL/Unicode/giới hạn byte |
| SETUP vẫn ghi chưa có login | Thành viên không biết đã có H01 | Cập nhật URL/cách thử/giới hạn |
| Trọng số/10 bảng và rule cũ còn trong nguồn | AI có thể thực thi yêu cầu đã hoãn | SRS 1.4 + COURSE_SCOPE + ADR 0003/0004 ưu tiên; source readonly |

## Các điều kiện vẫn chưa đạt — không che bằng tài liệu

- PR #1/#2 cần người khác review; chưa bật/xác minh branch protection/CODEOWNERS.
- H02 tạo/khóa tài khoản và tích hợp version của chat chưa triển khai.
- Catalog/CRUD/ảnh/tư vấn/chat/lịch nghiệp vụ còn do owner làm; có schema không là đã xong.
- Chưa demo layout responsive trực quan; browser tool lỗi môi trường ở vòng H01.
- Chưa có service lịch/chat để thử race/revocation thật. Khi có phải test hai
  kết nối MySQL/two-session và job chờ.
- Đề tài/rubric riêng, ngày nộp và GitHub username cần nhóm bổ sung khi có thông tin.
- Dữ liệu xe/ảnh mẫu và seeder module chưa bàn giao; không phụ thuộc API Toyota.

## Cách chốt thực tế

Kiểm local sau chỉnh lý: composer check pass 20 tests/158 assertions,
gồm 12 Auth, 6 nền và 2 inventory; Pint/cú pháp/encoding pass.
Không thay schema hoặc frontend trong vòng rà soát này.

Chốt schema/contract và PARALLEL_WORK làm nền phát triển P1.
Gộp tài liệu/nền rồi H01 sau review/CI. Mỗi người nhận một task nhỏ, tự test bằng
DB riêng và review chéo. Chỉ gọi sản phẩm sẵn sàng nộp khi ACCEPTANCE P1 có kết quả,
cài được máy khác, UI/báo cáo và demo đủ cả 5 người.
