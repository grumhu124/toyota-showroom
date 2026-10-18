# Bản trích nguồn v6

Nguồn nguyên bản để đối chiếu; triển khai theo SRS/ADR đã rà soát, không thực thi pseudocode trong bản trích.

LUỒNG NGHIỆP VỤ

WEBSITE SHOWROOM XE TOYOTA

Bản v6 — Đã chốt, loại bỏ mâu thuẫn

Nhóm 5 người · Mục tiêu 9/10 · PHP 8.2+ · Laravel 11+ · MySQL

Cập nhật: 16/10/2026

────────────────────────────────────────────────────────────────────────────────

Môn học: Lập trình mã nguồn mở

Không nằm trong phạm vi: Giỏ hàng · Thanh toán online · Đặt cọc trực tuyến · Hóa đơn điện tử · Trả góp · Nhiều showroom · Nhiều hãng xe

MỤC LỤC

0.  Tổng quan và phạm vi

1.  Actor và quyền

2.  Database — 10 bảng (9 nghiệp vụ + nhật ký)

3.  Eloquent Relationships (Laravel)

4.  Middleware và Route Groups

5.  Chức năng danh mục xe — Tìm kiếm, Lọc, Phân trang

6.  Tài khoản và xác thực

7.  Luồng tư vấn và chat

8.  Lịch hẹn

9.  Quản trị (Admin)

10.  Giao diện — Thiết kế & Responsive

11.  AJAX — Điểm sáng tạo (10%)

12.  Kịch bản kiểm thử

13.  Phân công công việc — 5 người

14.  Cấu trúc thư mục Laravel

15.  Checklist nộp bài

16.  Phần mở rộng

17.  Lịch sử thay đổi v5 → v6

0   Tổng quan và phạm vi

Website giới thiệu xe mới của hãng Toyota tại một showroom, xây bằng PHP Laravel + MySQL. Khách xem xe, xem giá công bố, gửi yêu cầu tư vấn, chat thời gian thực với nhân viên và đặt lịch xem xe hoặc bảo dưỡng. Thương lượng, hợp đồng, thanh toán và bàn giao xe thực hiện trực tiếp tại showroom — ngoài phạm vi hệ thống.

Không nằm trong bản này (out of scope):

Giỏ hàng

Thanh toán online

Hóa đơn điện tử

Đặt cọc trực tuyến

Ký hợp đồng điện tử

Trả góp

Nhiều showroom

Nhiều hãng xe (showroom chỉ bán xe Toyota)

So sánh xe

Xuất PDF báo giá

Dự toán lăn bánh (chuyển sang mở rộng sau nghiệm thu)

Phạm vi trên đủ để đạt 9/10 nếu làm sâu, đúng kỹ thuật và có báo cáo tốt.

1   Actor và quyền

| Actor | Không cần login | Cần login | Ghi chú |
| --- | --- | --- | --- |
| Khách vãng lai | Xem xe, tìm kiếm, lọc, xem giá, gửi form tư vấn, đặt lịch | — | Nhận mã yêu cầu qua màn hình; không chat, không xem lại/đổi/hủy lịch (nhân viên liên hệ qua SĐT) |
| Khách đăng ký | Như vãng lai | Chat, xem lịch sử yêu cầu, lịch hẹn, hủy lịch của mình | Tự đăng ký, role mặc định customer |
| Nhân viên | — | Xử lý hồ sơ được giao, chat, ghi chú, xác nhận lịch | Admin tạo tài khoản |
| Admin | — | CRUD toàn bộ, phân công, quản lý tài khoản, xem log | Tài khoản seeder |

2   Database — 10 bảng (9 nghiệp vụ + nhật ký)

2.1 Sơ đồ quan hệ (ERD tóm tắt)

users (id, name, email, phone, password, role, is_active, timestamps)
  ├──< inquiries (code unique, customer_id nullable, car_id nullable, assigned_to nullable,
  │       status, close_result, closed_at, access_version, ...)
  │       ├──< messages (inquiry_id, sender_id, content, client_message_id, ...)
  │       ├──< inquiry_notes (inquiry_id, user_id, content, timestamps)
  │       └──< appointments (inquiry_id nullable, ...)
  └──< appointments (assigned_to nullable)

car_categories (id, name, slug unique, description, is_visible, timestamps)
  └──< cars (category_id, code unique, name, year, color, specs json,
             price decimal(15,0), status enum, is_visible, timestamps)
        └──< car_images (car_id, path, sort_order, is_primary, timestamps)

services (id, name, type enum, description, allow_booking, is_visible, timestamps)
  └──< appointments (service_id nullable)

