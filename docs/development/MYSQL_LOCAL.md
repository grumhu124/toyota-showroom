# MySQL cục bộ riêng — H04

## Máy đã chuẩn bị ngày 20/10/2026

MySQL 8.4.3 tại 127.0.0.1:3307, bind loopback; CI vẫn dùng MySQL 8.0.
Không thay .env, không thay server Laragon cổng 3306, không đổi SQLite demo.
Dữ liệu instance nằm tại `.setup-cache/mysql-local/data`, không đưa vào Git.

| Mục đích | Database | User |
| --- | --- | --- |
| Phát triển, dữ liệu giữ lâu | toyota_showroom_dev | toyota_dev@127.0.0.1 |
| Test có RefreshDatabase | toyota_showroom_test | toyota_test@127.0.0.1 |
| Thử phục hồi riêng | toyota_showroom_restore | toyota_restore@127.0.0.1 |

Mỗi user chỉ có quyền trên database tương ứng; không chạy test bằng root/dev user.
Mật khẩu ngẫu nhiên lưu tại `.setup-cache/mysql-local/local.json` và các tệp
`dev.cnf`, `test.cnf`, `restore.cnf`, `root.cnf`. Không in/chia sẻ/commit các tệp này.
Bản SQL chứa password hash và có thể chứa thông tin khách, cũng phải giữ ngoài Git.

## Dùng trên máy này

Mở PowerShell tại repo. Script dùng PHP từ PATH hoặc đường dẫn Laragon của máy này.

```powershell
./scripts/mysql-local.ps1 -Action Status
./scripts/mysql-local.ps1 -Action Migrate
./scripts/mysql-local.ps1 -Action Test
./scripts/mysql-local.ps1 -Action Backup
```

Migrate chỉ chạy migration chưa có và UserSeeder local (1 admin, 2 sales, 3 customer).
Không reset DB, không đổi mật khẩu/trạng thái user đã seed.
Test chạy cả suite trên DB test riêng; RefreshDatabase có thể tạo lại bảng trong DB đó.
Helper cố định tên DB/user, kiểm port, CURRENT_USER và @@datadir trước khi chạy.
DB_URL/config cache riêng được cô lập và biến môi trường process được phục hồi khi xong.
Chưa có web server MySQL mới; web demo cổng 8020 tiếp tục SQLite.
Không chạy migrate:fresh trên dev hoặc nhập tên DB demo vào lệnh test.

Khi instance đã dừng, khởi động lại bằng binary MySQL đã cài (đường dẫn không có khoảng trắng ở máy này):

```powershell
$mysqlBase = 'C:/VoMinhHoang/laragon/bin/mysql/mysql-8.4.3-winx64'
$localRoot = (Resolve-Path '.setup-cache/mysql-local').Path.Replace('\','/')
Start-Process -FilePath "$mysqlBase/bin/mysqld.exe" -WindowStyle Hidden -ArgumentList @(
  '--no-defaults', "--basedir=$mysqlBase", "--datadir=$localRoot/data",
  '--port=3307', '--bind-address=127.0.0.1', '--mysqlx=0', '--skip-log-bin',
  "--log-error=$localRoot/server.log", "--pid-file=$localRoot/server.pid"
)
./scripts/mysql-local.ps1 -Action Status
```

Chỉ khởi động khi instance riêng đang dừng; xem server.log nếu port bận.
Không initialize lại data đã có. Muốn dừng đúng instance này, sau khi kiểm Status,
dùng mysqladmin với `--defaults-file=<đường dẫn tuyệt đối root.cnf> shutdown`.
Không dừng service MySQL khác bằng tên process chung.

## Backup

Tạm ngừng thay đổi schema/ghi ứng dụng trong lúc tạo backup quan trọng.
Helper gọi mysqldump với single-transaction/quick/utf8mb4/no-tablespaces,
không dùng --databases nên dump không có CREATE DATABASE/USE database nguồn.
Không truyền mật khẩu trên command line; dùng client options file cục bộ.

```powershell
./scripts/mysql-local.ps1 -Action Backup
# Hoặc chọn đường dẫn chưa tồn tại:
./scripts/mysql-local.ps1 -Action Backup -BackupFile '.setup-cache/mysql-local/backups/before-integration.sql'
```

Mặc định tạo SQL có timestamp tại .setup-cache/mysql-local/backups, không ghi đè tệp cũ,
hiện SHA256 để đối chiếu. Nếu dump thất bại thì coi tệp còn lại là bản lỗi, không restore.
Không sửa SQL gốc để đổi tên DB. Không commit SQL, env hay client config.

SQL chỉ là database. Để phục hồi cả ứng dụng, cần cùng commit/migration, bản sao
storage/app/public cho upload và cấu hình/APP_KEY lưu riêng an toàn; không gộp secret vào Git.
Thư mục MySQL data không phải bản backup logic có thể copy nóng tùy ý.

## Restore an toàn

