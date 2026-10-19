# Trạng thái thực tế

Ngày 20/10/2026. Nhánh demo/progress-2026-10-20 ghép frontend + H01 từ PR #5
với H02 từ PR #4. Đã đồng bộ main 983b12b: lịch sử main có H01/H02/FE,
nhưng catalog TV3 đã được revert. Đây là bản diễn tập, không thay nghiệm thu của nhóm.
Trang chủ, /xe và /xe/{id} vẫn dùng fixture local/testing. Auth dùng session/CSRF thật;
admin quản lý tài khoản qua database. Tư vấn, chat và lịch chưa nối backend trong bản này.
H03 mới có hợp đồng quyền và checklist. Nhánh TV3 feature/car-crud-search đã có mã,
chưa được kiểm tra hoặc đưa vào bản demo này.



| Module | Đã có | P1 còn làm | Chủ trách nhiệm |
| --- | --- | --- | --- |
| Framework | Laravel/lockfile/Vite/Tailwind/CI | Cài/demo máy khác | Hoàng + nhóm |
| Dữ liệu | 8 bảng core + audit, enum/model | Seed Toyota; giữ brand=Toyota, internal_note | Võ Nguyễn Thành Đạt (TV3)/Phan Võ Chí Bảo (TV5) + Hoàng |
| Auth | H01; H02 UserPolicy, admin list/search/pagination/tạo sales/khóa-mở, transaction tăng version khi khóa; test HTTP pass | Review/kiểm trực quan H02, tích hợp thu hồi socket/job với TV4 | Hoàng |
| Catalog | Schema/model/thư mục | CRUD/ảnh/search/filter/pagination/UI | Võ Nguyễn Thành Đạt (TV3) + Đạt |
| Tư vấn | Inquiry model/enum | Form AJAX, phân công/trạng thái/internal_note/quyền | Nguyễn Huy Hoàng (TV4) + Đạt |
| Chat | Reverb/Echo cấu hình, private channel từ chối mặc định | Gửi/lưu/phát, lịch sử, retry/catch-up, thu hồi quyền, UI hai phiên | Nguyễn Huy Hoàng (TV4) + Đạt |
| Lịch/dịch vụ | Schema/model/enum | Form, phân công, xác nhận khóa/trùng giờ, hủy/hoàn tất/no_show | Phan Võ Chí Bảo (TV5) |
| Audit | Bảng/model/namespace | Không buộc làm module riêng P1; giữ nền | Hoàng |
| Bàn giao | Kiểm tra nền từng pass local và CI | Test nghiệp vụ, responsive, báo cáo, demo cả 5 người | Cả nhóm |

Chưa triển khai InquiryNote/code/closed_at/other; các mục này là P2, không còn
chặn hoàn thành P1. Không cần migration bỏ brand trong P1. Kênh users control,
khách tự đổi lịch/mở lại cũng P2. Chưa có chức năng nào tự hoàn tất vì sửa SRS.
Chỉ đánh dấu hoàn tất khi chạy end-to-end, có quyền/validation và bằng chứng kiểm tra.
Bằng chứng trước khi ghép: FE + H01 có 27 tests/263 assertions; H02 riêng có
28 tests/222 assertions. Kết quả bản ghép ghi trong docs/demo/DEMO_2026-10-20.md.
Chưa nghiệm thu race trên hai kết nối MySQL hay thu hồi socket thực tế.
Xem AUTH_VALIDATION và docs/team/HOANG_TASKS.md. Đã kiểm menu Auth ở viewport
390×844 và luồng admin trên trình duyệt; chưa kiểm điện thoại thật/toàn bộ responsive.
Bản ghép: composer check 35 tests/328 assertions + Pint; build đạt.
