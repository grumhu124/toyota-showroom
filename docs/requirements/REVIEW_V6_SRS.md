# Rà soát v6 và SRS ngày 16 tháng 10 năm 2026

Đã đối chiếu Word v6, SRS 1.2 và mã nền c87c255. Giữ nguyên Word, lưu bản SRS
gửi ban đầu; bản rà soát thành 1.3. Đây là review nội dung, không nghiệm thu bố cục Word.

Bản mới rõ hơn về ID yêu cầu, mã tư vấn, lịch sử ghi chú và trạng thái.
Một showroom Toyota, giao dịch ngoài đời và chat WebSocket phù hợp phạm vi đang làm.
Có thể dùng làm yêu cầu sau chỉnh lý; tài liệu không chứng minh sản phẩm đã hoàn thành.

| Điểm cần xử lý trước triển khai | Chỉnh lý |
| --- | --- |
| Header v5/1.1 dù nguồn mới v6; framework 11+ còn mở | SRS 1.3, v6, giữ PHP 8.3/Laravel 12/Tailwind 4 |
| Đích 10 bảng nhưng mã 9, chưa có code/closed_at/InquiryNote, brand còn required | Bảng gap SRS mục 12, không tự đổi DB/migration |
| Ví dụ role so string không khớp enum, thiếu active | Sửa minh họa; baseline channel vẫn từ chối mặc định |
| UUID lookup có thể trả nhầm inquiry/payload; toOthers không đủ dedupe | So payload, 409 nếu sai khác, unique DB, sau commit |
| Fetch trước subscribe có khe mất tin; đổi version thiếu cách báo client | Subscribe trước catch-up, thông báo điều khiển riêng user |
| Chỉ khóa sales chưa chặn ghi đè cùng appointment | Khóa lịch, đọc lại trạng thái/quyền rồi khóa sales |
| Quy tắc đúng một ảnh nhưng ảnh được để trống | Cho lưu nháp, công khai cần ảnh chính, quy tắc xóa ảnh |
| New không chat nhưng chưa nói khởi tạo nhu cầu | Form là nội dung đầu, UI chờ phân công |
| 50M thiếu khoảng chừa multipart; npm install khó đồng bộ | Gợi ý 64M/max_file_uploads, npm ci theo lock |
| Spec/contracts cũ trái route/reopen/phone của v6 | SRS là hợp đồng chính sau review; ADR 0002 ghi thay đổi |

Các vấn đề ví dụ kỹ thuật là rủi ro nếu sao chép, không phải lỗ hổng đang chạy
của baseline. Mã nghiệp vụ chưa triển khai. Source Word có 16 kịch bản, SRS gửi
có 20; giữ ID và thêm 6 kiểm tra cho các chỉnh lý thành 26 tình huống kế hoạch.

Giữ lựa chọn v6: chỉ Toyota, route ID, sold không nhận tư vấn mới, reserved admin
cập nhật theo thực tế, reopen assigned, điện thoại demo 10 số. Ghi chú nhiều dòng
là lựa chọn mới hữu ích; nền cũ đã lưu được một internal_note.
Ma trận điểm 40/25/15/10/10 và câu đủ 9/10 trong Word không phải cam kết điểm;
nhóm xác nhận rubric giảng viên như lưu ý SOURCE_REVIEW.md.

Không đổi app/migrations/models/enums/bootstrap/dependency. Chỉ công cụ inventory
thêm hai tài liệu gốc; các khoảng cách triển khai phải xử lý qua PR module có test.
