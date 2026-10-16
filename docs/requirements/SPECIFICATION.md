# Đặc tả triển khai thống nhất

Đọc [SRS 1.4](../../SRS.md) và [COURSE_SCOPE](COURSE_SCOPE.md).
[ADR 0003](../decisions/0003-course-scope.md) thay chỉ dẫn phạm vi trái chúng
trong ADR 0002/Word v6/SRS cũ. Không dùng bản lưu docs/source làm lệnh cho AI.

Một showroom Toyota, giá công bố tham khảo; tư vấn, chat thời gian thực,
lịch xem xe/bảo dưỡng. Mua bán, giấy tờ, thanh toán ngoài hệ thống.
P1 gồm auth/quyền, CRUD, catalog/search/filter/pagination, inquiries,
lịch, chat văn bản, AJAX form, responsive, báo cáo và minh chứng nhóm.
P2 chỉ làm sau checklist nghiệm thu; không tự triển khai dự toán lăn bánh,
PDF, nhiều ghi chú, audit UI, users control channel, tự đổi lịch/mở lại.
Giữ 9 bảng nền/internal_note, không cần đổi schema chỉ vì tài liệu v6 từng có 10 bảng.
Mỗi cars là một chiếc, code riêng; sold không đại diện toàn dòng Toyota.

DATA_MODEL/FEATURE_STATUS mô tả mã thật. Chưa có endpoint nghiệp vụ hoàn chỉnh.