appointments (id, type enum, customer_id nullable, inquiry_id nullable,
              car_id nullable, service_id nullable, customer_car_info,
              contact_name, contact_phone, assigned_to nullable,
              requested_at, confirmed_at nullable, status enum,
              note, cancel_reason, timestamps)

activity_logs (id, user_id nullable, action, target_type, target_id, payload json, ip, timestamps)

2.2 Enum values quan trọng

| Bảng | Trường | Giá trị |
| --- | --- | --- |
| users | role | customer · sales · admin |
| cars | status | available · reserved · sold |
| inquiries | status | new · assigned · in_progress · closed |
| appointments | type | viewing · maintenance |
| appointments | status | pending · confirmed · completed · cancelled · no_show |
| inquiries | close_result | da_tu_van · khach_khong_tiep_tuc · khach_giao_dich_truc_tiep · khong_lien_he_duoc |
| services | type | maintenance · warranty · other |

2.3 Cột bổ sung và bảng inquiry_notes (v6)

inquiries.code: mã yêu cầu dạng YC- + 8 ký tự in hoa ngẫu nhiên (vd. YC-7K2M9QXA), unique, hiển thị cho khách sau khi gửi form.

inquiries bổ sung: car_id (nullable), close_result (nullable, 4 giá trị ở mục 7.3), closed_at (nullable), access_version (unsigned int, mặc định 1).

inquiry_notes (id, inquiry_id, user_id, content, timestamps): ghi chú nội bộ của nhân viên/Admin; khách không bao giờ xem được.

messages: unique (sender_id, client_message_id).

cars.price: decimal(15,0) unsigned (VND); cars.specs: json dạng key → value tự do, vd. {"engine":"2.0L","seats":5}.

Tổng số bảng: 10 = 9 bảng nghiệp vụ (users, car_categories, cars, car_images, services, inquiries, inquiry_notes, messages, appointments) + activity_logs.

car_categories: dòng xe Toyota (ví dụ Vios, Camry, Corolla Cross, Fortuner, Hilux); không có cột brand vì showroom chỉ bán xe Toyota. cars.name là tên phiên bản, ví dụ Vios 1.5G CVT.

3   Eloquent Relationships (Laravel)

// User
hasMany Inquiry (as customer_id)
hasMany Inquiry (as assigned_to)
hasMany Appointment (as customer_id)
hasMany Appointment (as assigned_to)

// CarCategory
hasMany Car
Car → belongsTo CarCategory
Car → hasMany CarImage
Car → hasMany Inquiry
Car → hasMany Appointment

// Inquiry
belongsTo User (customer)
belongsTo User (assignedStaff)
belongsTo Car (nullable)
hasMany Message
hasMany InquiryNote
hasMany Appointment

// Message
belongsTo Inquiry
belongsTo User (sender)

// InquiryNote
belongsTo Inquiry
belongsTo User (author)

// Appointment
belongsTo User (customer)
belongsTo User (assignedStaff)
belongsTo Inquiry (nullable)
belongsTo Car (nullable — for viewing)
belongsTo Service (nullable — for maintenance)

// Service
hasMany Appointment

4   Middleware và Route Groups

4.1 Nhóm route theo middleware

// Public — không cần auth
Route::group([], fn() => [danh sach xe, chi tiet xe, API trang thai xe, form tu van, form dat lich]);

// Auth required
Route::middleware(["auth", "ensure.active"])->group(fn() => [
    chat/tin nhan, lich su yeu cau, lich su lich hen, doi gio lich cua minh, huy lich cua minh
]);

// Staff only
Route::middleware(["auth", "ensure.active", "role:sales,admin"])->group(fn() => [
    ho so duoc giao, ghi chu noi bo, bat dau xu ly, dong/mo lai ho so, xac nhan lich, cap nhat ket qua
]);

// Admin only
Route::middleware(["auth", "ensure.active", "role:admin"])->group(fn() => [
    CRUD xe/danh muc/dich vu, quan ly tai khoan, phan cong, xem log
]);

4.2 Danh sách Middleware cần viết

| Middleware | Mô tả |
| --- | --- |
| EnsureAccountActive | Chặn tài khoản is_active = false, redirect login với thông báo |
| RoleMiddleware | Kiểm tra user->role có trong danh sách được phép |
| EnsureOwnInquiry | Khách chỉ xem inquiry của chính mình |
| EnsureAssignedStaff | Nhân viên chỉ xử lý hồ sơ được giao |
| ThrottleFormSubmission | Rate limit: max 3 lần gửi form / 10 phút / IP — áp dụng cho form tư vấn và cả 2 form đặt lịch |

5   Chức năng danh mục xe — Tìm kiếm, Lọc, Phân trang

