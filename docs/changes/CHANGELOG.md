# Nhật ký thay đổi hệ thống

Git lưu diff chính xác; file này giải thích thay đổi để người và AI hiểu.
Thêm mục mới lên đầu. Dùng CHANGE_TEMPLATE.md, không chỉ ghi “update code”.


## 2026-10-16 Rà soát v6 và SRS

- Thêm Word v6 nguyên byte, SRS 1.3, bản trích v6, SRS 1.2 gửi ban đầu, review và ADR 0002.
- Sửa metadata, enum/active, sau commit, UUID/payload, reconnect/version, khóa lịch,
  ảnh nháp/upload; tách schema đích khỏi baseline.
- Đồng bộ AGENTS/README/spec/contracts/kiến trúc/trạng thái/phân công/nghiệm thu.
- Công cụ inventory thêm hai tài liệu gốc; không đổi app/DB/migration/model/dependency.
- Đã chạy composer check: inventory 156 tệp, encoding/cú pháp, 6 test/19 assertions và Pint đều pass.
  Đã đối chiếu hash nguồn và đường dẫn tài liệu; CI xem trong PR. FR mới vẫn cần test khi làm module.

## 2026-10-14 Xác nhận kiểm tra CI của bản nền

- Sửa: FEATURE_STATUS và BASELINE_VALIDATION ghi kết quả GitHub Actions thực tế,
  thay trạng thái dự kiến kiểm tra MySQL bằng kết quả pass ở CI.
- Bằng chứng: run 38555128110 của commit 33cec56; frontend và PHP SQLite/MySQL đều success.
- Không thay mã nghiệp vụ/schema. Cập nhật inventory để phản ánh các file Markdown đã sửa.
- Hạn chế giữ nguyên: chưa có chat end-to-end hoặc service xác nhận lịch để thử cạnh tranh.

## 2026-10-14 Khởi tạo nền tảng

- Thêm: Laravel 12 và dependency Reverb/Echo, schema 8 core + audit, enums/models,
  active/role middleware, seed user local, cấu trúc module và tài liệu nhóm/AI.
- Sửa so với nguồn v5: thống nhất tên trường, giới hạn form, biến broadcasting,
  quy tắc overlap, reserved và số bảng; chi tiết ở SOURCE_REVIEW/ADR 0001.
- Thay: README/workflow/changelog mẫu upstream bằng hướng dẫn và CI cho nhóm.
- Xóa: workflow bot upstream cho issue/PR/update changelog; không xóa mã thành viên.
- Kiến trúc: monolith MVC, MySQL, Blade/Tailwind, Reverb; chưa có nghiệp vụ online bán hàng.
- Trạng thái: nền tảng; auth/CRUD/chat/lịch hẹn end-to-end chưa triển khai.
- Kiểm tra và hạn chế: xem BASELINE_VALIDATION.md; không coi CI pass là đạt điểm mục tiêu.
