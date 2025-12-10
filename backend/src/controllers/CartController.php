<?php
ob_start();

// ✅ ĐƯỜNG DẪN ĐÚNG: Lùi 2 cấp từ controllers/
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../helpers/Session.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        addToCart();
        break;
    case 'update':
        updateCart();
        break;
    case 'remove':
        removeFromCart();
        break;
    case 'get_count':
        getCartCount();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

ob_end_flush();

function addToCart() {
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($productId <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        return;
    }

    $database = new Database();
    $db = $database->getConnection();
    $query = "SELECT * FROM products WHERE id = :id AND status = 1";
    $stmt = $db->prepare($query);
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
        return;
    }

    if ($product['stock'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không đủ số lượng']);
        return;
    }

    Session::addToCart($productId, $quantity);
    echo json_encode([
        'success' => true,
        'message' => 'Đã thêm vào giỏ hàng',
        'cart_count' => Session::getCartCount()
    ]);
}

function updateCart() {
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);

    if ($productId <= 0 || $quantity < 0) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        return;
    }

    $database = new Database();
    $db = $database->getConnection();
    $query = "SELECT stock FROM products WHERE id = :id AND status = 1";
    $stmt = $db->prepare($query);
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
        return;
    }

    if ($quantity > $product['stock']) {
        echo json_encode(['success' => false, 'message' => 'Không đủ tồn kho (chỉ còn ' . $product['stock'] . ')']);
        return;
    }

    Session::updateCart($productId, $quantity);
    echo json_encode([
        'success' => true,
        'message' => 'Đã cập nhật giỏ hàng',
        'cart_count' => Session::getCartCount()
    ]);
}

function removeFromCart() {
    $productId = (int)($_POST['product_id'] ?? 0);

    if ($productId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        return;
    }

    Session::removeFromCart($productId);
    echo json_encode([
        'success' => true,
        'message' => 'Đã xóa khỏi giỏ hàng',
        'cart_count' => Session::getCartCount()
    ]);
}

function getCartCount() {
    echo json_encode([
        'success' => true,
        'count' => Session::getCartCount()
    ]);
}
?>