5.1 Trang danh sách GET /xe — Query params

| Param | Kiểu | Mô tả |
| --- | --- | --- |
| q | string, max 100 | Tìm theo tên xe, mã xe (LIKE %q%) |
| category | slug (slug không tồn tại → kết quả rỗng) | Lọc theo dòng xe |
| status | available · sold | Tình trạng xe |
| price_min | int (VND) | Giá từ |
| price_max | int (VND) | Giá đến (phải ≥ price_min, nếu không → lỗi validate) |
| sort | price_asc · price_desc · newest | Sắp xếp kết quả (mặc định newest) |
| page | int | Trang hiện tại |

Output: 12 xe/trang, dùng paginate(12) Laravel. URL giữ nguyên filter khi chuyển trang.

Không có kết quả → hiển thị empty state "Không tìm thấy xe phù hợp" + nút xóa bộ lọc.

Xe hiển thị công khai = xe is_visible = true VÀ dòng xe is_visible = true. Xe/dòng xe bị ẩn → không xuất hiện trong danh sách, truy cập URL chi tiết (/xe/{id}) trả 404.

Xe status = sold → hiển thị nhãn "Đã bán", vẫn xem được chi tiết nhưng không có nút tư vấn và không có nút đặt lịch.

5.2 Nút hành động theo trạng thái xe (trang chi tiết)

| Trạng thái xe | Nút hiển thị |
| --- | --- |
| available | Gửi yêu cầu tư vấn  ·  Đặt lịch xem xe |
| reserved | Xem thông tin (có thể đặt tư vấn, không đặt lịch xem) |
| sold | Đã bán — Xem xe khác (không tư vấn, không đặt lịch; server cũng từ chối) |

6   Tài khoản và xác thực

6.1 Đăng ký — Validation Rules

| Trường | Rule |
| --- | --- |
| name | required · string · max:100 |
| email | required · email · unique:users · max:255 |
| phone | required · regex VN /^(0[3-9])\d{8}$/ · max:11 |
| password | required · min:8 · confirmed |

Tạo user với role = customer, is_active = true.

Đăng nhập luôn sau khi đăng ký thành công.

6.2 Đăng nhập — Validation Rules

| Trường | Rule |
| --- | --- |
| email | required · email |
| password | required |

Rate limit: 5 lần thất bại / phút / IP → trả lỗi 429.

Sau đăng nhập: kiểm tra is_active, nếu false → đăng xuất ngay, thông báo "Tài khoản đã bị khóa".

Không có quên mật khẩu / xác thực email trong phạm vi. Admin đặt mật khẩu ban đầu khi tạo tài khoản nhân viên.

Admin khóa/mở khóa được mọi tài khoản (customer và sales), không xóa cứng.

7   Luồng tư vấn và chat

7.1 Form gửi yêu cầu tư vấn — Validation Rules

| Trường | Rule |
| --- | --- |
| contact_name | required · string · max:100 |
| contact_phone | required · regex VN /^(0[3-9])\d{8}$/ |
| content | required · string · min:10 · max:2000 |
| car_id | nullable · exists:cars,id · xe hiển thị (xe và dòng xe is_visible = true) · status khác sold |
| email | nullable · email · max:255 |

Nếu đã login → gắn customer_id. Nếu vãng lai → customer_id = null.

Lưu inquiry status = new, sinh mã yêu cầu (dạng YC-XXXXXXXX). Trả về thông báo thành công + mã yêu cầu.

Rate limit: 3 yêu cầu / 10 phút / IP (dùng chung ThrottleFormSubmission với form đặt lịch). Vượt → 429.

7.2 Trạng thái inquiry

NEW → ASSIGNED → IN_PROGRESS → CLOSED
          ↑                       ↓  (Admin/nhân viên được giao mở lại)
          └───────────────────────┘

ASSIGNED → IN_PROGRESS: tự động khi nhân viên được giao gửi tin nhắn đầu tiên hoặc bấm "Bắt đầu xử lý" (cái nào đến trước).

Mở lại hồ sơ CLOSED → quay về ASSIGNED, xóa close_result và closed_at.

7.3 Kết quả khi đóng hồ sơ

da_tu_van (Đã tư vấn xong)

khach_khong_tiep_tuc (Khách không tiếp tục)

khach_giao_dich_truc_tiep (Khách giao dịch tại showroom)

khong_lien_he_duoc (Không liên hệ được)

7.4 Chat WebSocket — Laravel Reverb

Công nghệ: Laravel Reverb (server) + Laravel Echo (browser).

