# Gói review cho nhóm — 17/10/2026

Chuẩn bị trước review, không thay đánh giá của thành viên khác.
Chưa có review submission/approval ở PR #1–#4 tại thời điểm kiểm.
Không gán reviewer tự động vì chưa xác nhận đủ GitHub username.
CI xanh không phải phê duyệt nghiệp vụ, quyền, UI hoặc cho phép tự merge.

## PR và thứ tự tích hợp

| PR | Nội dung | Base hiện tại | CI đã xác minh tại head | Điều kiện tiếp theo |
| --- | --- | --- | --- | --- |
| #1 | Phạm vi/SRS/nền/inventory | main | 508d505, run 38884213819 success | Thành viên khác review tài liệu, không nhận đã làm nghiệp vụ |
| #2 | H01 Auth + hợp đồng nhóm | docs/review-v6-srs | 668b65e, run 38884951066 success | Sau #1 merge, đổi base main, CI và review lại |
| #3 | FE preview | main | af2091f, run 39020622276 success sau sửa smoke test | Sau Auth tích hợp, cập nhật main và giải quyết route/docs chung; không ghi đè |
| #4 | H02 user admin + H03 review docs | feature/auth-middleware | H02 69d0302, run 39010837204 success; bổ sung tài liệu cần CI mới | Sau #2 merge, đổi base main; CI và review lại |

Các head/run là ảnh chụp thời điểm, không là chứng nhận cho commit tiếp theo.
Thứ tự khuyến nghị #1 → #2 → #4 rồi tích hợp #3 với Auth. #3 vẫn review UI
độc lập ngay bây giờ. Không đổi base sớm khi dependency chưa merge.
Nếu merge squash, nhánh phụ thuộc phải cập nhật/dọn diff có kiểm soát; không
force-push/reset nhánh người khác. Kiểm lại Files changed sau đổi base.

## Người review theo phần việc (chưa gửi yêu cầu GitHub)

- #1: cả nhóm xác nhận phạm vi và phần việc; một người khác Hoàng kiểm nguồn/rubric.
- #2: TV4 kiểm hợp đồng session/active/chat; Đạt kiểm tên route/form/mobile.
- #3: Hoàng kiểm tích hợp/Auth/route; TV3/TV5 kiểm dữ liệu xe và trạng thái/lịch.
- #4: TV4 kiểm version/khóa user, một thành viên khác kiểm luồng admin/form/list.
Đây là đề nghị phân công; chưa coi ai đã review, đồng ý hoặc được gửi thông báo.

## Checklist thực thi khi review

### #1 — tài liệu và nền

- [ ] SRS đúng P1/P2, rubric chính thức, không tự hứa điểm.
- [ ] Code-first/migration/schema 9 bảng khớp mã thật; source Word không sửa.
- [ ] Inventory text LF/CRLF và binary hoạt động, không tắt kiểm tra để qua CI.

### #2 — Auth

- [ ] Đăng ký customer, role/is_active từ payload bị bỏ qua; mật khẩu hash.
- [ ] Login 5 thất bại/phút/IP+email, thành công clear; normalize email.
- [ ] Session rotate, logout POST+CSRF, không flash password.
- [ ] Khóa user: HTTP mới bị chặn; không đánh đồng với socket đã thu hồi.
- [ ] UI tối thiểu và route names phù hợp FE; chưa claim responsive hoàn thiện.

### #3 — FE preview

- [ ] Chạy home → /xe → lọc/rỗng → chi tiết → CTA, desktop/mobile.
- [ ] Menu mobile có lối đăng nhập khi tích hợp; chưa có thì ghi vấn đề còn tồn.
- [ ] Fixture/slider/chi tiết nhất quán, nhãn minh họa rõ, chưa là dữ liệu MySQL.
- [ ] Preview không báo gửi thành công; form thật chỉ thành công khi BE lưu.
- [ ] Xử lý routes/web.php, CONTRACTS, FEATURE_STATUS, INVENTORY có giữ cả Auth/FE.
- [ ] Smoke test không phụ thuộc local Vite build; job build riêng vẫn pass.

### #4 — H02

- [ ] Chỉ admin có list/create/status; không khóa admin/xóa user/tự cấp admin.
- [ ] Tạo sales giữ session admin; input/email/phone/password sai bị từ chối.
- [ ] Search/filter/15 user mỗi trang/empty; không lộ mật khẩu.
- [ ] Khóa user và version cùng transaction; retry idempotent, unlock không hạ version.
- [ ] Giữ assignment/history; rollback khi cập nhật version lỗi.
- [ ] Không dùng test tuần tự CI MySQL làm bằng chứng race/socket đã xong.

## Cách ghi kết quả

Ghi commit đã review, màn/ca đã thử, kết quả, lỗi cần sửa và giới hạn.
Chỉ approve khi người review đã đọc và hiểu diff. Có commit mới ảnh hưởng quyền
hoặc dữ liệu thì review lại; không dựa vào “CI lần trước xanh”.
Không chạy test/migrate/reset trên DB dùng chung của nhóm.
