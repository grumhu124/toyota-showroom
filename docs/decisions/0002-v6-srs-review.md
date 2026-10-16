> Lịch sử rà soát v6/SRS 1.3. Phạm vi hiện hành dùng SRS 1.4, COURSE_SCOPE và ADR 0003; không tự triển khai phần đã chuyển P2.

# ADR 0002 Rà soát v6 và SRS

Ngày 2026-10-16. Đề xuất trong PR tài liệu để nhóm review.
ADR 0001 vẫn ghi nền cũ; sau gộp PR này dùng SRS 1.3 cho các yêu cầu đích trái v5.

Giữ PHP 8.3/Laravel 12/MySQL 8/Blade/Tailwind 4/Reverb/Echo. Tiếp nhận chỉ Toyota,
đích 10 bảng có inquiry_notes, code/closed_at, service other, route xe ID,
chat chỉ assigned/in_progress và reopen assigned. Phone demo theo v6, limiter form IP+endpoint.
Chỉnh lý D-18: sau commit, enum/active auth, UUID cùng payload, subscribe trước
catch-up và thông báo version riêng user; khóa appointment và sales; ảnh nháp.
Giữ default is_visible=false như nền: lưu nháp không tự công khai xe thiếu ảnh.

Không cascade xóa lịch sử tư vấn. Giữ contact_email và cột slug/source đã có.
Migration/model/enum/route thay ở PR module sau với backfill và test; không sửa
migration đã dùng chung hoặc bỏ dữ liệu để làm tài liệu trông như khớp.
SRS là yêu cầu đích, DATA_MODEL/FEATURE_STATUS giữ hiện trạng thực tế.
Word và SRS trước review lưu hash; v5 vẫn là lịch sử, không bị ghi đè.
Điểm mục tiêu/trọng số tự đặt không thay rubric giảng viên hoặc kiểm thử sản phẩm.