Điều kiện: khách phải có tài khoản, đã đăng nhập và là chủ hồ sơ mới chat được; chỉ chat khi hồ sơ ở trạng thái assigned hoặc in_progress (new và closed chỉ đọc).

Gửi tin qua HTTP POST có CSRF + auth; server lưu DB → broadcast event → Echo đẩy tới client.

Kênh: private-inquiry.{inquiry_id}.v{access_version} — chỉ khách sở hữu, nhân viên được giao, admin.

Thu hồi quyền: đổi nhân viên hoặc khóa tài khoản → tăng access_version → kênh cũ hết hiệu lực.

Mất kết nối → hiện banner → nối lại → tải tin còn thiếu → tiếp tục nhận event.

Chống trùng: client_message_id (UUID) → DB ràng buộc unique (sender_id, client_message_id). Gửi lại cùng ID → server trả lại tin đã lưu (200), không tạo bản ghi mới, không broadcast lại.

7.5 Validation tin nhắn chat

| Trường | Rule |
| --- | --- |
| content | required · string · min:1 · max:1000 |
| client_message_id | required · uuid (tính duy nhất do ràng buộc DB, không dùng rule unique — gửi lặp trả tin cũ) |

7.6 Điều kiện nghiệm thu chat

Demo 2 tab/thiết bị: tin 2 chiều xuất hiện < 2 giây (môi trường ổn định).

Reload trang vẫn đủ lịch sử.

Mất mạng → nối lại → không mất, không nhân đôi tin.

Truy cập kênh của người khác → bị từ chối 403.

8   Lịch hẹn

8.1 Form đặt lịch xem xe — Validation Rules

| Trường | Rule |
| --- | --- |
| contact_name | required · string · max:100 |
| contact_phone | required · regex VN |
| car_id | required · exists:cars · status = available · xe và dòng xe is_visible = true |
| requested_at | required · date · after:now · before:+6 months |
| note | nullable · max:500 |

8.2 Form đặt lịch bảo dưỡng — Validation Rules

| Trường | Rule |
| --- | --- |
| contact_name | required · string · max:100 |
| contact_phone | required · regex VN |
| service_id | required · exists:services · allow_booking = true · is_visible = true |
| customer_car_info | required · string · max:200 (dòng xe, biển số nếu có) |
| requested_at | required · date · after:now · before:+6 months |
| note | nullable · max:500 |

8.3 Luồng xác nhận lịch

PENDING → CONFIRMED → COMPLETED
    ↓            ↓
CANCELLED    CANCELLED / NO_SHOW

Nhân viên nhập confirmed_at (giờ đã thống nhất với khách).

Phụ trách lịch: Admin phân công assigned_to (mặc định lấy từ inquiry.assigned_to nếu lịch gắn inquiry_id); nhân viên chỉ xác nhận lịch được giao; Admin xác nhận thay được.

Không xác nhận nếu nhân viên phụ trách (assigned_to) đã có lịch confirmed khác lệch dưới 60 phút so với giờ xác nhận mới (chênh đúng 60 phút vẫn hợp lệ); kiểm tra DB có lock.

Dùng DB::transaction() khi kiểm tra + ghi để tránh race condition.

Đổi lịch: lịch confirmed → khách (đã đăng nhập, chủ lịch) gửi giờ mới → về pending, đặt confirmed_at = null (lưu lý do + giờ cũ vào note).

Hoàn tất: nhân viên đánh dấu completed sau khi khách đã đến. Không tự chuyển khi qua giờ.

Hủy: khách hủy lịch của mình (pending/confirmed) hoặc nhân viên/Admin hủy; bắt buộc ghi cancel_reason. No_show: nhân viên đánh dấu từ confirmed. completed, cancelled, no_show là trạng thái cuối.

9   Quản trị (Admin)

9.1 Validation CRUD Xe

| Trường | Rule |
| --- | --- |
| category_id | required · exists:car_categories |
| code | required · string · max:50 · unique:cars (bỏ qua bản đang sửa) |
| name | required · string · max:200 |
| year | required · integer · min:2000 · max:2030 |
| price | required · numeric · min:1000000 |
| color | required · string · max:100 |
| images[] | nullable · array · max:10 files |
| images.* | image · mimes:jpg,jpeg,png,webp · max:5120 (KB) |
| status | required · in:available,reserved,sold |
| is_visible | boolean |
| specs | nullable · array (tối đa 30 mục, key → value) |

Xe đã có trong inquiries hoặc appointments → chỉ ẩn (is_visible = false), không xóa cứng. Dòng xe còn xe và dịch vụ đã có lịch hẹn cũng chỉ ẩn, không xóa.

