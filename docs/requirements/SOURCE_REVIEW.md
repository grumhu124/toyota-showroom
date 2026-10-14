# Rà soát bản nghiệp vụ v5

Bản mới mô tả đủ module, vai trò Hoàng/Đạt, chat và lịch hẹn để dựng khung.
Các chỉnh lý dưới đây nhằm có một cách triển khai thống nhất. Không sửa file Word gốc.

| Điểm trong nguồn | Xử lý trong bộ khung và đặc tả |
| --- | --- |
| Gọi 8 bảng nhưng liệt kê activity_logs | Ghi đúng 8 bảng cốt lõi + 1 bảng audit, ngoài bảng kỹ thuật Laravel |
| client_msg_id ở sơ đồ và client_message_id ở validation | Dùng client_message_id mọi nơi; unique với sender_id |
| access_version dùng cho kênh nhưng thiếu trong schema | Thêm inquiries.access_version mặc định 1 |
| reserved có trong enum, thiếu luồng | Admin đánh dấu thủ công theo thực tế; lịch/chat không giữ xe |
| 3 form/phút ở middleware và 3/10 phút ở luồng/test | Mặc định 3/10 phút/IP cho từng endpoint form |
| Đổi kênh được mô tả như tự đóng socket cũ | Ngừng phát tin mới vào version cũ, từ chối auth cũ; dữ liệu đã nhận không thể thu lại |
| Chống chồng giờ ±60 phút chưa rõ ranh giới | Khoảng 60 phút nửa mở; khóa user trước kiểm tra để chặn cả hai giao dịch đầu tiên |
| BROADCAST_DRIVER trong checklist | Laravel 12 dùng BROADCAST_CONNECTION; cấu hình khớp framework cài thực tế |
| PHP route pseudocode như code thực thi | Chỉ là minh họa; repo dùng withRouting và middleware alias Laravel 12 |
| Tất cả Blade của Đạt nhưng admin/staff giao TV3/4/5 | Đạt quản layout/components và UI khách; module owner làm admin/staff trên layout chung |
| Schema appointments có note, code khác dễ dùng notes | Dùng note thống nhất; migrations/model là nguồn trường thực thi |
| Năm xe max 2030 cố định | Giới hạn dựa năm hiện tại theo FormRequest, không tạo logic hết hạn |

Về điểm số: Word dùng trọng số 40/25/15/10/10 và nói đủ 9/10. Đề cương v1.2
đã cung cấp trước đó ghi Rubric 2 là giao diện 30%, chức năng 40%, hình thức
báo cáo 10%, nội dung báo cáo 10%, tham gia nhóm 10%. Vì chưa có bảng chấm mới
của giảng viên, repo không công nhận các phần trăm tự đặt trong Word là rubric chính thức.
Nhóm xác minh yêu cầu giảng viên; dùng checklist để kiểm tra chất lượng, không hứa điểm.

Bộ khung chưa triển khai các controller CRUD hoặc chat end-to-end. Không dùng
sự tồn tại của migration/model/config để đánh dấu nghiệp vụ đã hoàn thành.
