# ADR 0003 — Ưu tiên đồ án Toyota theo đề cương

Ngày: 16/10/2026. Trạng thái: đề xuất trong PR, thực hiện theo chỉ đạo mới của người dùng.

## Bối cảnh

Người dùng yêu cầu bám nội dung/thang điểm môn học, kiểm ràng buộc trước code và
chỉ mở rộng sau phần môn học. SRS 1.3 chính xác hóa nhiều chi tiết nhưng phạm vi
vẫn nặng; Word v6 có trọng số tự đặt và cam kết điểm không có căn cứ.

## Quyết định

SRS 1.4 + COURSE_SCOPE thay chỉ dẫn phạm vi trái chúng trong v5/v6/SRS 1.3/ADR 0002.
Giữ bản 1.3 nguyên trong docs/source; Word v6 không sửa.
Giữ 9 bảng nền, internal_note; chưa thêm inquiry_notes, code/closed_at, other hay bỏ brand.
Giữ chat Reverb văn bản theo cam kết nhóm cùng các yêu cầu an toàn/retry/catch-up.
Kênh control users.{id} tự chuyển liền mạch là P2; P1 có thể cần tải lại để nhận
quyền mới, vẫn phải chặn tin mới về sales cũ/tài khoản khóa.
P1 chỉ quản trị/tiếp nhận/lịch và chat, không làm mua bán/kế toán/xưởng.
Khách tự đổi lịch, mở lại tư vấn, lịch sử ghi chú và UI audit sang P2.
Giới hạn login thống nhất 5 thất bại/phút/IP+email, hợp hướng limiter nền;
chưa được coi limiter đếm request hiện có là chức năng login đã hoàn thành.
Cars là từng chiếc mã riêng; không dùng sold để kết luận cả model hết hàng.

## Hệ quả

Bớt migration/màn hình không thiết yếu; không giảm kiểm quyền và tính đúng dữ liệu.
Không xóa code/bảng nền hoặc sửa migration đã merge. Chỉ tài liệu thay trong vòng này.
P1 có demo/checklist, báo cáo, UI và minh chứng nhóm trước P2. Không bảo đảm điểm.
Bản kiểm tra nền vẫn không nghiệm thu chức năng chưa được triển khai.
