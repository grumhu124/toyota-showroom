# Nhật ký thay đổi hệ thống

## 2026-10-18 Đồng bộ nhánh giao diện

- Giữ hai ảnh và nền tối đã lưu local; ghép commit sửa smoke test af2091f từ GitHub.
- Không đổi main, backend hoặc thời gian video; tạo lại inventory sau khi ghép.

## 2026-10-17 Chuẩn bị review — smoke test độc lập Vite build

- CI frontend build đạt nhưng PHP test không có bước tạo Vite manifest.
- Tái hiện điều kiện thiếu assets: ExampleTest trả 500 ViteManifestNotFoundException;
  HomePreviewTest/CollectionPreviewTest đã withoutVite, chỉ smoke test cũ chưa có.
- Sửa ExampleTest dùng withoutVite để kiểm render HTTP độc lập asset pipeline.
  Không sửa giao diện, route, fixture, dependency hoặc bỏ kiểm tra npm build riêng.
- Probe trước sửa fail, sau sửa pass; full composer check kiểm lại trước push.

## 2026-10-16 Hai góc nhìn Toyota dưới hero

- Thêm hai ảnh người dùng cung cấp: đèn xe và vô lăng, bố cục ảnh/chữ xen kẽ.
- Nền đen than đồng bộ dịch vụ/showroom/footer; chỉ áp dụng trang chủ.
- Reveal một lần và zoom ảnh chậm, tôn trọng reduced motion; không JS vẫn thấy nội dung.
- Giữ nguyên video, timeline, route và backend. CTA vẫn là preview, không tự nhận đặt lịch thật.
- Kiểm tra render có hai ảnh, composer check, build và inventory; chưa kiểm thử mọi thiết bị thực.

## 2026-10-16 Card sản phẩm và tên xe chuyển dọc

- Theo ảnh tham khảo: sidebar bộ lọc, card trắng bo góc, 3 cột desktop,
  ảnh từ bộ xe-san-pham đã bàn giao; thông tin minh họa, không bịa công suất/tốc độ.
- Tên dòng xe lớn tách khỏi slide ngang; tên vào từ dưới, tên cũ đi lên.
- Trở lại clip tách nền/canvas, giữ thời điểm dừng/tốc độ người dùng chỉnh.
- Không thêm so sánh/cấu hình xe của website tham khảo vào phạm vi đề tài.
- Kiểm tra build, render/404/trạng thái và browser slider/filter/cards.

## 2026-10-16 Thử clip gốc giữ nền studio

- Người dùng yêu cầu thử clip chưa xóa nền: native video 1280x720 nền xám sáng,
  đặt trong khung studio trên hero tối; không xóa nền/không xử lý pixel canvas.
- Giữ endTime 8 giây và tốc độ progress * 11 do người dùng chỉnh, Blob để seek local.
- Giữ file clip cũ để có thể so sánh/đổi lại, không ghi đè tài nguyên nguồn.
- Build/test và kiểm tra browser khung hình, cuộn thuận/ngược.

## 2026-10-16 Gió khí động học và chữ rõ nét

- Thay vệt ngang rất mờ bằng ba đường gió SVG cong ôm dáng xe, có vệt chạy
  theo path, stroke xanh băng rõ hơn; vẫn mờ đi khi tách xe và pause ngoài màn hình.
- Chữ CAMRY màu bạc đặc, bỏ text-shadow/glow; không đổi font hay hình xe.
- Không thay timeline người dùng, DB/route/dependency; kiểm tra build/browser.

## 2026-10-16 Chuyển động không gian quanh Camry

- Theo phương án được duyệt: 5 luồng gió, 3 vạch đường, ánh sáng quét chậm.
- Gió/đường mờ theo tiến trình tách, scanner hiện; CSS transform/opacity,
  tự pause khi ngoài màn hình/tab ẩn. Không sửa video/ảnh, endTime hoặc tốc độ tua.
- Kiểm tra build, inventory và browser; không thêm dependency/DB hay endpoint.

## 2026-10-16 Tăng độ rõ chữ Camry

- Chữ CAMRY có nền chữ xanh bạc, viền sáng hơn và glow nhẹ theo phản hồi.
- Chỉ sửa kiểu chữ hero, giữ chuyển động và thời gian người dùng tự chỉnh.
- Build và kiểm tra inventory; hiệu ứng gió/đường chạy mới chỉ là đề xuất.

