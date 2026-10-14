# Kịch bản nghiệm thu

Bảng này là kế hoạch kiểm tra, không phải kết quả test đã chạy.
Ghi kết quả và PR minh chứng khi module hoàn thành; FEATURE_STATUS phải khớp.

| ID | Tình huống | Kết quả cần đạt |
| --- | --- | --- |
| CAT-01 | Tìm/lọc/sắp xếp và phân trang | Dữ liệu đúng, giữ query, empty state |
| CAT-02 | Ẩn category hoặc biết URL xe ẩn | Không có ở public, URL trả 404 |
| CAT-03 | Xóa xe đã có inquiry/lịch | Từ chối xóa; vẫn ẩn được và giữ lịch sử |
| AUTH-01 | Đăng ký gửi role admin | Vẫn customer; email unique; password hashed |
| AUTH-02 | Khóa user đang login | HTTP bị chặn và không nhận được tin mới |
| INQ-01 | Form thiếu phone hoặc gửi lần 4/10 phút | 422 hoặc 429; không tạo bản ghi sai |
| INQ-02 | Giao A rồi chuyển B | B thấy lịch sử, A mất quyền HTTP/kênh mới |
| CHAT-01 | Hai phiên gửi nhận | Tự cập nhật dưới 2 giây ở mạng demo ổn định |
| CHAT-02 | Reload, mất mạng, retry cùng UUID | Đủ lịch sử, không trùng, payload khác bị chặn |
| CHAT-03 | Đoán ID/version của người khác | Auth kênh và HTTP bị từ chối |
| APT-01 | Xe sold/reserved/ẩn hoặc service không booking | Không tạo/xác nhận lịch sai |
| APT-02 | Hai confirmation đồng thời cùng sales | Chỉ một thành công; chạy hai kết nối MySQL |
| APT-03 | Hai lịch đầu/cuối sát nhau | Không overlap nếu cách đúng 60 phút |
| APT-04 | Đổi, hủy, complete, no_show | Đúng chuyển trạng thái, ghi lý do, không tự giữ xe |
| AUD-01 | Quản trị thay dữ liệu | Có action/actor/target; log không chứa secret/PII đầy đủ |
| UI-01 | Mobile/tablet/desktop và lỗi/empty/loading | Không tràn ngang, điều khiển dùng được |
| SET-01 | Checkout trên máy khác | Cài lockfiles/migrate/seed/build được theo README |

PHP tests nền dùng SQLite; CI chạy thêm MySQL. Test khóa hàng/race condition
cần hai giao dịch/connections thật, không kết luận từ test tuần tự hoặc mock DB.
Chat cần demo trình duyệt hai session độc lập, không chỉ unit test Event.
