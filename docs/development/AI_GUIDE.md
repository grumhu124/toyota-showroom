# Dùng AI trong nhóm

Đưa AI vào đúng checkout repo, không chỉ gửi riêng file Word.
AGENTS.md là quy tắc chung; CLAUDE.md và Copilot instructions trỏ về cùng quy tắc.
Công cụ không tự đọc các file đó thì dán yêu cầu dưới đây và yêu cầu nó đọc tệp.

```text
Đọc AGENTS.md, README.md, SPECIFICATION, ARCHITECTURE, DATA_MODEL,
CONTRACTS, FEATURE_STATUS và OWNERSHIP trong repo trước khi làm.
Tôi phụ trách [module]. Nhiệm vụ lần này là [một việc cụ thể].
Giữ phạm vi và kiến trúc đã chốt, không viết lại toàn dự án.
Sau khi sửa, cập nhật tài liệu liên quan, CHANGELOG, chạy inventory --write
và kiểm tra phù hợp. Báo tệp thêm/sửa/xóa, kiểm tra đã chạy và phần chưa làm.
Không nói chức năng hoàn tất chỉ vì có thư mục/model/config.
```

Chọn tác vụ nhỏ: ví dụ validate và tạo inquiry, hoặc màn hình danh sách xe.
Chủ module kiểm tra AI có thay hợp đồng chung/lockfile/quyền ngoài nhiệm vụ không.
Không gửi .env, token, dữ liệu khách thật hoặc mật khẩu cho AI.
Đổi phạm vi do con người chốt và ghi bằng spec/ADR, không do AI tự quyết.
