# Phân công và vị trí thư mục

| Thành viên | Trách nhiệm | Vị trí chính | Nhánh gợi ý |
| --- | --- | --- | --- |
| Hoàng | Repo, nền tảng, auth, active/role, audit, tích hợp và review | Controllers/Auth, Controllers/Admin/User và Log, Middleware, Services/Audit, bootstrap, config, docs, CI | feature/auth-middleware |
| Đạt | Layout/component, UI khách, CSS, responsive, AJAX filter và UI chat | resources/views/layouts, components, auth, cars, services, inquiries, appointments, resources/css, resources/js | feature/frontend-layout |
| TV3 | Xe, dòng xe, ảnh, giá, search/filter/pagination, admin xe | Controllers/Catalog, Requests/Catalog, Services/Catalog, policies xe, views/admin/cars và categories, tests/Feature/Catalog | feature/car-crud-search |
| TV4 | Tư vấn, phân công, chat, quyền kênh, event và tin nhắn | Controllers/Inquiry, Requests/Inquiry, Services/Inquiry, Events, policies Inquiry/Message, routes/channels.php, views/staff/inquiries, tests/Feature/Inquiry | feature/inquiry-chat-websocket |
| TV5 | Dịch vụ, lịch xem/bảo dưỡng, xác nhận/đổi/hủy, xung đột giờ | Controllers/Appointment, Requests/Appointment, Services/Appointment, policies Appointment/Service, views/staff/appointments và admin/services, tests/Feature/Appointment | feature/appointment-schedule |

TV3/4/5 chưa được cung cấp tên và GitHub username, không tự đoán. Khi nhóm điền
chính xác, Hoàng thêm CODEOWNERS nếu muốn tự động yêu cầu review.
Phân công là trách nhiệm chính, không phải chặn mọi cộng tác.
Đạt quản giao diện khách và layout chung; chủ module làm màn hình admin/staff
trên layout đó, phối hợp cùng Đạt để thống nhất màu/chữ/component.

Schema/model/enum đã có baseline chung. Chủ module tiếp tục hoàn thiện logic
và kiểm thử, tạo migration mới nếu cần; không xóa bảng hoặc sửa migration đã chạy.
DatabaseSeeder do Hoàng tổng hợp; mỗi module bổ sung seeder riêng.
UserSeeder cần mật khẩu demo trên máy; chưa có seed giá/ảnh xe thực tế.

Trước khi làm: đọc AGENTS, spec, contracts, trạng thái; chọn issue nhỏ đúng module.
Thay đổi shared files (User, route groups, bootstrap, config, package/lockfile,
migrations chung, layouts) phải mô tả người/module bị ảnh hưởng trong PR.
Mỗi người chịu kiểm thử và báo cáo phần mình; Hoàng không tự nhận sửa mọi lỗi.
