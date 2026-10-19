# H03 — Rà mã catalog TV3 ngày 20/10/2026

## Phạm vi và kết luận

Rà tĩnh commit `44dee2edddbfbaca856507165e4cc835b47ca410` của
`feature/car-crud-search`; đối chiếu AUTHORIZATION_CONTRACT và SRS 1.4.
Không đổi mã TV3, không ghép lại catalog vào main hoặc bản demo.
Chưa chạy suite catalog trong lượt này; không dùng kết quả suite demo làm bằng chứng catalog.
CI chưa được xác nhận tại đúng SHA này.

Chưa đề xuất nghiệm thu H03-CAT: cần sửa các mục dưới và chạy lại trên DB test riêng.
Đây là checklist review cho owner, không phải yêu cầu bổ sung nghiệp vụ P2.

## Các điểm cần sửa

### [P1] after validation vẫn truy vấn category_id sai kiểu

Tệp `app/Http/Requests/Catalog/CarRequest.php`, phương thức `after()`:
gọi `CarCategory::find($this->input('category_id'))` rồi đọc `$category->is_visible`
mà chưa kiểm lỗi/kiểu của category_id.

Payload `category_id[] = 1` bị rule integer từ chối nhưng callback after vẫn chạy.
find nhận mảng trả collection; đọc is_visible trên collection phát sinh exception,
thay vì phản hồi validation. Đây là suy luận trực tiếp theo mã/framework, chưa tái hiện HTTP.
Đầu vào chuỗi không phải số cũng cần kiểm trên MySQL để tránh lỗi ép kiểu SQL.

Đề nghị: return sớm nếu category_id đã có lỗi; chỉ truy vấn sau khi chắc chắn là ID hợp lệ.
Ca kiểm: admin POST/PUT với category_id dạng mảng, chuỗi, null; JSON 422 hoặc web
redirect kèm lỗi, không 500, không tạo/sửa xe.

### [P2] Chỉ nhập giá tối đa thì bộ lọc bị bỏ qua

Tệp `app/Http/Controllers/Catalog/CarsController.php`, đầu `index()`:
khi query có max_price mà không có min_price, request merge max_price thành null.
Vì vậy `/xe?max_price=500000000` không giới hạn giá, dù hợp đồng mô tả hai tham số giá.

Mặt khác min_price có giá trị thì max_price bị required_with bắt buộc nhập.
Cần thống nhất rõ hỗ trợ lọc một đầu hay chỉ cặp; hợp lý với hai ô độc lập hiện tại là
cho nhập một đầu và chỉ so max >= min khi cả hai có giá trị.
Ca kiểm: chỉ min, chỉ max, cả hai, cả hai trống, max < min; kiểm kết quả ID trên MySQL.
Không bỏ qua kiểm SQLite chỉ dựa vào một nhận xét; ghi rõ khác biệt khi có bằng chứng.

### [P2] Xóa xe bỏ lại tệp ảnh upload trên disk

`app/Services/Catalog/SaveCar.php::delete()` chỉ gọi `$locked->delete()`.
FK car_images cascade xóa bản ghi ảnh; Car/CarImage không có hook dọn file.
`StoreCarImage::delete()` có dọn file nhưng không được dùng trong luồng xóa xe.

Xe chưa có hồ sơ được phép xóa; nếu trước đó có ảnh upload, tệp trên public disk còn lại,
không còn bản ghi quản lý. Chỉ xét ảnh upload, không xóa public/assets của seed.

Đề nghị: chụp danh sách đường dẫn thuộc xe, transaction xóa DB, dọn upload sau commit;
nếu dọn lỗi phải có cách phát hiện/thử lại, không xóa file trước khi DB commit.
Ca kiểm Storage::fake: xóa xe không có lịch sử → DB và upload biến mất;
xe có inquiry/appointment → DB và upload nguyên vẹn; seed assets không bị xóa.

## Những điểm đã thấy trong mã

- Admin route nằm trong nhóm auth/active/admin; CarPolicy kiểm active và admin.
- Public list và detail đều kiểm xe visible + dòng visible, kể cả admin ở URL public.
- Xóa ảnh kiểm image.car_id đúng car.id; không chỉ tin ID ảnh từ URL.
- Upload giới hạn JPG/PNG/WEBP, dung lượng/kích thước, tên file UUID do server tạo.
- Khóa hàng xe khi sửa/xóa; chặn xóa xe có inquiry/appointment; FK bảo vệ lịch sử.
- Ghi attributes qua allowlist, enum status, Toyota cố định theo dòng xe.
- Có test nguồn cho role/visibility/ảnh khác xe/xóa xe có inquiry.

Các mục này là bằng chứng mã tồn tại; chưa thay kiểm HTTP, storage thật, CSRF hoặc MySQL.

## Khoảng trống kiểm thử nên đóng cùng bản sửa

- Seeder idempotence hiện có assert `Car::count() === Car::count()`, không kiểm số lượng
  trước/sau. Lưu count xe/dòng/ảnh trước seed lần hai rồi assert bằng nhau.
- Test tên có pagination chưa kiểm trang 2, kích thước trang, query string và tổng kết quả.
- Bổ sung appointment reference khi xóa xe; khóa admin khi đang dùng module catalog;
  mutation CSRF thật ngoài bypass của môi trường test.
- Tái chạy composer check + build trên nhánh catalog và MySQL test riêng;
  không dùng DB demo đang trình bày.
- Review FE cùng Đạt: giữ home/detail links và menu mobile khi thay fixture bằng DB.

## Handoff cho các owner

TV3 sửa module/catalog tests và mở PR từ nền main hiện tại. Vì commit TV3 đã là ancestor
của main nhưng bị revert, chỉ mở lại nhánh cũ có thể không tạo diff mong muốn.
Cần PR khôi phục có chủ đích trên nền main, chứa bản sửa và bằng chứng mới; không merge
lại nhánh cũ rồi mặc định catalog được phục hồi.

Hoàng tiếp tục review quyền/validation/ảnh và nghiệm thu tích hợp sau bản sửa.
TV4/TV5 còn chờ mã endpoint/policy để kiểm H03-INQ/CHAT/APT; không mở kênh giả để demo.
