# Task list của Hoàng — repo, tài khoản và tích hợp

Ngày bắt đầu: 16/10/2026. Phạm vi P1 theo SRS 1.4.
Hoàng phụ trách repo/auth trong file này khác Nguyễn Huy Hoàng (TV4, tư vấn/chat).
Không làm thay catalog, tư vấn/chat hoặc lịch của thành viên khác.

## H01 — Tài khoản và phiên đăng nhập (đã triển khai, chờ review)

- [x] Form đăng ký customer, validate tên/email/phone/mật khẩu.
- [x] Login/logout, đổi ID session sau xác thực, không nhận role từ form.
- [x] Giới hạn 5 lần thất bại/phút/IP+email, thành công xóa bộ đếm.
- [x] Tài khoản khóa không login; phiên đang dùng bị thu hồi ở request kế tiếp.
- [x] Trang tài khoản tối thiểu để kiểm tra trạng thái và logout.
- [x] Test dữ liệu sai, giả role, login sai/khóa, session/logout và role middleware.
- [x] Ghi hợp đồng route cho Đạt thay giao diện và bằng chứng test/build.
- [ ] Thành viên khác review PR, Đạt kiểm tra trực quan và gộp sau nhánh tài liệu.

Đợt đầu chỉ bàn giao H01; trang tài khoản là màn tối thiểu, không thay dashboard
nghiệp vụ của TV3/4/5. Chưa nhận toàn bộ Auth/quản lý tài khoản hoàn thành.

Bằng chứng sau rà soát: AuthenticationTest có 12 test; toàn bộ suite 20 test/158 assertions
pass local SQLite, Pint và Vite build pass. Xem docs/testing/AUTH_VALIDATION.md.
Review nhóm, kiểm tra trực quan responsive và tích hợp MySQL tiếp tục theo PR.

## H02 — Admin quản lý người dùng (tiếp theo H01)

- [ ] UserPolicy: chỉ admin xem danh sách/tạo sales/khóa-mở customer hoặc sales.
- [ ] Form/list có tìm kiếm và phân trang; không xóa tài khoản hoặc cấp admin qua UI.
- [ ] Khóa/mở theo transaction; không khóa admin; giữ người đang được giao hồ sơ.
- [ ] Khi khóa customer/sales, tăng access_version các inquiry liên quan.
- [ ] Test ownership/role/payload, thao tác lặp, khóa user đang hoạt động.
- [ ] Phối hợp TV4: chặn HTTP, private channel và tin đang chờ sau thu hồi quyền.

H01 chỉ chặn tài khoản đã bị đánh dấu khóa; H02 mới cung cấp thao tác admin.
Không tuyên bố thu hồi kết nối WebSocket hoàn chỉnh trước khi tích hợp module TV4.

## H03 — Quyền chung và phối hợp

- [ ] Chốt route public/customer/staff/admin với từng chủ module.
- [ ] Review Policy theo bản ghi của TV3/4/5, không chỉ kiểm role.
- [ ] Review CSRF/upload/response allowlist/throttle và log không lộ dữ liệu.
- [ ] Kiểm các ca đoán ID, chuyển sales, khóa user và trạng thái không hợp lệ.

Đã soạn hợp đồng chung ở PARALLEL_WORK và ADR 0004; đã sửa URL prefix customer.
Vẫn chờ owner review và code module thật để nghiệm thu tích hợp, không đánh dấu
toàn bộ H03 chỉ vì đã có quy ước.

## H04 — Dữ liệu và tích hợp

- [ ] Tổng hợp seeder từ TV3/TV5; đủ >12 xe Toyota, dịch vụ và user demo.
- [ ] Kiểm migration/enum/FK trên MySQL riêng; không sửa migration đã chạy chung.
- [ ] Tích hợp từng PR sau review, giữ main chạy được.
- [ ] Demo tư vấn → phân công → chat → lịch với dữ liệu và quyền đúng.

## H05 — Bàn giao môn học

- [ ] README cài trên máy khác, secret riêng; chạy web/Reverb/queue/build.
- [ ] Hướng dẫn backup/restore MySQL demo.
- [ ] Tổng hợp ACCEPTANCE có bằng chứng, responsive và lỗi tồn.
- [ ] Báo cáo ERD/MVC/phạm vi/test/nguồn; mỗi người viết và demo phần mình.
- [ ] Đối chiếu rubric và yêu cầu GV trước khi chọn P2.

Mỗi task xong ghi PR, bằng chứng và giới hạn; không đánh dấu chỉ vì có file.
Không có deadline cố định vì nhóm chưa cung cấp ngày nộp.
