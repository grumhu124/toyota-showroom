# Nhật ký thay đổi hệ thống

Git lưu diff chính xác; file này giải thích thay đổi để người và AI hiểu.
Thêm mục mới lên đầu. Dùng CHANGE_TEMPLATE.md, không chỉ ghi “update code”.


## 2026-10-16 Rà soát code-first và triển khai song song

- Sửa inventory LF/CRLF đã đưa vào PR #1, merge fix vào nhánh Auth; giải quyết conflict bằng giữ hai mục CHANGELOG và regenerate manifest.
- Bỏ URL prefix customer để khớp /yeu-cau, /lich-hen; giữ auth/active/role và route name.
- Chặn password NUL/>72 byte trước bcrypt; thêm test đầu vào ASCII/Unicode/NUL.
- Thêm PARALLEL_WORK, READINESS_REVIEW và ADR 0004: code-first/migration, shared
  files, route namespace, fixture/seed, review chéo, thứ tự khóa và tích hợp.
- Đồng bộ SRS/AGENTS/README/kiến trúc/hợp đồng/SETUP/CONTRIBUTING/PR template.
- Không sửa migration/DB hoặc triển khai thêm nghiệp vụ P2. Các service/chat/lịch
  chưa có không được nhận hoàn tất bằng việc thêm quy ước.
- Kiểm local: composer check pass 20 test/158 assertions, Pint/cú pháp/encoding.
  CI PR #1 đã success run 38884213819; PR #2 chạy lại sau push.
  Không dùng test H01/inventory làm bằng chứng toàn bộ P1.

## 2026-10-16 H01 tài khoản và task list Hoàng

- Thêm HOANG_TASKS: H01–H05, không làm thay các module của nhóm.
- Thêm FormRequest/controller/Blade/routes đăng ký, đăng nhập, đăng xuất, tài khoản.
- Login failure-only 5/phút/IP+email; xóa limiter login đếm request chưa dùng.
- Middleware active thu hồi session trên web/JSON của user đã khóa; cấu hình redirect.
- Không đổi schema, dependency hoặc mở private channel. Admin user/Policy và thu hồi
  socket/job chat vẫn ở H02/tích hợp TV4, chưa nhận hoàn thành.
- 11 test Auth mới; tổng 17 test/135 assertions pass local SQLite, Pint/build pass.
- Browser lỗi môi trường nên chưa kiểm trực quan; MySQL xem CI, chưa claim local.

## 2026-10-16 Sửa inventory Windows/Linux

- CI PR tài liệu và Auth dừng ở inventory vì hash CRLF cục bộ khác LF Git checkout.
- Chuẩn hóa CRLF thành LF khi băm tệp text, giữ nguyên byte tệp binary; manifest format 2.
- Thêm test phát hiện đổi nội dung, tương đương LF/CRLF và giữ byte DOCX.
- Không sửa schema hoặc file nguồn Word. Kiểm lại CI trước khi nhận đủ điều kiện merge.

## 2026-10-16 Bổ sung tên thành viên

- Xác nhận TV3 Võ Nguyễn Thành Đạt, TV4 Nguyễn Huy Hoàng, TV5 Phan Võ Chí Bảo.
- Đồng bộ SRS, phân công và chủ module trong FEATURE_STATUS; phân biệt các tên Đạt/Hoàng.
- Giữ nguyên trách nhiệm và phạm vi; chưa thêm CODEOWNERS vì chưa có GitHub username.
- Chỉ sửa tài liệu và inventory; kiểm tra theo quy trình repo, không thay mã hoặc DB.

## 2026-10-16 Thu gọn phạm vi theo môn học và Toyota

- Thêm COURSE_SCOPE: nguồn đề cương trang 4–14, Rubric 2 đúng, đối chiếu thực hành và cổng P1/P2.
- SRS 1.4 viết lại phạm vi; lưu nguyên SRS 1.3, Word v6 không đổi.
- Giữ chat WebSocket văn bản/quyền/retry, CRUD, tìm/lọc/phân trang, AJAX form và lịch tiếp nhận.
- Hoãn InquiryNote/code/closed_at/other, audit UI, control channel tự chuyển, đổi lịch/mở lại và công cụ giá.
- Làm rõ từng chiếc xe/mã riêng, giá công bố tham khảo, bảo hành chỉ giới thiệu; không mô phỏng xưởng.
- Đồng bộ chỉ dẫn AI, kiến trúc/dữ liệu/contracts/trạng thái/phân công/nghiệm thu; không sửa mã nghiệp vụ, DB hoặc dependency.
- Kiểm tra: composer check pass (6 tests/19 assertions, cú pháp/encoding và Pint);
  inventory cập nhật 159 tệp. Không đổi mã nghiệp vụ, không nhận test nền là nghiệm thu P1.

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
