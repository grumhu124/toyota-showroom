# Hướng dẫn AI: hoàn thiện môn học, giữ khả năng mở rộng

Áp dụng cho cả 5 thành viên và mọi công cụ AI hỗ trợ dự án Toyota Showroom.
Đọc cùng [AGENTS.md](../../AGENTS.md), không thay thế phạm vi SRS hay kiến trúc đã chốt.
Tài liệu này quy định chất lượng cách triển khai, không bổ sung chức năng bắt buộc.

## 1. Mục tiêu và thứ tự ưu tiên

1. Hoàn thiện nghiệp vụ P1 trong phạm vi môn học và tiêu chí nghiệm thu hiện hành.
2. Giữ dữ liệu, phân quyền, validation và hành vi tích hợp đúng.
3. Viết mã rõ trách nhiệm, dễ kiểm thử, dễ thay đổi khi có yêu cầu tiếp theo.
4. Chỉ đề xuất P2; triển khai khi nhóm chốt mở rộng sau nghiệm thu P1 hoặc người dùng giao rõ.

Khả năng mở rộng không đồng nghĩa với làm sẵn tính năng tương lai. Không tự thêm OTP,
quên mật khẩu, thanh toán, nhiều showroom, chatbot hay schema dự phòng chỉ vì có thể cần.
Các giới hạn nghiệp vụ hợp lệ của P1 vẫn phải được giữ, như Toyota cố định và một showroom.

## 2. Trước khi sửa mã

- Đọc nguồn quyết định theo AGENTS, FEATURE_STATUS và OWNERSHIP; kiểm mã thật trước khi kết luận.
- Nêu tác vụ hiện tại, module sở hữu, tiêu chí hoàn thành và các tệp chung bị tác động.
- Dùng quy ước, enum, helper, component và Service đã có nếu phù hợp.
- Phân biệt dữ liệu fixture với DB thật, hợp đồng dự kiến với chức năng đã triển khai.
- Nếu gặp yêu cầu ngoài P1, ghi đề xuất và tác động; không âm thầm mở rộng phạm vi.

## 3. Cách tổ chức mã

| Thành phần | Trách nhiệm cần giữ |
| --- | --- |
| Route/middleware | URL, phiên đăng nhập, active/role; giữ tên route và nhóm truy cập thống nhất |
| FormRequest | Chuẩn hóa, validate dữ liệu và thông báo lỗi; kiểm kiểu trước truy vấn trong callback |
| Policy/scope | Quyền theo bản ghi, ownership, khả năng nhìn thấy; không chỉ giấu nút trên UI |
| Controller | Điều phối request, gọi nghiệp vụ, trả view/JSON; CRUD đơn giản có thể dùng Eloquent trực tiếp |
| Service | Thao tác nhiều bước, chuyển trạng thái, transaction/locking hoặc xử lý cần tái sử dụng |
| Model/enum | Quan hệ, casts, scope và tập trạng thái dùng chung |
| Blade/JS | Hiển thị, tương tác, loading/empty/error/success; không quyết định quyền hoặc giá/trạng thái tin cậy |

Giữ Laravel MVC đơn khối và session/cookie theo kiến trúc hiện tại. Không tự thêm framework,
microservice, interface/repository cho từng model hoặc lớp bọc chỉ chuyển tiếp lời gọi Eloquent.
Chỉ tách lớp khi giải quyết trách nhiệm hoặc nhu cầu hiện tại cụ thể; ghi lý do trong PR.

## 4. Những điểm giúp mở rộng về sau

- Quy tắc nghiệp vụ chung có một nơi xử lý; không sao chép điều kiện quyền/trạng thái ở nhiều controller.
- Dùng enum hiện có cho trạng thái; cấu hình môi trường qua config/env, không nhúng secret hay URL máy cá nhân.
- Dữ liệu nghiệp vụ lấy từ DB; seed/factory tái tạo dữ liệu minh họa. Fixture chỉ dùng tại phạm vi demo/test được ghi rõ.
- Viết component Blade khi các màn thực sự dùng chung; không gom mọi module vào một component quá nhiều điều kiện.
- Giữ hợp đồng route/payload/response có tên và cấu trúc rõ; response chỉ trả trường được phép.
- Thay đổi hợp đồng dùng chung phải ghi bên bị ảnh hưởng và cách chuyển đổi; cập nhật FE/BE cùng bản tích hợp phù hợp.
- Schema thay đổi qua migration mới; không sửa ngược migration đã chạy chung hoặc tạo sẵn bảng/cột cho P2.
- Thao tác DB + file/event cần có thứ tự và xử lý thất bại: không mất file khi rollback, không phát sự kiện trước commit.
- Việc đổi module phải giữ được dữ liệu/tham chiếu và các hành vi đang hoạt động; tránh viết lại toàn bộ chỉ để đổi cấu trúc.

