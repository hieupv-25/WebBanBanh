<?php
// =========================
//  Xử lý đặt hàng (order-submit.php)
// =========================

// 1) Nạp các file cấu hình và helper cần thiết
require_once '../../backend/config/config.php';
require_once '../../backend/config/database.php';
require_once '../../backend/src/helpers/Session.php';
require_once '../../backend/src/helpers/Validator.php';
require_once '../../backend/src/models/Order.php';

// 2) Kiểm tra đăng nhập
if (!Session::isLoggedIn()) {
    redirect('frontend/pages/auth/login.php');
}

// 3) Lấy thông tin người dùng từ session
$user = Session::user();
$userId = $user['id'] ?? null;
if (!$userId) {
    Session::clearUser();
    redirect('frontend/pages/auth/login.php');
}

// 4) Xử lý khi form được gửi bằng phương thức POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    // 4.1) Kiểm tra CSRF token
    $csrf = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
        die('CSRF token không hợp lệ');
    }

    // 4.2) Lấy dữ liệu từ form
    $productId = $_POST['product_id'] ?? '';
    $quantity = (int)($_POST['quantity'] ?? 1);
    $address = trim($_POST['shipping_address'] ?? '');
    $payment = $_POST['payment_method'] ?? 'cod';

    // 4.3) Validate dữ liệu đầu vào
    $validator = new Validator($_POST);
    $validator->required('product_id', 'Vui lòng chọn sản phẩm')
              ->required('shipping_address', 'Vui lòng nhập địa chỉ')
              ->required('payment_method', 'Vui lòng chọn phương thức thanh toán');

    if ($validator->fails()) {
        die('Dữ liệu không hợp lệ');
    }

    // 4.4) Kết nối database
    $database = new Database();
    $db = $database->getConnection();

    // 4.5) Tạo đơn hàng mới
    $orderModel = new Order($db);
    $orderModel->createOrder($userId, $productId, $quantity, $address, $payment);

    // 4.6) Reset CSRF token để tránh resubmit
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // 4.7) Đặt flash message và chuyển hướng về trang tài khoản
    Session::setFlash('success', 'Đặt hàng thành công!');
    redirect('frontend/pages/account.php');
}
?>