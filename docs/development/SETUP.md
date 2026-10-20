# Môi trường phát triển

Dùng README để cài từ lockfile. Không chạy composer update/npm update chỉ để cài.
PHP 8.3, Composer 2, Node 22.12+, MySQL 8; DB timezone ứng dụng Asia/Ho_Chi_Minh.
API ngày giờ dùng ISO 8601 có offset +07:00 hoặc chuyển rõ sang timezone ứng dụng;
không coi chuỗi nhập từ datetime-local là UTC.

`.env.example` là template, `.env` riêng từng máy. APP_KEY tạo bằng artisan.
REVERB_APP_KEY là public client key; REVERB_APP_SECRET chỉ máy chủ, không đưa vào VITE.
Tạo secret riêng trên máy, ví dụ bằng PHP random_bytes. DEMO_PASSWORD chỉ dùng local/testing.
UserSeeder không chạy để tạo tài khoản demo trong production.

| Role | Email mẫu |
| --- | --- |
| admin | admin@example.test |
| sales | sales1@example.test, sales2@example.test |
| customer | customer1@example.test, customer2@example.test, customer3@example.test |

H01 có /dang-ky, /dang-nhap, /tai-khoan; H02 có /admin/users dành cho admin
để tạo sales và khóa/mở customer/sales (không tạo/khóa admin).
Auth dùng session/cookie, SESSION_DRIVER=database trong env mẫu, không JWT.
Khi đăng ký public, chỉ truyền trường khách được sửa; User.role/is_active không mass assignable.

`php artisan test` mặc định dùng SQLite in-memory, không đụng database demo.
Kiểm tra MySQL riêng: tạo DB test riêng, dùng biến môi trường testing chỉ tới DB đó.
CI có MySQL service riêng để chạy lại cùng bộ test. Tránh migrate:fresh trên database dùng chung.
Khi bổ sung test cạnh tranh lịch, dùng hai kết nối thật MySQL và xác nhận chỉ một lịch thành công.

`.gitignore` giữ dependency, env, uploads, cache và database local ngoài Git.
Đưa migration, seed dữ liệu minh họa, cấu hình template và lockfile vào repo.
Ảnh công khai dùng trong demo phải có nguồn/quyền phù hợp; chưa lấy ảnh hay giá thật trong setup.

Schema quản lý bằng migration, không sửa DB tay thay cho migration.
Mỗi máy tạo database riêng; pull bản mới rồi migrate, không reset DB chung.
Chủ module dùng factory/fixture test riêng; seed demo tổng do Hoàng tổng hợp.
Xem docs/team/PARALLEL_WORK.md về thứ tự nhánh/PR và bàn giao module.

## MySQL riêng và phục hồi dữ liệu

Xem [MYSQL_LOCAL.md](MYSQL_LOCAL.md): instance loopback, user dev/test/restore tách biệt,
script migration/test/backup/restore và bằng chứng kiểm thử local 20/10/2026.