## 2026-10-16 Giữ khung xe tách theo ảnh người dùng

- Giới hạn video Camry ở giây 7.5, giữ trong 25% cuối đoạn cuộn sticky.
- Cuộn ngược vẫn lắp xe; không thay clip, bố cục, DB hoặc route.
- Kiểm tra build và mốc currentTime trong trình duyệt.

## 2026-10-16 Phong cách công nghệ trang chủ

- Theo người dùng duyệt: nền than chì/xanh đen, ánh sáng xanh băng, vòng sàn,
  grid kỹ thuật nhẹ, chữ Camry viền; chữ sáng, CTA đỏ Toyota, dịch vụ nền sáng.
- CSS home-tech riêng và body class home; không thay ảnh/video/DB hoặc quyền.
- Kiểm tra build và trình duyệt ở hero nguyên xe/tách xe.

## 2026-10-16 Trang chủ Camry

- Hero theo Figma 22:43; sticky scroll video/canvas và ảnh dự phòng, responsive.
- Video bàn giao có nền đen: lọc vùng đen liên thông biên canvas, không giả alpha.
- Dịch vụ/showroom và CTA thông báo chưa nối backend; logo về home, link anchor thật.
- Chỉ thay frontend/route preview local; không đổi DB/schema/dependency/quyền.
- Kiểm tra build/test và browser; giới hạn clip mô phỏng trong HOME_PREVIEW.

## 2026-10-16 Thu nhỏ xe và luôn bật chuyển động

- Theo yêu cầu người dùng: giới hạn ảnh hero 620px desktop, 84% mobile,
  giữ tỷ lệ/căn chân bánh; giảm phóng ảnh nguồn 518px.
- Bỏ nút chọn hiệu ứng và logic localStorage; slider luôn chuyển ngang 1300ms.
- Không đổi ảnh, dữ liệu, DB hoặc dependency; build và kiểm tra trình duyệt.

## 2026-10-16 Trang chi tiết xe frontend

- Thêm /xe/{id} local/testing và fixture riêng; gallery ảnh/phóng to, thông tin,
  thông số chờ backend, CTA theo trạng thái và xe liên quan. Không đổi DB/quyền.
- Nối card và hero với chi tiết; Innova fixture sold không nhận đặt lịch.
- Dùng ảnh thật đã có trong bộ tài nguyên, không bịa thông số hay ảnh góc khác.
- Kiểm tra render các trạng thái, 404, build và browser nếu môi trường cho phép.

## 2026-10-16 Làm chậm chuyển xe

- Theo phản hồi người dùng: tăng transition từ 850ms lên 1300ms,
  dùng easing (.45,0,.2,1) để xe tăng tốc và dừng mềm hơn.
- Giữ nút bật/tắt và reduced-motion; không đổi dữ liệu hay quyền.
- Kiểm tra build và inventory sau thay đổi.

## 2026-10-16 Sửa chuyển xe khi giảm chuyển động

- Tái hiện: trình duyệt báo prefers-reduced-motion: reduce nên transition là none.
- Thêm nút bật/tắt hiệu ứng slider, aria-pressed và lưu lựa chọn localStorage;
  lựa chọn rõ ràng của người dùng ưu tiên cài đặt giảm chuyển động của hệ thống.
- Giữ mặc định giảm chuyển động; bật chủ động cho transform 850ms hai chiều.
- Không thay DB, quyền, route hoặc dependency. Kiểm tra trình duyệt và build.

## 2026-10-16 Trang Dòng xe frontend

- Thêm layout app, car-card, trang cars/index theo Figma 22:44, ảnh/font local.
- Slider Corolla Cross → Vios → Innova dịch ngang, không quay vòng; keyboard,
  swipe, reduced-motion. Form lọc/sort fixture và empty state; responsive.
- GET /xe chỉ local/testing; dữ liệu minh họa tách resources/data, không ghi DB.
- CTA chưa tích hợp dùng dialog thông báo; chưa có auth/chi tiết/đặt lịch thật.
- Shared files: routes/web.php, app.css, app.js; không đổi schema/dependency/quyền.
- Kiểm tra build, test nền, browser và giới hạn ghi ở COLLECTION_PREVIEW.md.

Git lưu diff chính xác; file này giải thích thay đổi để người và AI hiểu.
Thêm mục mới lên đầu. Dùng CHANGE_TEMPLATE.md, không chỉ ghi “update code”.

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