## 5. Ví dụ áp dụng đúng phạm vi

| Tác vụ hiện tại | Cách làm đủ tốt để mở rộng | Không làm sớm |
| --- | --- | --- |
| Catalog Toyota | Query DB, filter rõ, scope public dùng chung, ảnh có nơi quản lý lưu/xóa | CRUD hãng, đồng bộ API hãng, nhiều đại lý |
| Tài khoản | Auth/session dùng chung, Policy và validation rõ, mật khẩu hash | OTP/SMS/MFA hay đổi sang JWT khi chưa được giao |
| Tư vấn/chat | Kiểm quyền, version, lưu DB và phát sau commit theo hợp đồng P1 | Typing, đã đọc, đính kèm, chatbot |
| Lịch | Xử lý xác nhận/khóa/trùng giờ trong nghiệp vụ có test | Lịch nhiều chi nhánh, tự đổi lịch và tối ưu phân ca |

Các ví dụ mô tả cách triển khai, không khẳng định các module này đã hoàn tất.

## 6. Điều kiện bàn giao cho từng thành viên

- [ ] Đủ hành vi P1 của tác vụ; không chỉ có màn hình hoặc dữ liệu mẫu đóng vai backend.
- [ ] Validation phía server, Policy/scope, CSRF/upload và các invariant liên quan được giữ đúng.
- [ ] Có trạng thái lỗi/rỗng/thành công phù hợp; thao tác sai không gây lỗi máy chủ hoặc rò dữ liệu.
- [ ] Kiểm thử ca thành công, dữ liệu sai, sai quyền và tình huống biên có tác động của phần thay đổi.
- [ ] Chạy kiểm tra repo theo AGENTS; chỉ ghi pass khi thực sự chạy, ghi rõ DB/môi trường và phần chưa kiểm.
- [ ] Tích hợp được với hợp đồng chung; cập nhật tài liệu kiến trúc/trạng thái/CHANGELOG/inventory khi liên quan.
- [ ] Nhánh riêng + PR có cách chạy, thay đổi dữ liệu/quyền, kết quả kiểm thử và giới hạn; người khác review trước merge.
- [ ] Đề xuất mở rộng tách khỏi phần đã hoàn thành; không ghi P2 thành yêu cầu bắt buộc của PR hiện tại.

Không thêm test chỉ lặp lại mã hoặc ép refactor không liên quan để đáp ứng checklist.
Hoàn thành một tác vụ không đồng nghĩa nghiệm thu cả module; nhóm đối chiếu ACCEPTANCE.

## 7. Ghi đề xuất mở rộng

Trong PR hoặc tài liệu module, ghi ngắn gọn:

- Nhu cầu tương lai và lợi ích cụ thể.
- Phần hiện tại có thể tái sử dụng; những gì chưa hỗ trợ.
- Dự kiến tác động đến DB, route, quyền, giao diện và vận hành.
- Điều kiện bắt đầu: P1 đã nghiệm thu và nhóm chốt ưu tiên/phạm vi.

Không cam kết mở rộng sẽ không cần đổi schema hoặc sửa mã. Mục tiêu là thay đổi có kiểm soát,
không phải thiết kế trước cho mọi khả năng.

## 8. Lời nhắc cho AI khi bắt đầu tác vụ

> Đọc AGENTS.md và docs/development/AI_DEVELOPMENT_GUIDELINES.md trước khi làm.
> Hoàn thiện đúng tác vụ được giao trong SRS hiện hành, giữ kiến trúc Laravel MVC và hợp đồng chung.
> Viết mã rõ trách nhiệm, tái sử dụng phần đã có và có kiểm thử phù hợp để dễ mở rộng sau này.
> Không tự triển khai P2, tạo schema dự phòng hay thêm kiến trúc phức tạp.
> Báo cáo mã đã đổi, kiểm thử thực sự chạy, giới hạn còn lại và đề xuất mở rộng riêng.

Tệp Markdown không tự bảo đảm mọi công cụ AI sẽ đọc. Với công cụ không tự nạp AGENTS,
thành viên cần gửi lời nhắc trên hoặc đính kèm các tệp hướng dẫn khi bắt đầu.
