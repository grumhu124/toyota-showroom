# Giao diện tài khoản và chat khách hàng — 23/10/2026

- `/tai-khoan`: customer dùng layouts.customer, thông tin thật từ session. Admin/sales giữ auth.account và layouts.account.
- `/tai-khoan/tin-nhan` (`customer.chat.preview`): GET local/testing, bảo vệ web/auth/ensure.active/role:customer qua bootstrap. Không có POST hoặc endpoint gửi/đọc hồ sơ thật.
- Fixture resources/data/customer-chat-preview.php hoàn toàn minh họa, không đọc inquiries/messages và không ghi DB. Các hồ sơ new/closed không soạn tin; in_progress thử soạn tối đa 1000 ký tự nhưng submit chỉ báo chưa kết nối, giữ nội dung trong ô. Không giả gửi thành công, không tự trả lời, không polling hoặc lưu localStorage.
- JS chuyển hội thoại/tìm kiếm/đếm ký tự/back mobile; mỗi ô giữ bản nháp trong DOM, mất khi reload. Không gọi Echo cho tới khi TV4 triển khai endpoint và private channel thật.
- Lịch hẹn chỉ có nhãn sắp có, không gán số đếm/hồ sơ mẫu vào tài khoản thật. Không thêm chỉnh sửa thông tin hoặc mật khẩu.

## Bàn giao TV4

Tái sử dụng markup thread/composer, thay fixture bằng response allowlist của inquiry đã kiểm Policy. Shared HTTP chat phải nằm trong routes/inquiries.php cho customer/sales/admin, không đặt vào nhóm customer-only. Route preview này không thay contract thật.

Trạng thái thật cần nối: connecting/connected/disconnected; submit sending/sent/failed; UUID retry cùng payload, dedupe ID; subscribe rồi catch-up, server từ phiên lấy sender; new/closed chỉ đọc. Không dùng nội dung fixture hay timestamp minh họa khi nối DB. Quyền và trạng thái phải được server kiểm lại, không dựa nút disabled.

## Kiểm tra

Chạy composer check và npm run build. Kiểm route guest/customer/admin/sales/locked, account chỉ hiển thị user hiện tại, preview không tồn tại trong production. Trình duyệt kiểm desktop/mobile: tìm không có kết quả, chuyển new/closed, giữ draft khi đổi thread, submit không phát request hoặc báo thành công.

Kết quả local 23/10: composer check đạt 39 tests/360 assertions + Pint; npm run build đạt. Route list production chỉ có account, không có chat preview. Browser desktop và 390×844: profile từ session, điều hướng, logout mobile, tìm rỗng, chuyển thread/back, new/closed disabled, giữ draft và feedback chưa gửi đạt; không tràn ngang. QA dùng SQLite riêng, không dùng DB khách hàng đang chạy. Chưa kiểm thiết bị thật, kết nối WebSocket, MySQL concurrency hoặc gửi/nhận tin thực tế.
