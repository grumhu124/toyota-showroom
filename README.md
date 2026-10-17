# Toyota Showroom

Đồ án Lập trình mã nguồn mở của nhóm 5 người. Website giới thiệu xe, giá công bố,
tiếp nhận tư vấn, chat thời gian thực và lịch xem xe hoặc bảo dưỡng.
Việc mua bán, giấy tờ và thanh toán được thực hiện tại showroom trong phạm vi bài.

**Hiện tại đây là bộ khung phát triển, chưa phải website đã hoàn thành.**
Đã có Laravel, cấu hình Reverb, schema nền, model, middleware và tài liệu.
H01 đã có đăng ký/đăng nhập/đăng xuất và màn tài khoản tối thiểu.
H02 có quản lý user phía admin ở /admin/users (tạo sales, khóa/mở customer/sales).
CRUD xe, tư vấn/chat và lịch vẫn chưa hoàn chỉnh.

## Đọc trước khi làm

1. [AGENTS.md](AGENTS.md) — quy tắc cho AI và người sửa mã.
2. [SRS 1.4 theo phạm vi môn học](SRS.md) và [chỉ mục đặc tả](docs/requirements/SPECIFICATION.md).
3. [Kiến trúc hệ thống](ARCHITECTURE.md).
4. [Phân công và vị trí thư mục](docs/team/OWNERSHIP.md).
5. [Trạng thái thực tế](docs/architecture/FEATURE_STATUS.md).
6. [Cách góp mã](CONTRIBUTING.md).
7. [Task list của Hoàng](docs/team/HOANG_TASKS.md) — thứ tự phần repo/auth/tích hợp.
8. [Quy ước làm song song](docs/team/PARALLEL_WORK.md) và [kết quả rà soát](docs/requirements/READINESS_REVIEW.md).

## Công nghệ đã chọn

PHP 8.3, Laravel 12, MySQL 8, Blade, Tailwind CSS 4, Vite, JavaScript,
Laravel Reverb và Echo. Cài bằng lockfile, không tự nâng phiên bản chính.
Laravel 12 khớp bài thực hành hiện có; các quyết định ở [ADR](docs/decisions/0001-foundation.md).

## Cài trên máy của thành viên

Cần PHP 8.3 với các extension Laravel yêu cầu, `pdo_mysql`, `pdo_sqlite`, `zip`,
Composer 2, Node 22.12 trở lên trong dòng 22, npm và MySQL 8 đang chạy.
Có thể mở terminal của Laragon để PHP và Composer nằm trong PATH.

```sh
git clone https://github.com/grumhu124/toyota-showroom.git
cd toyota-showroom
composer install
npm ci
```

Sao chép `.env.example` thành `.env` (`Copy-Item .env.example .env` trên PowerShell).
Tạo một database MySQL mới tên `toyota_showroom`; chỉnh DB_HOST, DB_PORT,
DB_USERNAME và DB_PASSWORD theo máy của bạn. Điền DEMO_PASSWORD và
REVERB_APP_SECRET riêng trên máy, không đưa các giá trị này lên Git.

```sh
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Mở `http://127.0.0.1:8000`. Bản nền hiện hiển thị trang welcome mặc định;
Đạt sẽ thay bằng giao diện showroom. Seeder tạo 1 admin, 2 sales, 3 customer
khi DEMO_PASSWORD có giá trị; chưa tạo xe hoặc dịch vụ giả như dữ liệu thật.
Danh sách email mẫu ở [hướng dẫn môi trường](docs/development/SETUP.md).

Mở /dang-ky hoặc /dang-nhap để thử H01; sau xác thực vào /tai-khoan.
Màn tài khoản chưa là dashboard tư vấn/lịch. Xem [kiểm tra H01](docs/testing/AUTH_VALIDATION.md).

## Khi phát triển chat

Mỗi tiến trình chạy ở một terminal; hiện chưa có endpoint gửi tin nghiệp vụ.

```sh
php artisan serve
php artisan reverb:start --host=127.0.0.1 --port=8080
php artisan queue:work --tries=3
npm run dev
```

Chat dùng WebSocket, không dùng polling thay cho thời gian thực.
HTTP dùng để gửi/lưu tin, WebSocket dùng để nhận sự kiện.
Đọc [hợp đồng chat](docs/architecture/CONTRACTS.md) trước khi viết mã.

## Kiểm tra và cập nhật kiến trúc

```sh
php scripts/architecture-inventory.php --write
composer check
npm run build
git diff --stat
```

Danh mục [INVENTORY.json](docs/architecture/INVENTORY.json) ghi đường dẫn và hash
các tệp được kiểm soát. Nếu thêm, sửa hoặc xóa tệp, bước kiểm tra chỉ ra khác biệt.
Git lưu lịch sử; [nhật ký](docs/changes/CHANGELOG.md) giải thích lý do thay đổi.
Quy trình này hỗ trợ kiểm soát, không tự bảo đảm mọi công cụ AI đều tuân thủ.


## Tài liệu ngày 16 tháng 10 năm 2026

Xem [rà soát v6/SRS](docs/requirements/REVIEW_V6_SRS.md) và ADR 0002.
Theo ADR 0003, P1 giữ 9 bảng nền/internal_note; 10 bảng của v6 là phương án cũ. PR tài liệu không thay DB/model/route/nghiệp vụ.
Word giữ nguyên; SRS gửi ban đầu lưu ở docs/source.

Upload 10 ảnh x 5MB: kiểm tra PHP phục vụ web, đề xuất upload_max_filesize >= 5M,
post_max_size >= 64M, max_file_uploads >= 10, chừa multipart và kiểm tra giới hạn proxy.
Server vẫn validate count/size/MIME; chưa tự sửa php.ini trên máy thành viên.

## Phạm vi trước khi chia việc

Đọc [COURSE_SCOPE](docs/requirements/COURSE_SCOPE.md): rubric thật, đối chiếu thực hành,
ràng buộc P1 và điều kiện mới được mở rộng P2. Chat WebSocket văn bản vẫn P1.
Một chiếc xe có mã riêng; giá công bố tham khảo; không làm công cụ tính lăn bánh.
SRS 1.4/ADR 0003 thay phạm vi trái chúng trong tài liệu v6 cũ; Word giữ nguyên.
