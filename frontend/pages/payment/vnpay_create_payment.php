<?php
session_start();

// Load file Database class
require_once __DIR__ . '/../../../backend/config/database.php';

// Load VNPAY config
$vnpayConfig = require __DIR__ . '/../../../backend/config/vnpay_config.php';

// Kết nối database
$database = new Database();
$db = $database->getConnection();

$orderId = $_GET['order_id'] ?? null;
if (!$orderId) {
    $_SESSION['flash_error'] = 'Không tìm thấy đơn hàng';
    header('Location: http://localhost/WebBanBanh/frontend/pages/cart.php');
    exit();
}

$stmt = $db->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetch();

if (!$order) {
    $_SESSION['flash_error'] = 'Không tìm thấy đơn hàng';
    header('Location: http://localhost/WebBanBanh/frontend/pages/cart.php');
    exit();
}

$vnp_TmnCode = $vnpayConfig['vnp_TmnCode'];
$vnp_HashSecret = $vnpayConfig['vnp_HashSecret'];
$vnp_Url = $vnpayConfig['vnp_Url'];
$vnp_ReturnUrl = $vnpayConfig['vnp_ReturnUrl'];
$vnp_Amount = strval((int)$order['total_amount'] * 100);
$vnp_TxnRef = $order['order_number'];
$vnp_OrderInfo = 'Thanh toan don hang ' . $order['order_number'];
$vnp_OrderType = 'billpayment';

// Fix IPv6 → IPv4
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
if ($vnp_IpAddr == '::1') {
    $vnp_IpAddr = '127.0.0.1';
}

$inputData = [
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $vnp_Amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => date('YmdHis'),
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => "vn",
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_OrderType" => $vnp_OrderType,
    "vnp_ReturnUrl" => $vnp_ReturnUrl,
    "vnp_TxnRef" => $vnp_TxnRef,
];

// Sắp xếp mảng theo key
ksort($inputData);

$query = "";
$i = 0;
$hashdata = "";

// Tạo chuỗi query và hash data chuẩn
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

$vnp_Url = $vnp_Url . "?" . $query;

// Tạo chữ ký bảo mật (Secure Hash)
if (isset($vnp_HashSecret)) {
    // 🔥 QUAN TRỌNG: Dùng sha512 thay vì sha256
    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
}

header('Location: ' . $vnp_Url);
exit();
?>