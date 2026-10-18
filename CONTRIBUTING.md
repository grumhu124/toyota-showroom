# Làm việc cùng nhóm qua Git

`main` giữ bản tích hợp chạy được. Commit đầu tiên là setup; sau đó làm qua nhánh
tính năng và PR. Mỗi PR có ít nhất một thành viên khác review; CI không thay review.
Quy tắc này đang được tài liệu/CI hỗ trợ, chưa tự coi branch protection đã bật.

```sh
git switch main
git pull --ff-only
git switch -c feature/ten-chuc-nang
# viết mã, kiểm thử, cập nhật tài liệu
php scripts/architecture-inventory.php --write
composer check
npm run build
git diff --stat
git status --short
git add <cac-tep-da-kiem-tra>
git commit -m "feat: mo ta thay doi"
git push -u origin feature/ten-chuc-nang
```

Không copy nguyên project của AI đè repo. Không force-push main, không reset
hoặc xóa CSDL của người khác để giải quyết conflict. Khi main đổi, cập nhật
nhánh và giải quyết conflict cùng chủ module; chạy lại check trước gộp.
Dùng feat/fix/refactor/docs/test/chore cho commit phù hợp.

## Thay đổi cơ sở dữ liệu theo code-first

1. Nêu bảng/cột bị đổi, module dùng chung và cần backfill gì trước khi code.
2. Tạo migration mới với timestamp riêng; không sửa migration đã chia sẻ/chạy chung.
3. Sửa model/request/policy/seed/test và DATA_MODEL/CONTRACTS cùng PR.
4. Chạy migrate trên DB test MySQL mới và DB test đã có dữ liệu từ bản trước;
   kiểm không mất lịch sử, unique/FK/index và khả năng phục hồi.
5. Với thay đổi có thể mất dữ liệu, backup/restore trên DB riêng và ghi giới hạn
   rollback. Có down() không tự bảo đảm phục hồi được dữ liệu đã xóa.
6. Sau merge, thành viên pull rồi migrate; không yêu cầu sửa phpMyAdmin bằng tay.

Không dùng migrate:fresh/rollback trên DB chung. Test dùng DB riêng, khóa/race cần
hai kết nối MySQL thật. Mỗi người tự tạo fixture để không đợi CRUD người khác.
Chi tiết chia file và review: docs/team/PARALLEL_WORK.md.

## Cập nhật tài liệu và inventory

Mỗi thay đổi: thêm entry ở docs/changes/CHANGELOG.md, chạy inventory --write.
Thay schema/route/quyền/kiến trúc: sửa tài liệu tương ứng và thêm ADR nếu đổi thiết kế.
FEATURE_STATUS chỉ nâng mức khi có mã và kiểm tra phù hợp.
Khi conflict INVENTORY.json, giải quyết mã trước rồi chạy lại script; không chọn
ngẫu nhiên ours/theirs. Không sửa hash thủ công để qua CI.
Format 2 băm text sau chuẩn hóa CRLF thành LF để khớp Git Windows/Linux,
binary giữ nguyên byte. Nội dung đổi vẫn bị phát hiện; không format lại nguồn Word.

## PR phải ghi

Module, người phụ trách, tệp thêm/sửa/xóa, lý do, tác động dữ liệu/quyền,
migration cần chạy, cách kiểm thử, bằng chứng và hạn chế. Dùng template có sẵn.
Nếu dùng AI, thành viên vẫn đọc và giải thích được mã trước khi review.
Tài liệu hướng dẫn AI giúp thống nhất nhưng không bảo đảm một AI bỏ qua hướng dẫn
sẽ bị ngăn hoàn toàn; diff, inventory, test, review là các lớp kiểm tra thực tế.
