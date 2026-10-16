# Trang chủ Camry

Bản cuối dùng lại camry-exploded.mp4 (clip tách nền do người dùng cung cấp),
canvas lọc nền đen. Clip gốc giữ lại để đối chiếu nhưng không được render.
Người dùng đã thử khung video giữ nền rồi chọn quay lại bố cục trong suốt.
Mốc endTime hiện 8, tốc độ 11 theo người dùng tự chỉnh; không đổi các giá trị này.

Gió mới: ba path SVG cong với stroke-dashoffset chuyển động, thay vệt thẳng mờ.
Chữ CAMRY bạc đặc không text-shadow để rõ nét. Không giả hiệu ứng quay bánh.

Atmosphere: gió SVG/vệt đường CSS loop chậm khi xe nguyên; opacity theo --camry-shift
giảm khi tách xe, scanner tăng dần. Không dịch chuyển ảnh/bánh hoặc tua video tự động.
Các lớp aria-hidden/pointer-events none nằm sau xe, không che CTA/nội dung.
IntersectionObserver/visibilitychange tạm dừng animation khi hero/tab không hiển thị.
Giữ nguyên các giá trị endTime/tốc độ do người dùng tự chỉnh trong home.js.

Khung dừng hiện tại: 8 giây, tốc độ progress * 11 do người dùng tự chỉnh.
Phần cuối giữ hình rồi thoát sticky; cuộn lên tua ngược.

Điều chỉnh theo phản hồi người dùng: hero/header riêng home dùng nền xanh đen,
glow xanh băng, chữ Camry bạc đặc rõ nét, sàn ellipse và grid nhẹ; dịch vụ bên dưới giữ sáng.
home-tech.css giới hạn scope; không đổi ảnh, hiệu ứng scroll hoặc trang Dòng xe.

Preview local/testing ở /, Figma 22:43. Ảnh mở đầu tái sử dụng đúng asset 99cfd.png.
Blade layout chung, CSS responsive, JS thuần. Không thêm dependency/DB/schema.
Các mục dịch vụ và showroom theo Figma; CTA chưa có backend dùng dialog rõ ràng.

Video người dùng: video_xe_khong_nen.mp4, H264 1280x720, 10.03s, khoảng 2.6MB.
Video thực tế nền đen, không alpha. Canvas 960x540 loại vùng gần đen liên thông
biên khung hình; không loại mọi pixel tối trong xe. Đây là giải pháp preview,
có thể còn viền tối; nguồn alpha chính thức là lựa chọn tốt hơn sau này.
Ảnh/video mô phỏng không xác nhận thông số Toyota. Không dùng ảnh nội thất giả.

Scroll map sang currentTime, seeked vẽ khung hình và đuổi theo đích mới nhất.
Clip tải thành Blob local trước để tua được trên php artisan serve (server này
trả 200 cho Range, khiến seekable có thể [0,0]); không cần endpoint media riêng.
Video tắt tiếng, không phát autoplay; sticky hữu hạn rồi tiếp tục dịch vụ/showroom.
Không vẽ/seek liên tục khi không scroll. Theo hướng hiệu ứng luôn bật đã chốt,
chuyển động chỉ do người dùng chủ động cuộn, không autoplay/không bộ chọn.
Lỗi decode/canvas giữ poster và báo thay thế. Canvas không vào cây accessibility.

Đổi header/footer logo về trang chủ và link dịch vụ/showroom thành anchor thật.
Giữ /xe và chi tiết xe, không merge PR/auth hay triển khai đặt lịch/tư vấn backend.