Ẩn dòng xe (car_category) → tất cả xe thuộc dòng cũng ẩn khỏi trang công khai (xử lý bằng điều kiện truy vấn, không ghi đè is_visible từng xe).

Tên file ảnh do server tạo, không lấy tên file của người dùng. Không cho tải file thực thi.

Tổng số ảnh của một xe tối đa 10 (ảnh đã có + ảnh tải lên); mỗi xe đúng 1 ảnh is_primary (mặc định ảnh đầu tiên), sort_order do Admin sắp xếp.

CRUD dòng xe (dòng Toyota): name required max:100; slug unique tự sinh từ name; is_visible boolean. CRUD dịch vụ: name required max:150; type in:maintenance,warranty,other; allow_booking, is_visible boolean.

Quản lý tài khoản: Admin tạo nhân viên (đặt mật khẩu ban đầu), khóa/mở khóa mọi tài khoản. Khóa → access_version++ trên inquiry liên quan; hồ sơ của nhân viên bị khóa giữ nguyên assigned_to đến khi Admin chuyển giao.

9.2 Nhật ký hoạt động (activity_logs) — tự động ghi cho:

Thêm/sửa/ẩn/đổi tình trạng xe

Tạo/khóa tài khoản nhân viên

Phân công / chuyển người phụ trách

Đóng hồ sơ tư vấn (kèm kết quả)

Xác nhận/hủy/hoàn tất lịch hẹn

10   Giao diện — Thiết kế & Responsive

10.1 Design direction

Phong cách: Cao cấp, tối giản, tập trung ảnh xe lớn.

Màu chủ đạo: Đen/trắng/vàng đồng hoặc xanh navy — chốt 1 bộ màu ngay tuần 1.

Font: 1 font sans-serif hiện đại (Inter, Nunito hoặc Be Vietnam Pro từ Google Fonts).

CSS Framework: Tailwind CSS hoặc Bootstrap 5 — chốt ngay, không dùng lẫn lộn.

10.2 Responsive Breakpoints

| Breakpoint | Mô tả | Màn hình bắt buộc phải đúng |
| --- | --- | --- |
| Mobile (< 768px) | 1 cột | Danh sách xe, form tư vấn, chat |
| Tablet (768–1024px) | 2 cột | Danh sách xe, chi tiết |
| Desktop (> 1024px) | 3–4 cột | Danh sách xe, admin panel |

10.3 Trạng thái UI bắt buộc

| Trạng thái | Mô tả |
| --- | --- |
| Loading | Skeleton hoặc spinner khi fetch dữ liệu |
| Empty | Text + icon khi không có dữ liệu (danh sách rỗng, không có tin nhắn) |
| Error | Thông báo lỗi tại trường (validation) hoặc toast khi lỗi server |
| Success | Toast/alert xanh sau khi gửi form thành công |
| Disabled | Nút submit vô hiệu hóa khi đang gửi (chống click nhiều lần) |

10.4 Blade Layout & Component

layouts/
  app.blade.php        — Layout chính khách (header, nav, footer)
  admin.blade.php      — Layout admin/staff (sidebar, topbar)
  auth.blade.php       — Layout đăng nhập/đăng ký

components/
  car-card.blade.php          — Card xe dùng ở danh sách
  status-badge.blade.php      — Badge trạng thái
  pagination.blade.php        — Pagination tùy chỉnh (giữ query params)
  alert.blade.php             — Thông báo thành công/lỗi
  image-gallery.blade.php     — Gallery ảnh xe

11   AJAX — Điểm sáng tạo (10%)

| Tính năng | Kỹ thuật | Ưu tiên |
| --- | --- | --- |
| Chat WebSocket (Reverb + Echo) | Broadcasting | 🔴 Bắt buộc |
| Lọc xe không reload trang | fetch() + history.pushState() | 🟡 Nên có |
| Kiểm tra xe available khi đặt lịch | AJAX GET /api/xe/{id}/status | 🟡 Nên có |
| Badge tin chưa đọc tự cập nhật | Echo channel event (cần thêm cột read_at cho messages nếu làm) | 🟢 Bonus |
| Xem trước ảnh khi upload xe | FileReader API | 🟢 Bonus |

12   Kịch bản kiểm thử (nghiệm thu)

