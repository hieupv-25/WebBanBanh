# WebBanBanh
Quy tắc đặt tên nhánh (đề xuất)

Cấu trúc:
<type>/<scope>/<mô-tả-ngắn>[-<issue|id|tên-người>]

type (nhóm công việc):

feat (tính năng mới)

fix (sửa bug)

refactor (tái cấu trúc, không đổi hành vi)

chore (việc vặt: config, build, deps)

docs (tài liệu)

hotfix (sửa gấp trên production)

scope: khu vực chính của code, ví dụ: header, footer, products, auth, orders, config, assets.

mô-tả-ngắn: kebab-case, 2–5 từ, nêu mục tiêu.

định danh (tùy chọn): issue ID GitHub/Jira, hoặc tên viết tắt (hieu).

Ví dụ (liên quan header)

feat/header/add-sticky-top-cart-badge-hieu

fix/header/include-path-config-123

refactor/header/split-to-partials

chore/header/replace-cdn-bootstrap-5-3

Quy tắc commit (Conventional Commits – gọn)

Dạng:
<type>(<scope>): <mô tả ở thì hiện tại> (#issue)

Ví dụ:

fix(header): sửa đường dẫn require config (#123)

feat(header): thêm dropdown user + cart count

refactor(header): tách search-form thành partial

Quy trình nhanh (GitHub Flow)

Tạo nhánh từ main:

git checkout -b fix/header/include-path-hieu


Code + commit:

git add -A
git commit -m "fix(header): sửa require sang __DIR__"


Push & mở PR:

git push -u origin fix/header/include-path-hieu


Yêu cầu review ≥1 người, Squash & merge vào main.

Quy ước thêm (ngắn gọn)

Dùng kebab-case (chữ thường, - nối từ).

Mỗi nhánh = 1 mục tiêu rõ (ví dụ: “thêm sticky header”), tránh gom quá nhiều.

Nếu có issue: gắn số #id vào cuối nhánh hoặc commit/PR title.

main bật branch protection: bắt PR + review, cấm push trực tiếp.