Chỉ nhập backup do nhóm biết nguồn vào database restore RỖNG; helper dùng restore user
không có quyền lên database dev/test. Không dùng root để vượt guard.
Trên máy này restore đã được thử và có dữ liệu, nên chạy lại sẽ bị từ chối có chủ đích.

```powershell
./scripts/mysql-local.ps1 -Action Restore -BackupFile '.setup-cache/mysql-local/backups/before-integration.sql'
```

Để diễn tập lần nữa, nhờ người quản lý instance chuẩn bị một môi trường restore rỗng mới
và điều chỉnh helper qua PR; không tự xóa bảng hiện có để vượt cảnh báo.
Nếu import lỗi, giữ nguyên dev, kiểm restore DB/log, không coi import một phần là thành công.

Sau import, kiểm:
- Danh sách bảng và migration đúng commit nguồn.
- Số lượng user theo role và bản ghi nghiệp vụ quan trọng.
- So dữ liệu đầy đủ khi có thể (dump dữ liệu cùng tùy chọn, thứ tự theo PK, bỏ comment thời gian;
  so hash/bytes cục bộ, không đưa password hash vào log).
- Upload tồn tại, đăng nhập/quyền trên bản phục hồi riêng sau khi chuẩn bị app config phù hợp.
Chỉ sau review mới lập kế hoạch phục hồi DB dùng chung; helper này không ghi đè dev.

## Chuẩn bị máy khác

Cài MySQL 8.x/PHP pdo_mysql; các tệp local của máy này không nằm trong repo.
Tạo instance loopback 3307, datadir <repo>/.setup-cache/mysql-local/data, không dùng data cũ.
Có thể initialize datadir mới bằng mysqld --no-defaults --initialize-insecure với basedir/datadir
đã kiểm; ngay sau khi khởi động phải đặt mật khẩu root ngẫu nhiên bằng kết nối cục bộ.
Chỉ áp dụng khi đường dẫn data chưa tồn tại, không dùng thao tác này để sửa một DB đang có.

Qua MySQL client của instance riêng, dùng mật khẩu riêng thay các placeholder bên dưới:

```sql
CREATE DATABASE toyota_showroom_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE toyota_showroom_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE toyota_showroom_restore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'toyota_dev'@'127.0.0.1' IDENTIFIED BY '<unique-dev-password>';
CREATE USER 'toyota_test'@'127.0.0.1' IDENTIFIED BY '<unique-test-password>';
CREATE USER 'toyota_restore'@'127.0.0.1' IDENTIFIED BY '<unique-restore-password>';
GRANT ALL PRIVILEGES ON toyota_showroom_dev.* TO 'toyota_dev'@'127.0.0.1';
GRANT ALL PRIVILEGES ON toyota_showroom_test.* TO 'toyota_test'@'127.0.0.1';
GRANT ALL PRIVILEGES ON toyota_showroom_restore.* TO 'toyota_restore'@'127.0.0.1';
```

Lưu client options file riêng cho từng role (UTF-8, không BOM), định dạng:

```ini
[client]
host=127.0.0.1
port=3307
protocol=TCP
user=toyota_dev
password=<unique-dev-password>
```

Lưu `local.json` ngoài Git với host=127.0.0.1, port=3307, mysql_bin là đường dẫn
thư mục bin tuyệt đối; passwords chứa dev/test/restore/demo_password trùng giá trị
đã tạo (demo_password ít nhất 8 ký tự). Không copy root/password máy khác.
Ví dụ cấu trúc, tất cả giá trị placeholder cần thay:

```json
{
  "host": "127.0.0.1",
  "port": 3307,
  "mysql_bin": "C:/path/to/mysql/bin/",
  "passwords": {
    "dev": "<unique-dev-password>",
    "test": "<unique-test-password>",
    "restore": "<unique-restore-password>",
    "demo_password": "<local-only-account-password>"
  }
}
```

Chạy Status trước Migrate/Test. Không chia sẻ local.json/client config/SQL thật qua PR.
Nếu cần MySQL 8.0 đúng bản CI, chuẩn bị instance riêng tương tự và chạy lại; không hạ phiên bản
trực tiếp trên datadir 8.4 đã tạo.

## Bằng chứng ngày 20/10

- MySQL 8.4.3 thật: 35 tests/328 assertions pass, gồm 12 Auth + 8 admin users.
- Dev migrate/seed thành công: 17 bảng (kể cả bảng Laravel), 12 migration, 6 user theo role.
- Dump `verified-dev-20261020.sql` và restore vào toyota_showroom_restore thành công.
- So toàn bộ dữ liệu INSERT từ dump dev/restore cùng tùy chọn: giống nhau.
- Test user đọc dev bị từ chối; helper restore DB không rỗng bị từ chối.
- Bằng chứng cục bộ: .setup-cache/mysql-local/VALIDATION.txt, ngoài Git.
- Chưa kiểm hai kết nối cạnh tranh, WebSocket/job, nghiệp vụ catalog/chat/lịch hoặc đăng nhập
  trình duyệt trên restore. Suite HTTP không chứng minh session driver database ngoài trình duyệt,
  vì phpunit dùng SESSION_DRIVER=array.