| # | Kịch bản | Kết quả mong đợi |
| --- | --- | --- |
| 1 | Khách vãng lai tìm xe, lọc theo giá và dòng xe | Kết quả đúng, URL thay đổi, pagination hoạt động |
| 2 | Khách vãng lai gửi form tư vấn thiếu SĐT | Hiện lỗi tại trường, không tạo hồ sơ |
| 3 | Khách gửi form 4 lần liên tiếp trong 10 phút | Lần 4 bị chặn (429) |
| 4 | Admin phân công nhân viên A → A xử lý | A thấy hồ sơ, B không thấy |
| 5 | Admin chuyển hồ sơ sang nhân viên B | A mất quyền, B xem được lịch sử, kênh chat cũ bị đóng |
| 6 | Khách và nhân viên chat 2 tab | Tin xuất hiện < 2 giây, không reload |
| 7 | Ngắt mạng → nối lại → gửi tiếp | Không mất tin, không nhân đôi |
| 8 | Khách đặt lịch xem xe đã bán | Bị từ chối với thông báo rõ |
| 9 | Nhân viên xác nhận 2 lịch cùng giờ | Lần 2 bị từ chối do trùng giờ |
| 10 | Khách A truy cập URL hồ sơ của khách B | Bị chặn 403 |
| 11 | Xe đang có trong hồ sơ → admin cố xóa | Bị chặn, chỉ có thể ẩn |
| 12 | Admin ẩn dòng xe → các xe thuộc dòng đó | Xe biến mất khỏi trang công khai |
| 13 | Khách gửi form tư vấn cho xe đã bán | Bị từ chối, thông báo rõ |
| 14 | Gửi lại cùng client_message_id | Không tạo bản ghi mới, không broadcast lại |
| 15 | Khách đổi giờ lịch đã confirmed | Về pending, note lưu lý do + giờ cũ, confirmed_at = null |
| 16 | Mở lại hồ sơ đã đóng | Về assigned, xóa close_result/closed_at, chat dùng lại được |

13   Phân công công việc — 5 người

Hoàng = quản lý repo, kiến trúc hệ thống, review PR, code backend cốt lõi

Đạt = toàn bộ frontend (Blade + CSS + AJAX filter)

13.1 Bảng phân công chi tiết

| Người | Vai trò | Module chính | Bàn giao |
| --- | --- | --- | --- |
| Hoàng | Repo Manager + Backend Lead | Setup Laravel, DB migration, Auth (đăng ký/đăng nhập), Middleware, Activity Log, phân quyền toàn hệ thống, review toàn bộ PR | Migration + Seeder hoàn chỉnh · Middleware · Auth routes · README cài đặt |
| Đạt | Frontend Lead | Layout Blade (app/admin/auth), component dùng chung, trang chủ, danh sách xe, chi tiết xe, form tư vấn + lịch hẹn, giao diện chat phía khách, responsive, AJAX filter | Tất cả Blade views · CSS/Tailwind · Responsive pass trên mobile |
| TV3 | Backend Xe & Catalog | CRUD dòng xe, xe, ảnh (upload, validate, tên file server-side), giá, tình trạng, tìm kiếm, lọc, phân trang, màn hình admin xe | Migration xe · Controller + Policy xe · API trả data cho Đạt |
| TV4 | Backend Tư vấn & Chat | CRUD inquiry, phân công, ghi chú nội bộ (bảng inquiry_notes), trạng thái, WebSocket Reverb config, Message model, phân quyền kênh, chống trùng tin, màn hình tư vấn nhân viên | Migration inquiries + inquiry_notes + messages · Reverb chạy được · API chat · Event broadcast |
| TV5 | Backend Lịch hẹn & Dịch vụ | CRUD dịch vụ, lịch xem xe, lịch bảo dưỡng, xác nhận không chồng giờ (DB lock + transaction), đổi/hủy lịch, màn hình lịch admin/nhân viên | Migration services + appointments · Logic xung đột giờ · Controller lịch |

13.2 Ma trận tiêu chí → Người phụ trách

| Tiêu chí | Trọng số | Người chịu trách nhiệm chính |
| --- | --- | --- |
| CRUD + Auth + Validation + Pagination + Search | 40% | Hoàng (Auth) + TV3 (xe) + TV4 (tư vấn) + TV5 (lịch) |
| Eloquent ORM + Migration + Blade + Middleware | 25% | Hoàng (setup, Middleware) + TV3/4/5 (Migration riêng) + Đạt (Blade) |
| Giao diện đẹp, Responsive, thân thiện | 15% | Đạt (toàn bộ) |
| Báo cáo, Use Case, ERD, screenshot, thuyết trình | 10% | Hoàng (tổng hợp ERD, kiến trúc) + cả nhóm viết phần mình |
| AJAX, WebSocket, tính năng nâng cao | 10% | TV4 (WebSocket) + Đạt (AJAX filter) + TV3 (AJAX check xe) |

13.3 Timeline thực hiện

