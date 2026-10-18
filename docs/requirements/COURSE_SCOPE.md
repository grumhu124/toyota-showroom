# Phạm vi môn học và các điều kiện cần chốt

Ngày đối chiếu: 16/10/2026. Tài liệu này cùng SRS 1.4 kiểm soát phạm vi hiện hành.
Mục tiêu là sản phẩm môn học hoàn chỉnh cho nhóm 5 người, định hướng 8+,
không quy đổi số bảng/chức năng thành điểm bảo đảm.

## 1. Căn cứ và giới hạn tham khảo

- Đề cương người dùng cung cấp: LapTrinhMaNguonMo_Decuong_v1.2.pdf;
  trang 4 CLO, trang 5–9 nội dung, trang 10 đánh giá học phần,
  trang 11 quy định nhóm, trang 13–14 Rubric 2.
- [Toyota Việt Nam](https://www.toyota.com.vn/),
  [danh sách xe](https://www.toyota.com.vn/danh-sach-xe),
  [bảng giá được liên kết trên trang chủ](https://www.toyota.com.vn/media/vcdhziej/9-ba-ng-gia-sep-26.pdf),
  [đặt lịch dịch vụ](https://www.toyota.com.vn/dat-lich-hen-dich-vu):
  đọc thông tin công khai, không gửi form thật/không thử giao dịch hoặc hệ thống nội bộ.
- Word v6/SRS cũ: giữ làm lịch sử, không dùng trọng số tự đặt làm rubric chính thức.

Toyota công khai catalog/bảng giá, tiếp nhận liên hệ và có form hẹn dịch vụ.
Bảng giá phân biệt phiên bản/màu; form dịch vụ có thông tin xe, liên hệ, đại lý,
ngày dự kiến và nội dung nhân viên liên hệ xác nhận.
Đây là căn cứ tham khảo cho giới thiệu → tiếp nhận → nhân viên trao đổi.
Không suy ra toàn bộ backend/quy trình đại lý từ các trang công khai.
Một showroom demo không cần hệ thống đại lý toàn quốc, VR, bảo hiểm hay tài chính.
Không khẳng định không có kênh bán online nào khác chỉ từ trang đã xem.

Bảng PDF được trang chủ liên kết có chú thích ngày cũ; tên file không tự chứng minh
mọi mức giá đang hiệu lực. P1 chỉ nhập giá mẫu có nguồn/ngày và nhãn tham khảo,
không tự dùng chú thích đó làm công thức thuế/phí hiện hành.

## 2. Bám đúng rubric đồ án

| Tiêu chí Rubric 2 | Trọng số | Minh chứng nhóm phải chuẩn bị |
| --- | ---: | --- |
| Hình thức báo cáo | 10% | Theo mẫu GV, mục lục/đánh số/chính tả, hình và bảng dễ đọc |
| Nội dung/chất lượng báo cáo | 10% | Phạm vi, actor/use case, ERD, MVC, test và nhận xét hạn chế |
| Giao diện phần mềm | 30% | Trang khách/staff/admin nhất quán, responsive, đủ trạng thái form |
| Sản phẩm phần mềm | 40% | Demo P1 hoạt động, DB thật, CRUD/tìm/lọc/phân trang/quyền đúng |
| Tham gia, tương tác nhóm | 10% | Chia việc 5 người, PR/review/nhật ký, mỗi người hiểu phần mình |

Các mức hoàn thành trong rubric không phải chỉ tiêu tự chọn bỏ ngẫu nhiên 20%
chức năng. Hoàn thành trọn phạm vi GV chấp thuận, giữ chất lượng và minh chứng.
Không có hạng mục riêng “AJAX/sáng tạo 10%” hoặc “kỹ thuật 25%” trong Rubric 2.
Cấp học phần: bài tập 10%, kiểm tra thực hành 40%, đồ án 50%;
bảng trên chỉ đánh giá đồ án, không thay phần thực hành/bài tập.
Nhóm 5 nằm trong quy định 3–5; đề tài tự đề xuất cần GV đồng ý.
Chưa có bằng chứng GV đã duyệt phạm vi/rubric riêng; ghi việc cần xác nhận,
không coi mặc nhiên đã được chấp thuận và cũng không dừng chuẩn bị tài liệu vì việc này.

## 3. Đối chiếu nội dung thực hành

| Nội dung đề cương | Minh chứng áp dụng trong P1 |
| --- | --- |
| PHP/OOP, namespace/autoload | Model, controller, request, policy/service; giải thích trách nhiệm lớp |
| Form GET/POST, file, session | Tìm kiếm/form validate, upload ảnh, login/logout |
| JavaScript/DOM/AJAX | Form tư vấn fetch JSON và lỗi theo trường; chat dùng Reverb riêng |
| MySQL/khóa/quan hệ, SQL | ERD, PK/FK/unique, lọc/sắp xếp, truy vấn quan hệ, migration |
| Tìm kiếm/phân trang | Catalog giữ filter và phân trang hoạt động |
| Laravel MVC/route/Blade | Luồng request đến DB và view của một chức năng thật |
| Eloquent/migration/seeder/factory | Quan hệ, dữ liệu demo tái tạo được; không hard-code danh sách xe |
| Validation/middleware/auth/quyền | Role + ownership phía server, case lỗi và truy cập trái quyền |
| Đóng gói/triển khai/bảo mật | README, lockfiles, .env.example, backup/restore và demo cài máy khác |

PDO là nội dung thực hành chương 3; nhóm vẫn làm bài theo GV.
Trong sản phẩm Laravel dùng Eloquent, không xây thêm một backend PDO trùng lặp.
Nếu GV yêu cầu minh chứng PDO riêng, nộp bài thực hành tương ứng.
WebSocket không được liệt kê là bắt buộc trong đề cương: giữ vì nhóm đã chọn,
chỉ làm văn bản/lịch sử/quyền cơ bản, không mở thành ứng dụng nhắn tin hoàn chỉnh.

## 4. Ràng buộc cần thống nhất trước khi code

| Điểm | Quyết định P1 | Cần kiểm khi triển khai |
| --- | --- | --- |
| Đơn vị xe | Một record là một chiếc có code riêng; category là dòng Toyota | Hai chiếc cùng phiên bản vẫn cập nhật sold độc lập |
| Giá | Giá công bố tham khảo VND, không phải tổng lăn bánh | Hiển thị nhãn/nguồn/ngày, giá server validate |
| Hãng | Toyota cố định, một showroom | Không thêm CRUD hãng, chi nhánh hoặc đồng bộ hãng |
| Tài khoản | Guest gửi form; tài khoản mới chỉ customer | Không nâng quyền qua payload, không tra hồ sơ bằng phone |
| Tư vấn | Admin giao sales; new/closed không gửi chat | Own/assigned/active kiểm ở HTTP và kênh riêng |
| Chat | WebSocket văn bản + DB history + retry/quyền | Hai phiên, reconnect, khóa user/chuyển sales không lộ tin mới |
| Xe hiển thị | Xe và dòng đều công khai | Chặn URL trực tiếp và cả POST chọn xe ẩn |
| Xóa | Chưa liên quan mới xóa; có tham chiếu thì ẩn | FK, không mất hồ sơ/tin/lịch |
| Lịch | Pending là yêu cầu, nhân viên mới xác nhận | Chặn quá khứ, trùng sales, sai type/xe/service |
| Giờ tiếp nhận | 60 phút/sales, quy ước demo | Không tự nhận đây là năng lực xưởng hoặc thời lượng sửa xe |
| Bảo hành | Nội dung giới thiệu/liên hệ | Không tự chẩn đoán/duyệt chi trả bảo hành |
| Dữ liệu | 9 bảng nền, internal_note đủ | Không thêm inquiry_notes hoặc other chỉ để đủ bảng |
| An toàn | CSRF/hash/validation/upload/Policy | Không hạ xuống P2 vì là đồ án |

Các quyết định trên là mặc định triển khai theo yêu cầu mới nhất; ghi ngoại lệ trong
PR nếu nhóm/GV thay đổi. Bản kiểm tra này không chứng nhận các ràng buộc đã có code.

## 5. Cổng hoàn thành trước khi mở rộng

1. Hoàn thành P1 SRS và toàn bộ kịch bản ACCEPTANCE; ghi bằng chứng thật.
2. Cài/chạy MySQL, web và Reverb trên máy thành viên khác theo README.
3. Đóng lỗi gây mất dữ liệu/sai quyền/lỗi luồng; hoàn thiện giao diện 30% và báo cáo 20%.
4. Mỗi thành viên có đóng góp, hiểu mã và tập demo phần mình.
5. Đối chiếu lại yêu cầu GV, thống nhất thời gian còn lại rồi mới chọn **một** P2,
   cập nhật SRS/ADR/schema/test/phân công. Không hứa tăng điểm chỉ vì có P2.
