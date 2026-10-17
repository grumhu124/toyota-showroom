# Trang Dòng xe xem trước

Trang chi tiết /xe/1 đến /xe/9 dùng car-details-preview.php, tái sử dụng ảnh
hiện có. Chỉ có một ảnh mỗi xe, nút phóng to mở dialog và đóng bằng Escape.
Không giả ảnh nội thất/góc khác. Thông số kỹ thuật chưa có ghi đang cập nhật.
Camry available; Land Cruiser reserved; Innova sold để kiểm tra UI trạng thái.
Card và hero nối sang ID fixture; giá/tình trạng/thông tin vẫn là minh họa.
Nút tư vấn/đặt lịch chỉ mở thông báo chưa kết nối, không ghi dữ liệu.

Mở /xe ở môi trường local/testing. Dựng theo Figma QD7Z9HRR58H4VwI1ebysKU,
node 22:44; ba trạng thái slider 22:44, 22:45, 32:130.
Blade/Tailwind hiện có, CSS responsive và JavaScript thuần; không thêm dependency.
Layout app và component car-card có thể tái sử dụng. Controller Catalog thật chưa có.

Fixtures ở resources/data/collection-preview.php chỉ phục vụ UI, không seed DB.
Giá và tình trạng là minh họa, không là báo giá Toyota. Bộ lọc/sort xử lý
sáu fixture sản phẩm trên trình duyệt, giữ query khi reload; chưa phải tìm kiếm database.
Các chức năng chưa triển khai mở dialog thông báo, không gửi dữ liệu/không báo thành công.
Mặc định chỉ có một trang kết quả; chưa triển khai pagination backend.

Xe dịch ngang 1300ms; tên dòng xe vào từ dưới/ra trên độc lập, không tự chạy/không quay vòng;
Theo yêu cầu mới: hiệu ứng luôn bật 1300ms khi bấm chuyển, không có nút chọn
và không đọc localStorage/reduced-motion cho slider. Hỗ trợ bàn phím và swipe.
Ảnh hero tối đa 620px, mobile 84% chiều rộng để hạn chế phóng lớn ảnh nguồn.
Card 3 cột desktop theo ảnh tham khảo người dùng, dùng bộ xe-san-pham; xem CATALOG_CARDS.md.
Font Be Vietnam Pro lưu local; nguồn Google Fonts, giấy phép OFL đi kèm.
Ảnh xuất từ Figma người dùng, tải ngày 16/10/2026, chỉ dùng demo theo thiết kế:
corolla 4e1a5.png; vios 2a875.png; innova e97f7.png; camry 99cfd.png;
land-cruiser 2fa1f.png. Không dùng screenshot nguyên trang làm giao diện.

TV3 thay fixtures bằng dữ liệu public đã kiểm visible/status; thống nhất route tên
trước khi nối chi tiết. TV4/TV5 nối tư vấn/đặt lịch; không lấy disabled UI làm phân quyền.
Main chưa có PR #1/#2; không thay các bản SRS chung hoặc merge thay nhóm.
