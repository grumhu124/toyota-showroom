# ADR 0004 — Code-first và hợp đồng làm song song

Ngày 16/10/2026. Đề xuất trong PR; không thay phạm vi P1/SRS 1.4.

## Bối cảnh

Nhóm cần làm song song, review chéo và tránh đổi DB/route khác nhau.
Rà soát thấy CI băm CRLF/LF sai, prefix customer trái URL SRS,
hướng dẫn môi trường chưa cập nhật H01, và phân công chưa quy định điểm giao nhau.

## Quyết định

Migrations versioned là nguồn tạo/thay schema; Models/casts/relationships là cách
truy cập dữ liệu. Không sinh schema tự động từ Model, không coi phpMyAdmin là nguồn chuẩn.
Thay DB sau khi chia sẻ dùng migration mới, PR có tác động/backfill/rollback.
Giữ 9 bảng P1; không làm lại schema, không thêm kho/VIN/thuế/phí.
Giữ monolith/session/Blade/Reverb; không JWT hoặc thêm framework cho từng người.

Customer URL ở root (/yeu-cau,/lich-hen), route name vẫn customer.*.
Staff/admin giữ URL prefix; namespace route chia module.
PARALLEL_WORK quy định shared files, fixture, review và điểm bàn giao.
Khóa user/inquiry/lịch cần thứ tự chung và thử cạnh tranh MySQL lúc triển khai.

Inventory format 2 băm text sau chuẩn hóa CRLF→LF, binary giữ byte gốc.
Đây là sửa kiểm tra, không bỏ kiểm nội dung; có regression test.
Source Word không bị đổi; hash raw nguồn cũ trong README là hash lúc tiếp nhận,
không phải cam kết mọi text file giữ byte CRLF sau Git checkout.

## Hệ quả

Có thể chia việc ngay với fixtures; không buộc đợi CRUD/Auth UI module khác.
Vẫn cần review và kiểm thử tích hợp; không hứa không bao giờ có lỗi.
P1 chưa xong; H02, catalog, inquiry/chat, lịch và demo/báo cáo vẫn còn việc.
