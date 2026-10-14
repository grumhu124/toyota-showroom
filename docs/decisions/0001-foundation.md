# ADR 0001 Nền tảng và xử lý mâu thuẫn v5

Ngày 2026-10-14. Trạng thái: áp dụng trong bản setup theo yêu cầu chủ repo;
nhóm có thể thay đổi bằng quyết định/PR mới, không âm thầm đổi từng module.

Chọn Laravel 12, PHP 8.3, MySQL 8, Blade và Tailwind 4 từ nền tảng bài thực hành
hiện có; Reverb + Echo cho chat. Không tự nâng Laravel 13 hoặc chọn React/Bootstrap.
Dùng controller theo module, policy từng record và service cho thao tác nhiều bước.
Có 8 bảng cốt lõi, activity_logs là bảng audit thứ 9; bảng session/cache/jobs kỹ thuật riêng.
Trạng thái dùng backed enum PHP + string DB, phiên bản dependency cố định bằng lockfile.

Áp dụng các chỉnh lý SOURCE_REVIEW.md: tên trường chat, access_version,
giới hạn form, reserved thủ công, khoảng lịch nửa mở và biến broadcasting.
Không thêm module bán hàng online. Activity logging chỉ lấy trường an toàn,
payload không lưu mật khẩu, session, secret, body tin nhắn hoặc liên hệ khách đầy đủ.

Hệ quả: schema nền dùng chung giảm việc mỗi người tạo một bảng khác tên.
Thay đổi schema sau commit baseline cần migration mới. Khóa lịch phải được thử
bằng MySQL; SQLite kiểm tra schema/quan hệ không thay được kiểm tra cạnh tranh.
Quyết định màu sắc chi tiết thuộc Đạt; lựa chọn framework CSS đã thống nhất Tailwind.
