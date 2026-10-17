# Kiến trúc hệ thống Toyota Showroom

Cập nhật tài liệu 2026-10-16; mã baseline chưa thay đổi. Kiến trúc là Laravel MVC đơn khối cho một showroom.
Tài liệu mô tả cả nền tảng hiện có và hợp đồng cần triển khai; xem
[FEATURE_STATUS](docs/architecture/FEATURE_STATUS.md) để phân biệt.

## Luồng xử lý dự kiến

Trình duyệt → route web → auth/active/role middleware → FormRequest + Policy
→ Controller → Service khi cần → Eloquent/MySQL → Blade hoặc JSON.
Chat nhận tin qua Echo ← Reverb ← sự kiện sau commit từ MessageService.
Queue worker chạy riêng cho broadcasting dùng hàng đợi; MySQL là nơi lưu tin chính.

## Cơ chế đã chốt

Code-first bằng migration versioned (ADR 0004): migration tạo/thay schema,
Eloquent Model định nghĩa truy cập/casts/quan hệ; sửa Model không tự đổi database.
Đăng nhập session + cookie; env mẫu dùng database session 120 phút và CSRF.
Không dùng JWT/Sanctum/Passport; private channel tái dùng session.
Phân công file chung/route/seed và giao thức khóa ở docs/team/PARALLEL_WORK.md.

## Vị trí mã nguồn

| Lớp | Vị trí | Trách nhiệm |
| --- | --- | --- |
| Giao diện | resources/views, resources/css, resources/js/modules | Blade, trạng thái UI, fetch, Echo; không quyết định quyền |
| HTTP | routes, app/Http/Controllers, app/Http/Requests | URL, validate, điều phối; không nhồi toàn bộ transaction vào view |
| Truy cập | app/Http/Middleware, app/Policies, routes/channels.php | Active/role và quyền từng hồ sơ/kênh |
| Nghiệp vụ | app/Services/Catalog, Inquiry, Appointment, Audit | Thao tác nhiều bước, transaction, log an toàn |
| Tài khoản | app/Services/Account, app/Policies/UserPolicy.php | H02 khóa/mở user và version hồ sơ trong transaction; role admin bắt buộc |
| Dữ liệu | app/Models, app/Enums, database | Quan hệ, casts, migration, seeder, enum |
| Sự kiện | app/Events, config/broadcasting.php, config/reverb.php | Tin mới sau commit; không thay thế lưu CSDL |

Các thư mục module đã có chỗ đặt mã. Không tạo controller/service rỗng mang tên
chức năng hoàn thành. Controller CRUD đơn giản có thể dùng Eloquent trực tiếp;
Service dành cho phân công, gửi tin, xác nhận lịch hoặc thao tác nhiều bước.

## Module và phụ thuộc

- Auth và users là nền cho mọi hồ sơ đăng nhập.
- Catalog quản lý car_categories, cars, car_images; Inquiry/Appointment tham chiếu xe.
- Inquiry quản lý yêu cầu, người phụ trách, messages và access_version.
- Appointment quản lý lịch viewing/maintenance, dùng cars/services và users.
- Audit ghi các thay đổi quản trị, không sao chép toàn bộ tin nhắn hoặc liên hệ khách vào payload.
- Frontend dùng layout/component chung; dữ liệu module đến từ controller cùng nhóm route.

Không đặt code theo tên thành viên vào app/Hoang hoặc app/Dat. Chia theo module
để đổi người phụ trách không phải đổi kiến trúc. Phân công nằm trong tài liệu ownership.

## Dữ liệu và trạng thái

Mã hiện có **8 bảng cốt lõi + 1 audit**. SRS 1.4 giữ 9 bảng này cho P1; inquiry_notes là P2. Bảng kỹ thuật tính riêng.
[DATA_MODEL](docs/architecture/DATA_MODEL.md) giữ hợp đồng trường và quan hệ.
Trạng thái là PHP backed enum, cột string để migration/test tương thích MySQL và SQLite.
MySQL vẫn là database phát triển và demo chính; SQLite test nhanh không chứng minh
rằng khóa hàng và xử lý cạnh tranh hoạt động đúng trên MySQL.

## Ranh giới an toàn

Public chỉ thấy xe và dòng xe công khai. Dữ liệu customer thuộc tài khoản;
sales chỉ hồ sơ được giao; admin có quyền quản trị. Role middleware chưa đủ
thay cho Policy quyền sở hữu. Kênh chat có inquiry_id + access_version.
Đổi phiên quyền làm tin mới chuyển kênh, không xóa tin đã nhận trước đó.
Log dùng danh sách trường cho phép và che thông tin liên hệ; không log credentials.

## Tài liệu kiểm soát thay đổi

- [INVENTORY.json](docs/architecture/INVENTORY.json): danh mục và hash tệp kiểm soát.
- [CHANGELOG](docs/changes/CHANGELOG.md): lý do, thêm/sửa/xóa và bằng chứng kiểm tra.
- [ADR](docs/decisions/0001-foundation.md): quyết định thiết kế và xử lý mâu thuẫn.
- Git commit/PR: diff chính xác và người thực hiện; tài liệu không thay Git.

Thêm/sửa/xóa file cần cập nhật inventory. Đổi schema, route, quyền hoặc cấu trúc
cần cập nhật tài liệu tương ứng. Chỉ sửa chữ hoặc CSS không buộc viết lại toàn bộ
kiến trúc, nhưng vẫn ghi trong PR/nhật ký và chạy kiểm tra liên quan.

## Tài liệu kỹ thuật chính thức

[Laravel 12](https://laravel.com/docs/12.x/installation),
[Broadcasting](https://laravel.com/docs/12.x/broadcasting),
[Reverb](https://laravel.com/docs/12.x/reverb).
Phiên bản chính dùng theo bài thực hành hiện có, phiên bản chính xác nằm ở lockfile.


## Vòng yêu cầu v6

[SRS](SRS.md) 1.4 giữ tên module và bảng truy vết yêu cầu cũ;
[ADR 0002](docs/decisions/0002-v6-srs-review.md) là lịch sử rà soát v6.
Word v6 giữ nguyên làm nguồn, phạm vi theo ADR 0003.
DATA_MODEL/FEATURE_STATUS giữ schema và trạng thái thật; PR tài liệu không mở chat
hoặc áp schema 10 bảng. Kênh điều khiển users.{id} là P2, chưa có mã; P1 có thể tải lại khi đổi quyền nhưng người cũ phải bị chặn tin mới.

## Phạm vi hiện hành theo ADR 0003

SRS 1.4 và COURSE_SCOPE thay các đích v6 trái phạm vi môn học.
Giữ schema nền/internal_note/brand=Toyota; chưa thêm module audit UI, InquiryNote,
code/closed_at hay other. Audit namespace/bảng là khả năng nền, không chức năng đã chạy.
Không thay kiến trúc MVC, private chat, thu hồi quyền và transaction bảo vệ dữ liệu.
Một cars là một chiếc mã riêng; lịch tiếp nhận 60 phút là quy ước demo.