| Giai đoạn | Nội dung | Mục tiêu cuối giai đoạn |
| --- | --- | --- |
| Tuần 1<br>(Setup & Chốt) | Hoàng: repo, migration users, seeder. Đạt: mockup trang chủ. TV3: migration + seeder xe. TV4: thử Reverb môi trường. TV5: migration services + appointments | DB sync, migration sạch, auth cơ bản hoạt động |
| Tuần 2<br>(Core Modules) | Hoàng: Middleware + phân quyền + activity_log. Đạt: Layout Blade + danh sách + chi tiết xe. TV3: CRUD xe + search + filter. TV4: Inquiry CRUD + phân công. TV5: CRUD dịch vụ + tạo lịch | Mỗi module có CRUD hoàn chỉnh chạy với data thật |
| Tuần 3<br>(Chat & Tích hợp) | TV4: WebSocket hoàn chỉnh (auth kênh + retry + chống trùng). Đạt: giao diện chat + AJAX filter xe. TV3: màn hình admin xe. TV5: màn hình lịch + đổi/hủy lịch. Hoàng: review + merge PR | Luồng tư vấn + chat end-to-end chạy được |
| Tuần 4<br>(Polish & Báo cáo) | Đạt: responsive mobile + error/empty/loading states + polish UI. Hoàng: test toàn luồng + README + ERD. TV3/4/5: kiểm thử phần mình + viết báo cáo module | Chạy sạch trên máy khác theo README, báo cáo hoàn chỉnh |

13.4 Quy tắc Git

# Nhánh chính — luôn chạy được
main

# Mỗi tính năng dùng nhánh riêng
feature/auth-middleware          # Hoàng
feature/car-crud-search          # TV3
feature/inquiry-chat-websocket   # TV4
feature/appointment-schedule     # TV5
feature/frontend-layout          # Đạt

# Commit message format
feat: thêm CRUD xe với upload ảnh
fix: sửa lỗi validate giờ hẹn quá khứ
refactor: tách middleware RoleCheck

Mỗi PR phải có: mô tả đã làm gì + cách test.

Ít nhất 1 người review trước khi merge vào main.

Không commit: .env, vendor/, node_modules/, storage/app/ chứa ảnh thật.

14   Cấu trúc thư mục Laravel (gợi ý)

app/
  Http/
    Controllers/
      Auth/                    — Hoàng
      CarController.php        — TV3
      CategoryController.php   — TV3
      InquiryController.php    — TV4
      MessageController.php    — TV4
      AppointmentController.php — TV5
      ServiceController.php    — TV5
      Admin/
        UserController.php     — Hoàng
        LogController.php      — Hoàng
    Middleware/
      EnsureAccountActive.php  — Hoàng
      RoleMiddleware.php       — Hoàng
      EnsureOwnInquiry.php     — TV4
      EnsureAssignedStaff.php  — Hoàng
      ThrottleFormSubmission.php — Hoàng
    Requests/
      StoreInquiryRequest.php
      StoreAppointmentRequest.php
      StoreCarRequest.php
  Models/
    User, Car, CarCategory, CarImage   — Hoàng + TV3
    Inquiry, InquiryNote, Message      — TV4
    Appointment, Service               — TV5
    ActivityLog                        — Hoàng
  Events/
    MessageSent.php           — TV4

resources/views/
  layouts/                    — Đạt
  components/                 — Đạt
  auth/                       — Đạt + Hoàng
  cars/                       — Đạt (view) + TV3 (data)
  inquiries/                  — Đạt (customer) + TV4 (staff)
  appointments/               — Đạt (form) + TV5 (staff view)
  admin/                      — TV3/4/5 (màn hình admin riêng)

database/
  migrations/    — mỗi người tạo migration của module mình
  seeders/
    DatabaseSeeder.php   — Hoàng tổng hợp
    UserSeeder.php       — Hoàng
    CarSeeder.php        — TV3
    ServiceSeeder.php    — TV5

15   Checklist nộp bài

Mã nguồn & Cài đặt

☐  Code chạy được sau: composer install, npm install, php artisan migrate --seed

☐  Không có .env, vendor, node_modules trong repo

☐  .env.example có đủ biến: APP_KEY, DB_*, REVERB_*, BROADCAST_CONNECTION=reverb (Laravel 11+; BROADCAST_DRIVER là tên cũ)

☐  README hướng dẫn cài đặt + chạy Reverb + chạy queue (nếu dùng)

☐  README ghi phiên bản PHP 8.2+ / Laravel 11+ và giới hạn upload PHP (upload_max_filesize ≥ 5M, post_max_size ≥ 50M)

Database

☐  Tất cả migration chạy sạch, không conflict

