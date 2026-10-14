# Kiểm tra bản nền ngày 2026-10-14

Môi trường local: PHP 8.3.30, Node 22.22.0, Laravel 12.70.0, Reverb 1.12.0.
Phiên bản thư viện cài chính xác nằm trong composer.lock và package-lock.json.

| Kiểm tra | Kết quả ghi nhận |
| --- | --- |
| PHPUnit với SQLite in-memory | 6 test, 19 assertions pass: smoke HTTP, seeder idempotent/password hash, chặn mass assignment role, quan hệ/UUID unique, role/active và channel deny-by-default |
| Vite build với Node 22 | Pass; Echo/Pusher được biên dịch trong bundle |
| Composer dependency audit | Không có advisory được báo trong lần cài/đồng bộ lockfile |
| npm dependency audit | 0 vulnerabilities được báo trong lần cài |
| MySQL schema/tests | Pass ở GitHub Actions MySQL service; không chạy trên DB MySQL local của người dùng |
| WebSocket end-to-end | Chưa có nghiệp vụ gửi/broadcast; chưa nghiệm thu chat hai phiên |
| Khóa hàng/xung đột lịch đồng thời | Chưa có service xác nhận lịch; chưa kiểm tra cạnh tranh |

Các test trên chứng minh nền được kiểm tra ở phạm vi đó, không chứng minh toàn bộ
website đã hoàn thành. Kiểm tra inventory, syntax và Pint thuộc `composer check`;
kết quả chạy cuối và CI được đối chiếu với commit trong repo.
Nếu bổ sung kiểm thử sau baseline, thêm entry CHANGELOG thay vì sửa lịch sử thành pass giả.

CI của commit nền `33cec565a1aae265b89b2937c973db76715beca8` đã hoàn thành:
frontend, php (sqlite), php (mysql) đều success.
[Bằng chứng chạy](https://github.com/grumhu124/toyota-showroom/actions/runs/38555128110).
Danh mục 150 tệp cũng đã đối chiếu hash với Git blobs, không có khác biệt.
