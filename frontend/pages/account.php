<?php
// =========================
//  Account Page (handle-first, no output before render)
// =========================

// 0) Bootstrapping (KHÔNG xuất HTML ở trên)
require_once '../../backend/config/config.php';
require_once '../../backend/config/database.php';
require_once '../../backend/src/helpers/Session.php';
require_once '../../backend/src/helpers/Validator.php';

$pageTitle = 'Tài khoản của tôi';

// 1) Yêu cầu đăng nhập
if (!Session::isLoggedIn()) {
    redirect('frontend/pages/auth/login.php?redirect=' . urlencode('frontend/pages/account.php'));
}

// 2) Lấy user từ session (không dùng getUserId() vì chưa định nghĩa)
$user = Session::user();
$userId = $user['id'] ?? null;
if (!$userId) {
    Session::clearUser();
    redirect('frontend/pages/auth/login.php');
}

// 3) CSRF token (tạo nếu chưa có)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$flashSuccess = null;

// 4) Kết nối DB
$database = new Database();
$db = $database->getConnection(); // nhớ đặt ERRMODE_EXCEPTION trong Database.php

// 5) Xử lý cập nhật thông tin (KHÔNG render ở phần này)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Kiểm tra CSRF
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $errors['general'] = 'Phiên làm việc không hợp lệ. Vui lòng thử lại.';
    } else {
        $name    = trim($_POST['name'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        // Validate cơ bản
        $validator = new Validator($_POST);
        $validator->required('name', 'Họ và tên là bắt buộc')
                  ->max('name', 255, 'Họ và tên quá dài')
                  ->max('phone', 20, 'Số điện thoại quá dài')
                  ->max('address', 2000, 'Địa chỉ quá dài');

        if ($validator->fails()) {
            $errors = $validator->errors();
        } else {
            try {
                $upd = $db->prepare(
                    "UPDATE users
                     SET name = :name, phone = :phone, address = :address, updated_at = NOW()
                     WHERE id = :id LIMIT 1"
                );
                $ok = $upd->execute([
                    ':name'    => $name,
                    ':phone'   => $phone !== '' ? $phone : null,
                    ':address' => $address !== '' ? $address : null,
                    ':id'      => $userId
                ]);

                if ($ok) {
                    // Cập nhật lại session user (để header “Xin chào” đổi ngay)
                    $user['name']    = $name;
                    $user['phone']   = $phone;
                    $user['address'] = $address;
                    Session::setUser($user);

                    // Reset CSRF (tránh resubmit)
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    Session::setFlash('success', 'Cập nhật thông tin thành công!');
                    redirect('frontend/pages/account.php');
                } else {
                    $errors['general'] = 'Không thể cập nhật thông tin. Vui lòng thử lại.';
                }
            } catch (Throwable $e) {
                $errors['general'] = 'Lỗi hệ thống: ' . e($e->getMessage());
            }
        }
    }
}

// 6) Lấy thông tin user từ DB (không lấy password)
try {
    $stmt = $db->prepare(
        "SELECT id, name, email, phone, address, avatar, role, status, created_at, updated_at
         FROM users WHERE id = :id LIMIT 1"
    );
    $stmt->execute([':id' => $userId]);
    $me = $stmt->fetch() ?: $user; // fallback sang session nếu cần
} catch (Throwable $e) {
    $me = $user;
    $errors['general'] = 'Không thể tải thông tin tài khoản.';
}

// 7) Lấy danh sách đơn hàng (nếu có bảng orders)
$orders = [];
try {
    $q = $db->prepare(
        "SELECT id, order_number, total_amount, status, created_at
         FROM orders
         WHERE user_id = :uid
         ORDER BY created_at DESC"
    );
    $q->execute([':uid' => $userId]);
    $orders = $q->fetchAll() ?: [];
} catch (Throwable $e) {
    // Nếu chưa có bảng orders, cứ cho rỗng
    $orders = [];
}

// 8) Chỉ bây giờ mới render giao diện
require_once '../components/header.php';
?>
<div class="container py-5">
    <h2 class="mb-4">Tài khoản của tôi</h2>

    <?php if ($msg = Session::getFlash('success')): ?>
        <div class="alert alert-success"><?= e($msg) ?></div>
    <?php endif; ?>
    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger"><?= e($errors['general']) ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Cột trái: Thông tin tài khoản -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Thông tin tài khoản</h5>
                    <form method="POST" action="" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                        <div class="mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text"
                                   class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>"
                                   name="name" value="<?= e($me['name'] ?? '') ?>" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?= e($errors['name']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= e($me['email'] ?? '') ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text"
                                   class="form-control<?= isset($errors['phone']) ? ' is-invalid' : '' ?>"
                                   name="phone" value="<?= e($me['phone'] ?? '') ?>">
                            <?php if (isset($errors['phone'])): ?>
                                <div class="invalid-feedback"><?= e($errors['phone']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea class="form-control<?= isset($errors['address']) ? ' is-invalid' : '' ?>"
                                      name="address" rows="3"><?= e($me['address'] ?? '') ?></textarea>
                            <?php if (isset($errors['address'])): ?>
                                <div class="invalid-feedback"><?= e($errors['address']) ?></div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-brown w-100">Cập nhật</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Cột phải: Lịch sử đơn hàng -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Lịch sử đơn hàng</h5>
                    <?php if (empty($orders)): ?>
                        <p class="text-muted">Chưa có đơn hàng nào.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Chi tiết</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?= e($order['order_number']) ?></td>
                                        <td><?= e(date('d/m/Y', strtotime($order['created_at']))) ?></td>
                                        <td><?= e(function_exists('formatCurrency')
                                            ? formatCurrency($order['total_amount'])
                                            : number_format((float)$order['total_amount'], 0, ',', '.') . 'đ') ?></td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'pending'    => 'warning',
                                                'confirmed'  => 'info',
                                                'processing' => 'primary',
                                                'shipping'   => 'secondary',
                                                'completed'  => 'success',
                                                'cancelled'  => 'danger'
                                            ];
                                            $statusText = [
                                                'pending'    => 'Chờ xác nhận',
                                                'confirmed'  => 'Đã xác nhận',
                                                'processing' => 'Đang xử lý',
                                                'shipping'   => 'Đang giao',
                                                'completed'  => 'Hoàn thành',
                                                'cancelled'  => 'Đã hủy'
                                            ];
                                            $s = $order['status'];
                                            ?>
                                            <span class="badge bg-<?= e($statusClass[$s] ?? 'secondary') ?>">
                                                <?= e($statusText[$s] ?? ucfirst($s)) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="order-detail.php?id=<?= e($order['id']) ?>" class="btn btn-sm btn-outline-brown">
                                                Xem
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../components/footer.php'; ?>
