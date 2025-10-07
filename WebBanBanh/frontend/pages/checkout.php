<?php
$pageTitle = 'Thanh toán';
require_once '../components/header.php';
require_once '../../backend/config/database.php';
require_once '../../backend/src/helpers/Session.php';

$cart = Session::getCart();
if (empty($cart)) {
    header('Location: cart.php');
    exit();
}
$total = 0;
$products = [];
if (!empty($cart)) {
    $productIds = array_keys($cart);
    $idsStr = implode(',', array_map('intval', $productIds));
    $database = new Database();
    $db = $database->getConnection();
    $query = "SELECT * FROM products WHERE id IN ($idsStr)";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll();
    foreach ($products as $p) {
        $price = $p['sale_price'] > 0 ? $p['sale_price'] : $p['price'];
        $total += $cart[$p['id']] * $price;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $method = trim($_POST['payment_method']);

    if ($name && $phone && $address && $total > 0) {
        $database = new Database();
        $db = $database->getConnection();
        $orderNumber = 'ORD' . time();

        // Thêm đơn hàng
        $sql = "INSERT INTO orders 
            (user_id, order_number, customer_name, customer_email, customer_phone, customer_address, total_amount, payment_method, status) 
            VALUES 
            (NULL, :order_number, :customer_name, :customer_email, :customer_phone, :customer_address, :total_amount, :payment_method, 'pending')";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':order_number' => $orderNumber,
            ':customer_name' => $name,
            ':customer_email' => $email,
            ':customer_phone' => $phone,
            ':customer_address' => $address,
            ':total_amount' => $total,
            ':payment_method' => $method,
        ]);
        $orderId = $db->lastInsertId();

        // Thêm sản phẩm vào đơn hàng
        foreach ($products as $p) {
            $price = $p['sale_price'] > 0 ? $p['sale_price'] : $p['price'];
            $qty = $cart[$p['id']];
            $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal) VALUES (:order_id, :product_id, :product_name, :price, :quantity, :subtotal)");
            $stmt->execute([
                ':order_id' => $orderId,
                ':product_id' => $p['id'],
                ':product_name' => $p['name'],
                ':price' => $price,
                ':quantity' => $qty,
                ':subtotal' => $qty * $price
            ]);
        }

        // Xóa giỏ hàng
        Session::clearCart();

        header('Location: thankyou.php');
        exit();
    } else {
        $error = 'Vui lòng điền đầy đủ thông tin!';
    }
}
?>
<div class="container py-5">
    <h2 class="mb-4">Thanh toán</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>
    <form action="" method="post" class="row g-4 mb-5">
        <div class="col-md-6">
            <h5>Thông tin khách hàng</h5>
            <div class="mb-3">
                <label>Họ tên*</label>
                <input type="text" name="name" class="form-control" required value="<?= e($_POST['name'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label>Số điện thoại*</label>
                <input type="text" name="phone" class="form-control" required value="<?= e($_POST['phone'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label>Địa chỉ nhận hàng*</label>
                <textarea name="address" class="form-control" required><?= e($_POST['address'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label>Phương thức thanh toán</label>
                <select name="payment_method" class="form-select">
                    <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <h5>Đơn hàng của bạn</h5>
            <ul class="list-group mb-2">
                <?php foreach ($products as $p): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= e($p['name']) ?> <span>x <?= $cart[$p['id']] ?></span>
                    <span><?= formatCurrency(($p['sale_price'] > 0 ? $p['sale_price'] : $p['price']) * $cart[$p['id']]) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <div class="text-end">
                <strong>Tổng thanh toán: <span class="text-danger"><?= formatCurrency($total) ?></span></strong>
            </div>
        </div>
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-brown btn-lg">Đặt hàng</button>
        </div>
    </form>
</div>
<?php require_once '../components/footer.php'; ?>
