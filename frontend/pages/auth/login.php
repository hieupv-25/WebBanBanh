<?php
// =========================
//  Login Page (handle-first, no output before redirect)
// =========================

require_once '../../../backend/config/config.php';
require_once '../../../backend/config/database.php';
require_once '../../../backend/src/helpers/Session.php';

$pageTitle = 'Đăng nhập';

// Nếu đã đăng nhập → về trang chủ
if (Session::isLoggedIn()) {
    redirect('frontend/pages/index.php');
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$email  = '';

// Xử lý POST (KHÔNG render ở phần này)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $errors['general'] = 'Phiên làm việc không hợp lệ. Vui lòng thử lại.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $errors['general'] = 'Vui lòng nhập đầy đủ email và mật khẩu.';
        } else {
            try {
                $database = new Database();
                $db = $database->getConnection(); // nhớ bật ERRMODE_EXCEPTION trong Database.php

                // Chỉ đăng nhập user còn hoạt động
                $stmt = $db->prepare("SELECT id, name, email, password, role, status FROM users WHERE email = :email AND status = 1 LIMIT 1");
                $stmt->execute([':email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    // Đăng nhập thành công
                    Session::setUser($user);

                    // (Tuỳ chọn) Đồng bộ giỏ hàng từ session sang DB — an toàn, không chặn đăng nhập nếu lỗi
                    try {
                        $cart = Session::getCart();
                        if (!empty($cart)) {
                            foreach ($cart as $productId => $quantity) {
                                // Nếu đã có thì cập nhật, chưa có thì thêm
                                $check = $db->prepare("SELECT id FROM cart WHERE user_id = :uid AND product_id = :pid LIMIT 1");
                                $check->execute([':uid' => $user['id'], ':pid' => $productId]);
                                if ($check->fetch()) {
                                    $upd = $db->prepare("UPDATE cart SET quantity = quantity + :qty WHERE user_id = :uid AND product_id = :pid");
                                    $upd->execute([':qty' => (int)$quantity, ':uid' => $user['id'], ':pid' => $productId]);
                                } else {
                                    $ins = $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:uid, :pid, :qty)");
                                    $ins->execute([':uid' => $user['id'], ':pid' => $productId, ':qty' => (int)$quantity]);
                                }
                            }
                            // Sau khi sync có thể xoá giỏ tạm nếu muốn:
                            // Session::clearCart();
                        }
                    } catch (Throwable $e) {
                        // Bỏ qua lỗi giỏ hàng để không ảnh hưởng đăng nhập
                    }

                    // Reset CSRF + flash + redirect sớm
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    Session::setFlash('success', 'Đăng nhập thành công!');
                    redirect('frontend/pages/index.php');
                } else {
                    $errors['general'] = 'Email hoặc mật khẩu không đúng, hoặc tài khoản đang bị khoá.';
                }
            } catch (Throwable $e) {
                $errors['general'] = 'Lỗi hệ thống: ' . e($e->getMessage());
            }
        }
    }
}

// Chỉ render khi KHÔNG redirect
require_once '../../components/header.php';
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4">Đăng nhập</h2>

                    <?php if (!empty($errors['general'])): ?>
                        <div class="alert alert-danger"><?= e($errors['general']) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="email"
                                class="form-control"
                                id="email" name="email"
                                value="<?= e($email) ?>" required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input
                                type="password"
                                class="form-control"
                                id="password" name="password" required
                            >
                        </div>

                        <button type="submit" class="btn btn-brown w-100">Đăng nhập</button>

                        <div class="text-center mt-3">
                            <p class="mb-0">Chưa có tài khoản?
                                <a href="<?= url('frontend/pages/auth/register.php') ?>">Đăng ký ngay</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../../components/footer.php'; ?>
