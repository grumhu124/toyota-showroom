# Đặc tả triển khai thống nhất

Yêu cầu chính đề xuất cho vòng v6 là [SRS 1.3](../../SRS.md), từ Word v6 ngày
16/10/2026. Xem [ADR 0002](../decisions/0002-v6-srs-review.md) và
[kết quả review](REVIEW_V6_SRS.md). Có hiệu lực chung sau review/gộp PR.
File này là chỉ mục/tóm tắt, không giữ bộ luật thứ hai khác SRS.

Một showroom Toyota, giá công bố, tư vấn/chat và lịch xem/bảo dưỡng.
Mua bán/giấy tờ/thanh toán ngoài hệ thống. Không tự thêm checkout, nhiều hãng,
OTP, chatbot hoặc phí lăn bánh vào phần bắt buộc.

- customer/sales/admin; guest gửi form, không chat/tra hồ sơ chỉ bằng phone.
- Inquiry new → assigned → in_progress → closed; reopen assigned với sales active.
- New/closed chỉ đọc; form lưu nhu cầu ban đầu trước phân công.
- UUID cùng payload trả tin cũ; khác payload 409; sau commit mới phát.
- Reconnect subscribe thành công trước catch-up và ghép ID.
- Lịch khung tiếp nhận 60 phút, chênh đúng 60 hợp lệ; khóa appointment và sales.
- Sold không nhận tư vấn mới/lịch; reserved không sinh từ đặt lịch.
- Chi tiết xe ID, category slug. Form 3/10 phút/IP/endpoint; phone demo 10 chữ số.
- Đích 10 bảng, migration thật vẫn 9; chưa tự triển khai khi tải tài liệu lên Git.

Xem DATA_MODEL, CONTRACTS, FEATURE_STATUS để đối chiếu mã. Các rule v5 trái SRS
(chat new, reopen in_progress, URL slug, chuẩn hóa +84) chỉ còn là lịch sử ADR 0001.
ID FR/BR/D và rule chi tiết dùng SRS; mọi thay đổi cần tài liệu/PR tương ứng.