☐  Seeder tạo: 1 admin, 2 nhân viên, 3 khách, 10+ xe Toyota (thuộc ≥ 3 dòng xe), 3+ dịch vụ

Chức năng (40%)

☐  CRUD xe có upload ảnh, validate, phân trang, tìm kiếm, lọc

☐  Auth: đăng ký (validate), đăng nhập (rate limit), đăng xuất

☐  Phân quyền: khách A không xem hồ sơ B, nhân viên chỉ xử lý được giao

☐  Inquiry: tạo, phân công, chat, đóng với kết quả

☐  Appointment: cả 2 loại, xác nhận (chặn chồng giờ), đổi, hủy, hoàn tất

☐  Admin: CRUD xe/dịch vụ, quản lý tài khoản nhân viên, phân công

Công nghệ (25%)

☐  Migration đủ quan hệ, ràng buộc, index

☐  Eloquent: có relationship, eager loading (tránh N+1)

☐  Middleware hoạt động đúng (kiểm tra bằng Postman hoặc route test)

☐  Blade có layout, component tái sử dụng

☐  FormRequest validate từng form

Giao diện (15%)

☐  Responsive: mobile, tablet, desktop

☐  Không có nút bị vỡ layout trên mobile

☐  Loading / empty / error / success state đủ

Báo cáo (10%)

☐  ERD đúng với code

☐  Use Case diagram

☐  Mô tả luồng nghiệp vụ chính

☐  Screenshot các màn hình chính

☐  Kết quả kiểm thử (16 kịch bản ở mục 12)

☐  Đóng góp từng người (liên kết với commit Git)

Sáng tạo (10%)

☐  Chat WebSocket 2 chiều (Laravel Reverb)

☐  AJAX filter danh sách xe không reload

☐  (Bonus) AJAX check trạng thái xe khi đặt lịch

16   Phần mở rộng (sau khi nghiệm thu xong)

Nếu nhóm còn thời gian và muốn điểm sáng tạo cao hơn, có thể thêm theo thứ tự ưu tiên:

Dự toán lăn bánh đơn giản — chọn tỉnh/thành, hiển thị ước tính trước bạ (dữ liệu tĩnh từ seeder, ghi rõ "ước tính")

So sánh 2 xe — bảng thông số song song

Yêu thích xe — AJAX thêm/xóa, xem danh sách yêu thích

Xuất báo giá PDF (dùng barryvdh/laravel-dompdf)

17   Lịch sử thay đổi v5 → v6

Rate limit form thống nhất 3 lần / 10 phút / IP, áp cho cả form tư vấn và đặt lịch (sửa mục 4.2; v5 ghi 3 lần/phút).

Bảng inquiries: thêm code, car_id, close_result, closed_at, access_version (mục 2.3).

Thêm bảng inquiry_notes cho ghi chú nội bộ; tổng 10 bảng gồm activity_logs (mục 2, 3).

Chat: đổi client_msg_id → client_message_id; unique (sender_id, client_message_id); gửi lặp trả tin cũ, không báo lỗi (mục 7.4, 7.5).

Lịch hẹn: Admin phân công assigned_to; trùng giờ khi chênh < 60 phút; đổi lịch đặt confirmed_at = null (mục 8.3).

Vòng đời inquiry: điều kiện sang in_progress, mở lại về assigned, chat chỉ khi assigned/in_progress (mục 7.2, 7.4).

Xe sold không nhận tư vấn lẫn đặt lịch (mục 5, 7.1).

Danh sách xe: category = slug, sort mặc định newest, price_min ≤ price_max, chi tiết /xe/{id}, xe công khai = xe và dòng xe cùng hiển thị (mục 5).

CRUD: bổ sung status/is_visible/specs, quy tắc ảnh, validate dòng xe và dịch vụ; có dữ liệu liên quan thì chỉ ẩn (mục 9).

Tài khoản: khóa được mọi tài khoản, hồ sơ giữ assigned_to đến khi chuyển giao, Admin đặt mật khẩu ban đầu (mục 6, 9).

services.type: maintenance · warranty · other (mục 2.2).

Eloquent: bổ sung quan hệ còn thiếu và InquiryNote (mục 3); thêm EnsureAssignedStaff vào cấu trúc thư mục (mục 14).

Công nghệ: PHP 8.2+, Laravel 11+; BROADCAST_CONNECTION thay BROADCAST_DRIVER; ghi giới hạn upload PHP (mục 15).

Kịch bản kiểm thử: 12 → 16 (mục 12).

Chốt sản phẩm: showroom chỉ bán xe Toyota; bỏ cột brand của car_categories; dòng xe = dòng Toyota; seeder dùng xe Toyota (mục 0, 2, 9, 15).
