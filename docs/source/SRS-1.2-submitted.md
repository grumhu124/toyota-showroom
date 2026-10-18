# SRS — Website Showroom Xe Toyota

| | |
|---|---|
| **Môn học** | Lập trình mã nguồn mở |
| **Công nghệ** | PHP 8.2+ · Laravel 11+ · MySQL · Blade · Laravel Reverb + Echo (WebSocket) |
| **Nguồn** | *Luồng nghiệp vụ website bán xe v5* (chốt 13/10/2026) |
| **Phiên bản SRS** | 1.2 — 16/10/2026 (loại mâu thuẫn của v5; chốt sản phẩm là xe Toyota) |
| **Đối tượng đọc** | Nhóm Back-end (Hoàng, TV3, TV4, TV5); Frontend (Đạt) dùng làm hợp đồng dữ liệu |

> **Quy ước đọc tài liệu**
> - Bản 1.1 này **đã giải quyết toàn bộ điểm mâu thuẫn/thiếu của v5**. Chỗ nào khác v5 hoặc bổ sung thêm được gắn nhãn **(D-xx)**; đối chiếu "v5 ghi gì → SRS chốt gì" ở [mục 10](#10-quyết-định-thiết-kế-đã-áp-dụng).
> - Nội dung không gắn nhãn = lấy nguyên từ luồng nghiệp vụ v5.
> - Muốn đổi một quyết định: sửa dòng D-xx ở mục 10 và các chỗ có nhãn D-xx tương ứng.
> - ID yêu cầu (`FR-…`, `BR-…`) dùng để ghi vào commit/PR và test case.

---

## 1. Giới thiệu

### 1.1 Mục đích
Mô tả yêu cầu chức năng, dữ liệu, phân quyền và ràng buộc của website showroom xe để Back-end triển khai thống nhất.

### 1.2 Phạm vi
Website giới thiệu xe mới **của hãng Toyota** tại **một** showroom (D-17): khách xem xe, xem giá công bố, gửi yêu cầu tư vấn, chat thời gian thực với nhân viên, đặt lịch xem xe hoặc bảo dưỡng. Thương lượng, hợp đồng, thanh toán, bàn giao thực hiện trực tiếp tại showroom — **ngoài hệ thống**.

### 1.3 Ngoài phạm vi (out of scope)
Giỏ hàng · Thanh toán online · Hóa đơn điện tử · Đặt cọc trực tuyến · Ký hợp đồng điện tử · Trả góp · Nhiều showroom · Nhiều hãng xe (chỉ bán xe Toyota) · So sánh xe · Xuất PDF báo giá · Dự toán lăn bánh.
Các mục So sánh xe, Dự toán lăn bánh, Yêu thích, Xuất PDF chỉ làm **sau nghiệm thu** (mục 11).

### 1.4 Thuật ngữ
| Thuật ngữ | Nghĩa |
|---|---|
| Inquiry | Yêu cầu tư vấn (hồ sơ tư vấn) |
| Appointment | Lịch hẹn: `viewing` (xem xe) hoặc `maintenance` (bảo dưỡng) |
| Khách vãng lai | Chưa đăng nhập (guest) |
| Khách đăng ký | `role = customer` |
| Nhân viên | `role = sales` |
| Admin | `role = admin` |

---

## 2. Actor và quyền

| Actor | Không cần login | Cần login | Ghi chú |
|---|---|---|---|
| Khách vãng lai | Xem xe, tìm kiếm, lọc, xem giá, gửi form tư vấn, đặt lịch | — | Nhận **mã yêu cầu** trên màn hình |
| Khách đăng ký | Như vãng lai | Chat, xem lịch sử yêu cầu, lịch hẹn, đổi/hủy lịch của mình | Tự đăng ký, role mặc định `customer` |
| Nhân viên (`sales`) | — | Xử lý hồ sơ **được giao**, chat, ghi chú nội bộ, xác nhận lịch | Admin tạo tài khoản |
| Admin | — | CRUD toàn bộ, phân công, quản lý tài khoản, xem log | Tài khoản tạo bằng seeder |

**Hệ quả thiết kế (BR-ACT):**
- BR-ACT-1: Khách vãng lai **không chat được, không xem lại, không đổi/hủy lịch** (không có tài khoản để định danh). Nhân viên liên hệ lại qua số điện thoại đã nhập.
- BR-ACT-2: Chat chỉ tồn tại trên inquiry có `customer_id` khác null.

### 2.1 Ma trận quyền chi tiết

| Hành động | Vãng lai | Customer | Sales | Admin |
|---|:-:|:-:|:-:|:-:|
| Xem/tìm/lọc danh sách xe, chi tiết xe (xe hiển thị) | ✔ | ✔ | ✔ | ✔ |
| Gửi form tư vấn / đặt lịch | ✔ | ✔ | — | — |
| Xem inquiry của mình | — | ✔ (own) | — | — |
| Xem/xử lý inquiry | — | — | chỉ được giao | tất cả |
| Chat trên inquiry | — | chủ inquiry | nhân viên được giao | ✔ |
| Ghi chú nội bộ | — | — | hồ sơ được giao | ✔ |
| Phân công / chuyển người phụ trách | — | — | — | ✔ |
| Đóng / mở lại inquiry | — | — | hồ sơ được giao | ✔ |
| Xem lịch hẹn của mình, hủy, đổi giờ | — | ✔ (own) | — | — |
| Xác nhận / hoàn tất / no_show / hủy lịch | — | — | lịch được giao (D-05) | ✔ |
| CRUD dòng xe, xe, dịch vụ | — | — | — | ✔ |
| Đổi tình trạng xe (`available/reserved/sold`) | — | — | — | ✔ |
| Tạo / khóa tài khoản nhân viên | — | — | — | ✔ |
| Xem `activity_logs` | — | — | — | ✔ |

---

## 3. Yêu cầu dữ liệu

### 3.1 ERD tóm tắt
```
users (id, name, email, phone, password, role, is_active, timestamps)
├──< inquiries (customer_id nullable, car_id nullable, assigned_to nullable, code, status, close_result, access_version, …)
│     ├──< messages (inquiry_id, sender_id, content, client_message_id, …)
│     ├──< inquiry_notes (inquiry_id, user_id, content, timestamps)
│     └──< appointments (inquiry_id nullable)
└──< appointments (customer_id nullable, assigned_to nullable)

car_categories (id, name, slug unique, description, is_visible, timestamps)
└──< cars (category_id, code unique, name, year, color, specs json, price, status, is_visible, timestamps)
      └──< car_images (car_id, path, sort_order, is_primary, timestamps)

services (id, name, type, description, allow_booking, is_visible, timestamps)
└──< appointments (service_id nullable)

activity_logs (id, user_id nullable, action, target_type, target_id, payload json, ip, timestamps)
```
Số bảng: **10** (9 bảng nghiệp vụ + `activity_logs`). v5 ghi "8 bảng" là đếm thiếu — chưa có chỗ lưu ghi chú nội bộ (D-03).

### 3.2 Đặc tả bảng
Cột ghi **(bổ sung)** là cột v5 chưa liệt kê nhưng bắt buộc có để chạy đúng luồng (D-02).

#### `users`
| Cột | Kiểu | Ràng buộc |
|---|---|---|
| id | bigint PK | |
| name | string(100) | required |
| email | string(255) | unique |
| phone | string(11) | required |
| password | string | hash |
| role | enum `customer`,`sales`,`admin` | default `customer` |
| is_active | boolean | default `true` |
| timestamps | | |

Không xóa cứng user; chỉ khóa (`is_active = false`).

#### `car_categories`
`id`, `name`, `slug` (unique), `description` (nullable), `is_visible` (default true), timestamps.

Dòng xe = dòng sản phẩm Toyota (ví dụ: Vios, Camry, Corolla Cross, Fortuner, Hilux). **Không có cột `brand`** vì showroom chỉ bán xe Toyota (D-17). `cars.name` là tên phiên bản, ví dụ `Vios 1.5G CVT`.

#### `cars`
| Cột | Kiểu | Ràng buộc |
|---|---|---|
| category_id | FK → car_categories | required |
| code | string(50) | unique |
| name | string(200) | |
| year | smallint | 2000–2030 |
| color | string(100) | |
| specs | json | nullable; object key→value tự do, vd. `{"engine":"2.0L","seats":5}` (D-09) |
| price | decimal(15,0) unsigned (bổ sung) | ≥ 1.000.000 (VND) |
| status | enum `available`,`reserved`,`sold` | default `available` |
| is_visible | boolean | default true |

Index: `(is_visible, status)`, `price`, `category_id`.

#### `car_images`
`id`, `car_id` (FK, cascade), `path`, `sort_order` (default 0), `is_primary` (mỗi xe tối đa 1 ảnh primary), timestamps.

#### `services`
`id`, `name`, `type` (enum `maintenance`,`warranty`,`other` — D-12), `description`, `allow_booking`, `is_visible`, timestamps.

#### `inquiries`
| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | PK | |
| code | string, unique (bổ sung) | **Mã yêu cầu** hiển thị cho khách, dạng `YC-` + 8 ký tự in hoa ngẫu nhiên, vd. `YC-7K2M9QXA` (D-02) |
| customer_id | FK users, nullable | null nếu vãng lai |
| car_id | FK cars, nullable (bổ sung) | Form có `car_id` |
| assigned_to | FK users, nullable | nhân viên phụ trách |
| contact_name, contact_phone, email(nullable), content | | từ form |
| status | enum `new`,`assigned`,`in_progress`,`closed` | default `new` |
| close_result | enum, nullable (bổ sung) | xem 4.4 |
| closed_at | datetime, nullable (bổ sung) | |
| access_version | unsigned int default 1 (bổ sung) | phục vụ thu hồi quyền kênh chat |
| timestamps | | |

Index: `status`, `(assigned_to, status)`, `customer_id`.

#### `inquiry_notes` (D-03)
`id`, `inquiry_id` (FK, cascade), `user_id` (FK — tác giả, nhân viên/admin), `content` (text, 1–2000 ký tự), timestamps. Khách **không bao giờ** đọc được bảng này.

#### `messages`
| Cột | Kiểu | Ràng buộc |
|---|---|---|
| inquiry_id | FK, cascade | |
| sender_id | FK users | |
| content | text | 1–1000 ký tự |
| client_message_id | char(36) UUID | **unique `(sender_id, client_message_id)`** (D-04) |
| timestamps | | |

Index: `(inquiry_id, id)` để tải tin theo `after_id`.

#### `appointments`
| Cột | Ghi chú |
|---|---|
| type | `viewing` / `maintenance` |
| customer_id, inquiry_id, car_id, service_id, assigned_to | đều nullable |
| customer_car_info | xe của khách (bảo dưỡng) |
| contact_name, contact_phone | |
| requested_at | giờ khách mong muốn |
| confirmed_at | nullable, giờ đã thống nhất |
| status | `pending`,`confirmed`,`completed`,`cancelled`,`no_show` (default `pending`) |
| note, cancel_reason | |

Ràng buộc nghiệp vụ theo `type`:
- `viewing` → bắt buộc `car_id`.
- `maintenance` → bắt buộc `service_id` và `customer_car_info`.

Index: `(assigned_to, status, confirmed_at)`, `customer_id`, `status`.

#### `activity_logs`
`id`, `user_id` (nullable), `action`, `target_type`, `target_id`, `payload` (json), `ip`, timestamps. Index: `(target_type, target_id)`, `user_id`, `created_at`.

### 3.3 Eloquent relationships
```
User         hasMany Inquiry (customer_id) · hasMany Inquiry (assigned_to)
             hasMany Appointment (customer_id) · hasMany Appointment (assigned_to)
CarCategory  hasMany Car
Car          belongsTo CarCategory · hasMany CarImage
             hasMany Inquiry · hasMany Appointment          ← bổ sung (D-13)
Inquiry      belongsTo User (customer) · belongsTo User (assignedStaff)
             belongsTo Car (nullable)                       ← bổ sung (D-13)
             hasMany Message · hasMany InquiryNote · hasMany Appointment
Message      belongsTo Inquiry · belongsTo User (sender)
InquiryNote  belongsTo Inquiry · belongsTo User (author)
Appointment  belongsTo User (customer) · belongsTo User (assignedStaff)
             belongsTo Inquiry (nullable) · belongsTo Car (nullable) · belongsTo Service (nullable)
Service      hasMany Appointment                            ← bổ sung (D-13)
```
Bắt buộc dùng eager loading (`with()`) ở mọi danh sách để tránh N+1.

---

## 4. Yêu cầu chức năng

### 4.1 Danh mục xe công khai (`GET /xe`)

**FR-CAT-1 — Query params**
| Param | Kiểu | Mô tả |
|---|---|---|
| `q` | string, max 100 | Tìm theo tên xe hoặc mã xe (`LIKE %q%`) |
| `category` | slug (D-08) | Lọc theo dòng xe; slug không tồn tại → kết quả rỗng |
| `status` | `available` \| `sold` | Tình trạng |
| `price_min`, `price_max` | int (VND) | Khoảng giá; nếu `min > max` → lỗi validate (D-08) |
| `sort` | `price_asc` \| `price_desc` \| `newest` | Mặc định `newest` (D-08) |
| `page` | int | Trang |

**FR-CAT-2** — `paginate(12)`, giữ nguyên filter trên URL phân trang (`withQueryString()`).
**FR-CAT-3** — Không có kết quả → dữ liệu rỗng; UI hiển thị "Không tìm thấy xe phù hợp" + nút xóa bộ lọc.
**FR-CAT-4** — Xe công khai = `cars.is_visible = true` **và** `car_categories.is_visible = true`. Truy cập chi tiết xe bị ẩn bằng URL → **404**. Hiện thực bằng query scope dùng chung (`scopePublic`), không lặp điều kiện ở từng controller.
**FR-CAT-5** — Hành động theo tình trạng xe (trang chi tiết):

| `status` | Cho phép |
|---|---|
| `available` | Gửi tư vấn · Đặt lịch xem xe |
| `reserved` | Xem thông tin · Gửi tư vấn · **Không** đặt lịch xem |
| `sold` | Nhãn "Đã bán" · Xem xe khác. **Không** tư vấn, **không** đặt lịch (D-07) |

**FR-CAT-6** — `GET /api/xe/{id}/status` trả trạng thái hiện tại của xe (AJAX khi đặt lịch). Xe ẩn → 404.

### 4.2 Tài khoản & xác thực

**FR-AUTH-1 Đăng ký**

| Trường | Rule |
|---|---|
| name | required · string · max:100 |
| email | required · email · unique:users · max:255 |
| phone | required · regex `/^(0[3-9])\d{8}$/` · max:11 |
| password | required · min:8 · confirmed |

Tạo user `role = customer`, `is_active = true`; **đăng nhập luôn** sau khi đăng ký.

**FR-AUTH-2 Đăng nhập** — `email` required·email, `password` required. Rate limit **5 lần thất bại / phút / IP → 429**. Sau khi xác thực: nếu `is_active = false` → đăng xuất ngay, thông báo "Tài khoản đã bị khóa".

**FR-AUTH-3 Đăng xuất** — `POST`, hủy session.

**FR-AUTH-4** — Tài khoản `sales` do Admin tạo; tài khoản `admin` do seeder. Không có tự đăng ký role khác `customer`. Quên mật khẩu/xác thực email: **không nằm trong v5** (D-14).

### 4.3 Yêu cầu tư vấn (Inquiry)

**FR-INQ-1 Gửi form (public)**

| Trường | Rule |
|---|---|
| contact_name | required · string · max:100 |
| contact_phone | required · regex VN `/^(0[3-9])\d{8}$/` |
| content | required · string · min:10 · max:2000 |
| car_id | nullable · exists:cars,id · xe hiển thị (FR-CAT-4) · `status != sold` (D-07) |
| email | nullable · email · max:255 |

- Đã login → `customer_id = auth id`; vãng lai → `null`.
- Lưu `status = new`, sinh `code`, trả thông báo thành công **kèm mã yêu cầu**.
- Rate limit **3 yêu cầu / 10 phút / IP** (→ 429) (D-01).

**FR-INQ-2 Phân công (Admin)** — gán/đổi `assigned_to` (chỉ user `sales`, đang `is_active`).
- Lần đầu: `new → assigned`.
- Đổi người: **tăng `access_version`** (thu hồi quyền kênh chat cũ), ghi log.
- Nhân viên cũ mất quyền xem hồ sơ ngay; nhân viên mới xem được toàn bộ lịch sử tin nhắn.

**FR-INQ-3 Ghi chú nội bộ** — nhân viên được giao/Admin thêm ghi chú vào bảng `inquiry_notes`; **khách không bao giờ thấy** (D-03).

**FR-INQ-4 Đóng hồ sơ** — nhân viên được giao/Admin đóng kèm `close_result` (bắt buộc), ghi `closed_at`, ghi log (kèm kết quả).

**FR-INQ-5 Mở lại** — Admin hoặc nhân viên được giao mở lại hồ sơ `closed` → về `assigned`, xóa `close_result` và `closed_at` (D-06).

**FR-INQ-6 Danh sách**
- Customer: chỉ inquiry của mình (`customer_id = auth id`).
- Sales: chỉ `assigned_to = auth id`.
- Admin: tất cả, lọc theo trạng thái/nhân viên.

### 4.4 Trạng thái & kết quả đóng

```
NEW ──assign──▶ ASSIGNED ──▶ IN_PROGRESS ──close──▶ CLOSED
                   ▲                                   │
                   └──────────── mở lại ───────────────┘
```
Điều kiện `ASSIGNED → IN_PROGRESS` (D-06): tự động khi nhân viên được giao **gửi tin nhắn đầu tiên** hoặc bấm "Bắt đầu xử lý" — cái nào đến trước.

`close_result` (giá trị lưu DB):
| Giá trị | Nghĩa |
|---|---|
| `da_tu_van` | Đã tư vấn xong |
| `khach_khong_tiep_tuc` | Khách không tiếp tục |
| `khach_giao_dich_truc_tiep` | Khách giao dịch tại showroom |
| `khong_lien_he_duoc` | Không liên hệ được |

### 4.5 Chat thời gian thực (Laravel Reverb + Echo)

**FR-CHAT-1** — Chỉ khách **đã đăng nhập** và là chủ inquiry mới chat; phía nhân viên là người được giao; Admin xem/chat được tất cả.
**FR-CHAT-2 Gửi tin** — `POST` (CSRF + auth + policy) → server lưu DB → broadcast event `MessageSent` → Echo đẩy tới client. Không broadcast trực tiếp từ client.
**FR-CHAT-3 Kênh** — `private-inquiry.{inquiry_id}.v{access_version}`. Callback `routes/channels.php` cho phép: chủ inquiry, nhân viên đang được giao, admin; **và** `access_version` trong tên kênh phải bằng giá trị hiện tại. Server luôn broadcast vào kênh của **version hiện tại**.
**FR-CHAT-4 Thu hồi quyền** — đổi nhân viên hoặc khóa tài khoản liên quan → `access_version++` ⇒ kênh cũ không còn nhận tin mới và không auth lại được.
**FR-CHAT-5 Mất kết nối** — client hiện banner → nối lại → gọi API tải tin còn thiếu (`after_id`) → tiếp tục nhận event.
**FR-CHAT-6 Chống trùng** — client sinh `client_message_id` (UUID); unique theo `(sender_id, client_message_id)`. Gửi lại cùng ID **không tạo bản ghi mới** (D-04).
**FR-CHAT-7 Validate tin**

| Trường | Rule |
|---|---|
| content | required · string · min:1 · max:1000 |
| client_message_id | required · uuid (**không** dùng rule `unique` — tính duy nhất do ràng buộc DB, xem D-04) |

**FR-CHAT-8 Trạng thái cho phép chat** (D-06): chat chỉ được khi `status ∈ {assigned, in_progress}`; `new` (chưa có nhân viên) và `closed` → chỉ đọc.

**Nghiệm thu chat:** 2 tab/thiết bị, tin 2 chiều < 2 giây (mạng ổn định) · reload vẫn đủ lịch sử · mất mạng → nối lại không mất/không nhân đôi · truy cập kênh người khác → 403.

### 4.6 Lịch hẹn (Appointment)

**FR-APT-1 Đặt lịch xem xe (public)**

| Trường | Rule |
|---|---|
| contact_name | required · string · max:100 |
| contact_phone | required · regex VN |
| car_id | required · exists:cars · `status = available` · xe hiển thị |
| requested_at | required · date · after:now · before:+6 months |
| note | nullable · max:500 |

**FR-APT-2 Đặt lịch bảo dưỡng (public)**

| Trường | Rule |
|---|---|
| contact_name | required · string · max:100 |
| contact_phone | required · regex VN |
| service_id | required · exists:services · `allow_booking = true` · `is_visible = true` |
| customer_car_info | required · string · max:200 (dòng xe, biển số nếu có) |
| requested_at | required · date · after:now · before:+6 months |
| note | nullable · max:500 |

Đã login → gắn `customer_id`; vãng lai → null. Tạo với `status = pending`. Áp dụng `ThrottleFormSubmission` cho cả hai form (D-01).

**FR-APT-3 Xác nhận** — nhân viên/Admin nhập `confirmed_at` (giờ đã thống nhất với khách): `pending → confirmed`.
- **Phụ trách lịch (D-05):** `assigned_to` do Admin phân công; nếu lịch có `inquiry_id` thì mặc định lấy `inquiry.assigned_to`. Nhân viên chỉ xác nhận lịch được giao; Admin xác nhận thay được và gán luôn người phụ trách. `inquiry_id` chỉ được gán khi khách đã login đặt lịch từ trang hồ sơ của chính mình (server kiểm inquiry thuộc khách).
- **Không được trùng giờ (D-05):** từ chối nếu nhân viên phụ trách đã có lịch `confirmed` khác với `|chênh lệch| < 60 phút` so với `confirmed_at` mới. Chênh đúng 60 phút là hợp lệ.
- Kiểm tra + ghi trong `DB::transaction()` có khóa để tránh race condition (gợi ý ở mục 8).

**FR-APT-4 Đổi lịch** — khách (đã login, chủ lịch) với lịch `confirmed` gửi giờ mới → về `pending`; lưu **lý do + giờ cũ vào `note`**; giờ mới vào `requested_at` (validate như FR-APT-1/2); `confirmed_at` đặt lại `null` (D-05).
**FR-APT-5 Hủy** — khách hủy lịch **của mình** (từ `pending`/`confirmed`), hoặc nhân viên/Admin hủy; lưu `cancel_reason`; `→ cancelled`.
**FR-APT-6 Hoàn tất** — nhân viên đánh dấu `completed` sau khi khách đã đến (từ `confirmed`). **Không tự chuyển khi qua giờ.**
**FR-APT-7 No-show** — nhân viên đánh dấu `no_show` (từ `confirmed`).

```
PENDING ──▶ CONFIRMED ──▶ COMPLETED
   │            ├──▶ NO_SHOW
   ▼            ▼
CANCELLED   CANCELLED          (CONFIRMED ──đổi lịch──▶ PENDING)
```
`completed`, `cancelled`, `no_show` là trạng thái cuối.

### 4.7 Quản trị (Admin)

**FR-ADM-1 CRUD Xe**

| Trường | Rule |
|---|---|
| category_id | required · exists:car_categories |
| code | required · string · max:50 · unique:cars (bỏ qua bản đang sửa) |
| name | required · string · max:200 |
| year | required · integer · min:2000 · max:2030 |
| price | required · numeric · min:1000000 |
| color | required · string · max:100 |
| images[] | nullable · array · max:10 files |
| images.* | image · mimes:jpg,jpeg,png,webp · max:5120 (KB) |

Bổ sung (D-09): `status` required · in:available,reserved,sold · `is_visible` boolean · `specs` nullable · array (tối đa 30 mục). **Tổng số ảnh của một xe tối đa 10** (ảnh đã có + ảnh tải lên). Mỗi xe đúng 1 ảnh `is_primary` (mặc định ảnh đầu tiên); `sort_order` do Admin sắp xếp.

- BR-ADM-1: Xe đã xuất hiện trong `inquiries` hoặc `appointments` → **chỉ ẩn** (`is_visible = false`), chặn xóa cứng.
- BR-ADM-2: Ẩn dòng xe → toàn bộ xe thuộc dòng biến mất khỏi trang công khai (thực hiện bằng điều kiện truy vấn FR-CAT-4, **không** ghi đè `is_visible` của từng xe).
- BR-ADM-3: Tên file ảnh do **server tạo** (không dùng tên gốc); không chấp nhận file thực thi; kiểm tra MIME theo nội dung.

**FR-ADM-2 CRUD Dòng xe & Dịch vụ** (D-09)
- Dòng xe (dòng Toyota): `name` required · string · max:100; `slug` unique, tự sinh từ `name` (bỏ qua bản đang sửa); `description` nullable · max:2000; `is_visible` boolean. Dòng xe còn xe → **chỉ ẩn**, không xóa.
- Dịch vụ: `name` required · string · max:150; `type` required · in:maintenance,warranty,other; `description` nullable · max:2000; `allow_booking`, `is_visible` boolean. Dịch vụ đã có lịch hẹn → **chỉ ẩn**, không xóa.
**FR-ADM-3 Quản lý tài khoản** — Admin tạo tài khoản `sales` (đặt mật khẩu ban đầu, D-14) và khóa/mở khóa **mọi** tài khoản (customer lẫn sales), không xóa cứng. Khóa tài khoản ⇒ `access_version++` trên mọi inquiry liên quan. Hồ sơ nhân viên bị khóa đang giữ vẫn giữ `assigned_to` cho tới khi Admin chuyển giao (D-10).
**FR-ADM-4 Phân công** — theo FR-INQ-2; phân công lịch hẹn theo D-05.
**FR-ADM-5 Nhật ký hoạt động** — tự động ghi `activity_logs` cho:
- Thêm/sửa/ẩn/đổi tình trạng xe
- Tạo/khóa tài khoản nhân viên
- Phân công / chuyển người phụ trách
- Đóng hồ sơ tư vấn (kèm kết quả)
- Xác nhận/hủy/hoàn tất lịch hẹn

Admin chỉ xem, không sửa/xóa log. Ghi qua một service/observer dùng chung (không rải `ActivityLog::create` khắp controller).

---

## 5. Phân quyền & Route groups

```php
// Public
Route::group([], fn() => [danh sách xe, chi tiết xe, API status xe, form tư vấn, form đặt lịch, đăng ký/đăng nhập]);

// Auth required
Route::middleware(['auth', 'ensure.active'])->group(fn() => [
  chat/tin nhắn, lịch sử yêu cầu, lịch sử lịch hẹn, đổi lịch của mình, hủy lịch của mình
]);

// Staff
Route::middleware(['auth', 'ensure.active', 'role:sales,admin'])->group(fn() => [
  hồ sơ được giao, ghi chú nội bộ, xác nhận lịch, cập nhật kết quả
]);

// Admin
Route::middleware(['auth', 'ensure.active', 'role:admin'])->group(fn() => [
  CRUD xe/danh mục/dịch vụ, quản lý tài khoản, phân công, xem log
]);
```
(v5 bỏ sót "đổi lịch của mình" ở nhóm Auth — đã bổ sung.)

### Middleware
| Middleware | Mô tả |
|---|---|
| `EnsureAccountActive` | Chặn `is_active = false`, redirect login kèm thông báo |
| `RoleMiddleware` | `user->role` phải thuộc danh sách cho phép |
| `EnsureOwnInquiry` | Khách chỉ xem inquiry của chính mình (sai → **403**) |
| `EnsureAssignedStaff` | Nhân viên chỉ xử lý hồ sơ được giao (Admin bỏ qua). Hoàng viết cùng nhóm Middleware, đặt trong `app/Http/Middleware/` (D-15) |
| `ThrottleFormSubmission` | Rate limit form: **3 lần / 10 phút / IP** (D-01) |

Khuyến nghị: dùng **Policy** (`InquiryPolicy`, `AppointmentPolicy`, `CarPolicy`) cho kiểm quyền theo bản ghi, middleware cho kiểm quyền theo nhóm route.

### Route chuẩn của dự án (v5 chỉ cố định `/xe` và `/api/xe/{id}/status`)
| Method | URL | Nhóm | Mục đích |
|---|---|---|---|
| GET | `/xe` | public | Danh sách (hỗ trợ trả JSON/partial cho AJAX filter) |
| GET | `/xe/{id}` | public | Chi tiết xe (D-08) |
| GET | `/api/xe/{id}/status` | public | Trạng thái xe |
| POST | `/tu-van` | public + throttle | Gửi yêu cầu tư vấn |
| POST | `/dat-lich/xem-xe`, `/dat-lich/bao-duong` | public + throttle | Đặt lịch |
| POST | `/dang-ky`, `/dang-nhap`, `/dang-xuat` | public/auth | Xác thực |
| GET | `/yeu-cau` , `/yeu-cau/{inquiry}` | auth (own) | Lịch sử/chi tiết yêu cầu |
| GET/POST | `/yeu-cau/{inquiry}/tin-nhan` | auth | Tải tin (`?after_id=`) / gửi tin |
| GET | `/lich-hen` | auth | Lịch hẹn của tôi |
| POST | `/lich-hen/{id}/huy`, `/lich-hen/{id}/doi-gio` | auth (own) | Hủy / đổi giờ |
| * | `/staff/...` | staff | Hồ sơ, ghi chú, đóng/mở, xác nhận/hoàn tất/no_show |
| * | `/admin/...` | admin | CRUD, tài khoản, phân công, logs |

### Mã lỗi
| Tình huống | HTTP |
|---|---|
| Validate sai | 422 (web: redirect back kèm lỗi theo trường) |
| Chưa đăng nhập | 401 / redirect login |
| Sai quyền, truy cập hồ sơ/kênh người khác | **403** |
| Xe/tài nguyên bị ẩn hoặc không tồn tại | 404 |
| Vượt rate limit | **429** |
| Trùng giờ khi xác nhận lịch | 422, thông báo "Nhân viên đã có lịch trong khoảng giờ này" |

---

## 6. Quy tắc nghiệp vụ tổng hợp

| ID | Quy tắc |
|---|---|
| BR-1 | Số điện thoại VN: `/^(0[3-9])\d{8}$/` (10 số, không nhận `+84`) |
| BR-2 | Xe ẩn/dòng xe ẩn không lộ ở danh sách, chi tiết, API, form (tư vấn/đặt lịch) |
| BR-3 | Đặt lịch xem chỉ cho xe `available`; `reserved`/`sold` bị từ chối với thông báo rõ. Xe `sold` cũng không nhận form tư vấn (D-07) |
| BR-4 | Xe có dữ liệu liên quan chỉ ẩn, không xóa cứng |
| BR-5 | Nhân viên chỉ thao tác trên hồ sơ/lịch được giao; Admin toàn quyền |
| BR-6 | Đổi người phụ trách / khóa tài khoản ⇒ `access_version++` |
| BR-7 | Lịch `confirmed` của cùng một nhân viên không được gần nhau dưới 60 phút |
| BR-8 | Lịch hẹn chỉ chuyển `completed` bằng thao tác thủ công của nhân viên |
| BR-9 | Mọi thao tác trong FR-ADM-5 phải ghi `activity_logs` |
| BR-10 | Nút submit bị vô hiệu khi đang gửi (UI) và server vẫn chống gửi lặp bằng rate limit / unique |
| BR-11 | Mọi xe thuộc hãng Toyota; hệ thống không lưu/hiển thị lựa chọn hãng (D-17) |

---

## 7. Yêu cầu phi chức năng

| Nhóm | Yêu cầu |
|---|---|
| Bảo mật | CSRF mọi form/POST; hash mật khẩu (bcrypt/argon mặc định Laravel); validate bằng FormRequest (`StoreInquiryRequest`, `StoreAppointmentRequest`, `StoreCarRequest`, …); kiểm quyền bằng Policy/Middleware; chống upload file thực thi; không commit `.env`, `vendor/`, `node_modules/`, ảnh thật trong `storage/app` |
| Hiệu năng | Eager loading chống N+1; index theo 3.2; phân trang 12 xe/trang |
| Realtime | Tin chat hiển thị < 2 giây (mạng ổn định) |
| Tin cậy | Chat không mất/không nhân đôi khi mất mạng; xác nhận lịch dùng transaction + khóa |
| Thời gian | Đặt `app.timezone = Asia/Ho_Chi_Minh`; mọi `requested_at`/`confirmed_at` hiểu theo giờ VN |
| Tiền tệ | VND, số nguyên, không số thập phân |
| Upload | 10 ảnh × 5 MB cần `upload_max_filesize ≥ 5M`, `post_max_size ≥ 50M` (mặc định PHP 2M/8M **không đủ**) — ghi vào README (D-11) |
| Giao diện (tham chiếu cho BE trả dữ liệu) | Có đủ trạng thái loading / empty / error / success / disabled; responsive mobile–tablet–desktop (Frontend: Đạt) |
| Ghi log | `activity_logs` theo FR-ADM-5 |

---

## 8. Gợi ý triển khai cho các điểm khó

**Xác nhận lịch không trùng giờ (FR-APT-3)** — khóa **dòng của nhân viên** làm mutex thay vì khóa khoảng thời gian (gap lock dễ không bắt được khoảng rỗng):
```php
DB::transaction(function () use ($appointment, $staffId, $confirmedAt) {
    User::whereKey($staffId)->lockForUpdate()->first();          // mutex theo nhân viên
    $conflict = Appointment::where('assigned_to', $staffId)
        ->where('status', 'confirmed')
        ->where('id', '!=', $appointment->id)
        ->where('confirmed_at', '>', $confirmedAt->copy()->subMinutes(60))
        ->where('confirmed_at', '<', $confirmedAt->copy()->addMinutes(60))
        ->exists();
    abort_if($conflict, 422, 'Nhân viên đã có lịch trong khoảng giờ này');
    $appointment->update(['status' => 'confirmed', 'confirmed_at' => $confirmedAt, 'assigned_to' => $staffId]);
    // ghi activity_log trong cùng transaction
});
```

**Chống trùng tin chat (FR-CHAT-6)** — dùng `firstOrCreate(['sender_id'=>…, 'client_message_id'=>…], [...])`; nếu bản ghi đã có thì trả lại bản ghi cũ (HTTP 200) và **không broadcast lại**. Dùng `broadcast(...)->toOthers()` để người gửi không nhận tin của chính mình hai lần.

**Kênh chat có version (FR-CHAT-3)**
```php
Broadcast::channel('inquiry.{id}.v{version}', function ($user, $id, $version) {
    $i = Inquiry::find($id);
    return $i && (int)$version === $i->access_version && (
        $user->role === 'admin' ||
        $i->customer_id === $user->id ||
        $i->assigned_to === $user->id
    );
});
```

**Ẩn xe theo dòng xe (FR-CAT-4)**
```php
public function scopePublic($q) {
    return $q->where('is_visible', true)
             ->whereHas('category', fn($c) => $c->where('is_visible', true));
}
```

---

## 9. Tiêu chí nghiệm thu

### 9.1 12 kịch bản đã chốt
| # | Kịch bản | Kết quả mong đợi |
|---|---|---|
| 1 | Vãng lai tìm xe, lọc theo giá và dòng xe | Kết quả đúng, URL thay đổi, pagination hoạt động |
| 2 | Vãng lai gửi form tư vấn thiếu SĐT | Lỗi tại trường, không tạo hồ sơ |
| 3 | Gửi form 4 lần liên tiếp trong 10 phút | Lần 4 bị chặn (429) |
| 4 | Admin phân công nhân viên A | A thấy hồ sơ, B không thấy |
| 5 | Admin chuyển hồ sơ sang B | A mất quyền, B xem được lịch sử, kênh chat cũ bị đóng |
| 6 | Khách và nhân viên chat 2 tab | Tin xuất hiện < 2 giây, không reload |
| 7 | Ngắt mạng → nối lại → gửi tiếp | Không mất tin, không nhân đôi |
| 8 | Khách đặt lịch xem xe đã bán | Bị từ chối, thông báo rõ |
| 9 | Nhân viên xác nhận 2 lịch cùng giờ | Lần 2 bị từ chối do trùng giờ |
| 10 | Khách A truy cập URL hồ sơ khách B | Chặn 403 |
| 11 | Xe đang có trong hồ sơ → admin cố xóa | Bị chặn, chỉ ẩn được |
| 12 | Admin ẩn dòng xe | Xe thuộc dòng biến mất khỏi trang công khai |

### 9.2 Kịch bản bổ sung (v5 chưa có, thêm để phủ các quyết định D-xx)
| # | Kịch bản | Kết quả mong đợi |
|---|---|---|
| 13 | Đăng nhập sai 6 lần / phút | Lần thứ 6 trả 429 |
| 14 | Tài khoản bị khóa đăng nhập đúng mật khẩu | Bị đăng xuất, thông báo "Tài khoản đã bị khóa" |
| 15 | Gửi lại cùng `client_message_id` | Không tạo thêm bản ghi, không broadcast lại |
| 16 | Khách đổi giờ lịch `confirmed` | Về `pending`, `note` lưu lý do + giờ cũ |
| 17 | Truy cập URL chi tiết xe đã ẩn | 404 |
| 18 | Khách vãng lai không thể mở chat / lịch sử | Redirect login |
| 19 | Gửi form tư vấn cho xe `sold` | Bị từ chối, thông báo rõ |
| 20 | Mở lại hồ sơ đã đóng | Về `assigned`, `close_result`/`closed_at` bị xóa, chat dùng lại được |

### 9.3 Điều kiện bàn giao Back-end
- `composer install`, `npm install`, `php artisan migrate --seed` chạy sạch trên máy mới.
- Seeder: 1 admin, 2 nhân viên, 3 khách, 10+ xe Toyota (thuộc ≥ 3 dòng xe), 3+ dịch vụ.
- `.env.example` đủ `APP_KEY`, `DB_*`, `REVERB_*`, `BROADCAST_CONNECTION=reverb` (tên cũ `BROADCAST_DRIVER` không dùng từ Laravel 11; D-11); README ghi cách chạy Reverb và queue.
- Migration có đủ quan hệ, ràng buộc, index; Eloquent có relationship + eager loading; middleware kiểm bằng Postman/route test.

---

## 10. Quyết định thiết kế đã áp dụng

Bảng đối chiếu các điểm v5 mâu thuẫn hoặc còn thiếu và cách SRS 1.1 đã chốt. **Không còn điểm mở.**

| ID | v5 ghi gì | SRS chốt | Nơi áp dụng |
|---|---|---|---|
| D-01 | Rate limit form: mục 4 ghi **3 lần/phút/IP**, mục 7.1 + test #3 ghi **3 lần/10 phút/IP**; form đặt lịch không rõ có bị giới hạn | **3 lần / 10 phút / IP**, áp cho cả form tư vấn và 2 form đặt lịch (`ThrottleFormSubmission`) | FR-INQ-1, FR-APT-2, Middleware |
| D-02 | ERD `inquiries` chỉ ghi `…` | Thêm `code` (`YC-` + 8 ký tự), `car_id`, `close_result`, `closed_at`, `access_version` (+ cột từ form) | 3.2 |
| D-03 | Có "ghi chú nội bộ" nhưng không có nơi lưu; tiêu đề "8 bảng" đếm lệch (thực tế 9) | Thêm bảng `inquiry_notes` → tổng 10 bảng gồm `activity_logs` | 3.1, 3.2, FR-INQ-3 |
| D-04 | ERD `client_msg_id` ≠ mục 7 `client_message_id`; "unique per sender" bằng validation sẽ trả 422 khi client retry | Cột `client_message_id`; unique DB `(sender_id, client_message_id)`; gửi lặp → trả bản ghi cũ (200), không broadcast lại | 3.2, FR-CHAT-6/7 |
| D-05 | Ai gán `assigned_to` của lịch hẹn không rõ; ±60 phút `<` hay `≤`; đổi lịch có xóa `confirmed_at`? | Admin phân công (mặc định kế thừa `inquiry.assigned_to`); trùng khi chênh **< 60 phút**; đổi lịch → `confirmed_at = null` | FR-APT-3/4 |
| D-06 | Thiếu: điều kiện `assigned → in_progress`; mở lại về đâu; khi nào được chat | `in_progress` khi nhân viên gửi tin đầu hoặc bấm "Bắt đầu xử lý"; mở lại → `assigned`; chat chỉ khi `assigned`/`in_progress` (`new`, `closed` chỉ đọc) | 4.4, FR-INQ-5, FR-CHAT-8 |
| D-07 | Xe `sold`: 5.1 chỉ cấm đặt lịch, 5.2 chỉ hiện "Đã bán"; rule form tư vấn không chặn xe bán | Xe `sold` **không** tư vấn, **không** đặt lịch (UI + server). `reserved`: tư vấn được, đặt lịch xem không | FR-CAT-5, FR-INQ-1, BR-3 |
| D-08 | `category` "int/slug"; thiếu `sort` mặc định; `price_min > price_max`; xe không có slug để làm URL | `category` = slug; `sort` mặc định `newest`; min > max → lỗi validate; chi tiết xe `/xe/{id}` | FR-CAT-1, Route |
| D-09 | CRUD xe thiếu `status`, `is_visible`, `specs`, quy tắc ảnh; **không có validate** cho dòng xe và dịch vụ | Bổ sung đủ (xem FR-ADM-1/2); tối đa 10 ảnh/xe (tổng); 1 ảnh chính; dòng xe/dịch vụ có dữ liệu liên quan chỉ ẩn | FR-ADM-1/2 |
| D-10 | Khóa tài khoản chỉ nhắc nhân viên; nhân viên bị khóa đang giữ hồ sơ thì sao? | Khóa được mọi tài khoản; hồ sơ giữ nguyên `assigned_to` đến khi Admin chuyển giao; `access_version++` | FR-ADM-3 |
| D-11 | `.env.example` ghi `BROADCAST_DRIVER` (tên cũ); không ghi phiên bản; giới hạn upload PHP mặc định không đủ | PHP 8.2+, Laravel 11+; dùng `BROADCAST_CONNECTION=reverb`; README ghi `upload_max_filesize ≥ 5M`, `post_max_size ≥ 50M` | Header, 7, 9.3 |
| D-12 | `services.type` là enum nhưng không có giá trị | `maintenance`, `warranty`, `other` | 3.2 |
| D-13 | Eloquent thiếu `Inquiry→Car`, `Car→Inquiry/Appointment`, `Service→Appointment` | Đã bổ sung (cùng `InquiryNote`) | 3.3 |
| D-14 | Không nói quên mật khẩu, mật khẩu ban đầu của nhân viên, giờ làm việc | Ngoài phạm vi v5; Admin đặt mật khẩu ban đầu; `requested_at` chỉ cần sau hiện tại và trong 6 tháng | FR-AUTH-4, FR-ADM-3 |
| D-15 | `EnsureAssignedStaff` có ở danh sách middleware nhưng không có trong cấu trúc thư mục | Giữ lại, giao cho Hoàng | Middleware |
| D-16 | Regex SĐT giới hạn 10 số nhưng rule đăng ký còn `max:11`; không nhận `+84` | Giữ nguyên, ghi nhận (không ảnh hưởng chạy) | BR-1 |
| D-17 | Chưa chốt sản phẩm (chỉ ghi "showroom xe", "showroom cao cấp") | Sản phẩm là **xe của hãng Toyota**; bỏ cột `car_categories.brand`; dòng xe = dòng Toyota; seeder dùng xe Toyota | 1.2, 3.2, FR-ADM-2, BR-11 |

**Việc đồng bộ ngược cho tài liệu luồng nghiệp vụ v5 (.docx):** sửa mục 4.2 (3 lần/phút → 3 lần/10 phút), ERD `inquiries`, thêm `inquiry_notes` và "10 bảng", thêm giá trị `services.type`, đổi `BROADCAST_DRIVER` → `BROADCAST_CONNECTION`, thêm `InquiryNote` và các quan hệ còn thiếu vào mục 3.

---

## 11. Phân công Back-end & phần mở rộng

### 11.1 Module theo người
| Người | Module Back-end |
|---|---|
| **Hoàng** (Repo Manager + BE Lead) | Setup Laravel, migration `users`, Auth, Middleware, `ThrottleFormSubmission`, `activity_logs`, `Admin/UserController`, `LogController`, seeder tổng, review PR, README |
| **TV3** (Xe & Catalog) | `car_categories`, `cars`, `car_images`: CRUD, upload ảnh, tìm kiếm/lọc/phân trang, `CarSeeder`, API `/api/xe/{id}/status`, Policy xe |
| **TV4** (Tư vấn & Chat) | `inquiries`, `messages`, `inquiry_notes`, phân công, trạng thái, Reverb, `MessageSent`, phân quyền kênh, chống trùng tin, `EnsureOwnInquiry` |
| **TV5** (Lịch hẹn & Dịch vụ) | `services`, `appointments`, `ServiceSeeder`, xác nhận không trùng giờ (transaction + lock), đổi/hủy/hoàn tất |
| **Đạt** (Frontend) | Blade/CSS/AJAX; dùng hợp đồng route/JSON ở mục 5 |

### 11.2 Quy tắc Git
- `main` luôn chạy được; mỗi tính năng một nhánh `feature/...`.
- Commit: `feat: …`, `fix: …`, `refactor: …`.
- Mỗi PR có mô tả đã làm gì + cách test; ≥ 1 người review mới merge.
- Không commit `.env`, `vendor/`, `node_modules/`, ảnh thật trong `storage/app`.

### 11.3 Mở rộng sau nghiệm thu (theo thứ tự ưu tiên)
1. Dự toán lăn bánh đơn giản (chọn tỉnh/thành, ước tính trước bạ từ seeder, ghi rõ "ước tính")
2. So sánh 2 xe (bảng thông số song song)
3. Yêu thích xe (AJAX thêm/xóa)
4. Xuất báo giá PDF (`barryvdh/laravel-dompdf`)

### 11.4 AJAX liên quan Back-end
| Tính năng | Kỹ thuật | Ưu tiên |
|---|---|---|
| Chat WebSocket (Reverb + Echo) | Broadcasting | 🔴 Bắt buộc |
| Lọc xe không reload | `fetch()` + `history.pushState()` | 🟡 Nên có |
| Kiểm tra xe `available` khi đặt lịch | `GET /api/xe/{id}/status` | 🟡 Nên có |
| Badge tin chưa đọc | Echo event (cần cột/bảng "đã đọc" — chưa có trong ERD) | 🟢 Bonus |
| Xem trước ảnh khi upload | FileReader API (chỉ Frontend) | 🟢 Bonus |
