# Đặc tả triển khai thống nhất

Nguồn: business-flow-v5.docx ngày 13/10/2026, bản chụp ở docs/source.
Các điểm mâu thuẫn được xử lý ở ADR 0001 và SOURCE_REVIEW.md.
Đây là hợp đồng triển khai ban đầu; thay đổi phải cập nhật PR và tài liệu.

## Phạm vi và actor

Một showroom xe mới; khách xem giá công bố, tư vấn/chat và đặt lịch.
Giá chưa gồm tổng lăn bánh. Không có giỏ hàng, đặt cọc/thanh toán online,
hợp đồng, hóa đơn, trả góp, nhiều showroom, so sánh hoặc bộ tính lăn bánh bắt buộc.
Khách vãng lai vẫn là actor. Chat và lịch sử cá nhân cần tài khoản; tự đăng ký
chỉ có role customer. Admin tạo/khóa sales, phân công và quản trị.

## Catalog

GET /xe: q tối đa 100 ký tự; category dùng slug; status available/reserved/sold;
price_min/price_max không âm và min <= max; sort newest/price_asc/price_desc;
12 xe/trang, giữ bộ lọc và thứ tự khi phân trang. Cả car và category phải visible.
Xe ẩn trả 404 ở URL công khai. available cho tư vấn và đặt lịch; reserved/sold
vẫn xem được, không đặt lịch. reserved chỉ admin đánh dấu dựa trên thực tế ngoài đời.

## Tư vấn

Form cần tên, số điện thoại, nội dung 10–2000 ký tự; email và xe quan tâm tùy chọn.
Khách đã login dùng customer_id từ phiên, khách vãng lai để null.
Tạo new → admin giao sales thành assigned → sales bắt đầu thành in_progress
→ đóng thành closed, cần close_result. Admin/sales được giao có thể mở lại in_progress.
Ghi chú nội bộ không trả cho customer. Không xóa yêu cầu/tin nhắn đã có lịch sử.
Biểu mẫu tư vấn và lịch hẹn: 3 yêu cầu/10 phút/IP; mỗi endpoint có limiter riêng.
Đây là mặc định đồ án, sau này điều chỉnh có kiểm thử và quyết định ghi nhận.

## Chat

Customer đăng nhập tạo/mở inquiry của mình. Khi chưa có người phụ trách vẫn
lưu tin và báo chờ tiếp nhận; admin phân công, không tự hứa nhân viên luôn online.
Gửi HTTP POST có auth/CSRF; tin 1–1000 ký tự; client_message_id là UUID.
DB unique(sender_id, client_message_id); gửi lại cùng nội dung trả bản ghi cũ,
dùng lại UUID với payload khác phải từ chối. Sau commit mới broadcast.
Kênh private inquiry.{id}.v{access_version}; xác thực active + sở hữu/phân công/admin.
Đổi sales/khóa user tăng version; event mới dùng version hiện tại khi phát.
Khi nối lại: tham gia kênh rồi tải tin thiếu, ghép theo message ID để tránh khoảng mất tin.
Tải lại giữ lịch sử. Yêu cầu closed chỉ xem; tạo mới hoặc mở lại theo luồng hợp lệ.
Mục tiêu demo hai phiên là tin xuất hiện dưới 2 giây trong mạng ổn định.

## Lịch hẹn

viewing cần car available và car/category visible; maintenance cần service visible,
allow_booking và customer_car_info. Tên/liên hệ bắt buộc; requested_at ở tương lai,
trong 6 tháng; note tối đa 500 ký tự. Không yêu cầu đã được báo giá hoặc tư vấn trước.
pending → confirmed → completed. pending/confirmed được cancelled; confirmed
có thể no_show sau giờ hẹn khi khách không đến. Đổi giờ confirmed trở lại pending,
lưu giờ cũ/lý do; completed/cancelled/no_show kết thúc, gửi yêu cầu mới nếu cần.
Khung tiếp nhận 60 phút [bắt đầu, kết thúc); hai lịch sát đầu/cuối không chồng.
Xác nhận transaction khóa hàng user sales, rồi kiểm tra mọi lịch confirmed overlap,
bao gồm trường hợp chưa có lịch nào. Không chỉ khóa các lịch đang tồn tại.
Nhân viên trao đổi/xác nhận giờ thực tế; website không ấn định thời lượng sửa xe.
Gửi lịch không giữ xe; hủy lịch không tự đóng inquiry hoặc đổi status xe.

## Quản trị và an toàn

CRUD dòng xe, xe/ảnh, dịch vụ; quản lý sales; phân công inquiry/lịch; xem audit log.
Dữ liệu có tham chiếu chỉ ẩn/khóa; dòng xe ẩn làm xe thuộc dòng mất khỏi public.
Ảnh tối đa 10, mỗi ảnh 5MB, jpg/jpeg/png/webp; server kiểm tra MIME và đặt tên.
Price VND dạng decimal(15,0); năm hợp lệ theo năm hiện tại, không hardcode 2030.
Phone nhận số VN 10 chữ số bắt đầu 03/05/07/08/09, chuẩn hóa +84 trước validation.
Không dùng role trong request khách, không nối hồ sơ vãng lai vào account qua phone chưa xác minh.
Auth login limiter tối đa 5 lần thất bại/phút theo IP + tài khoản chuẩn hóa;
khóa user phải vô hiệu hóa phiên đăng nhập và phiên nhận tin mới.

## Giao diện và nghiệm thu

Blade + Tailwind 4; hướng hiện đại, tối giản, ảnh xe lớn, một bộ màu thống nhất.
Không trộn Bootstrap. Layout app/admin/auth; component card, badge, alert,
gallery và phân trang. Cần trạng thái loading/empty/error/success/disabled.
AJAX filter là nên có sau khi tìm/lọc server ổn định; WebSocket là bắt buộc.
Kịch bản kiểm thử ở docs/testing/ACCEPTANCE.md. Điểm mục tiêu không phải bảo đảm.
