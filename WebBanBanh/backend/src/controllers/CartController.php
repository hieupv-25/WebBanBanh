<?php
require_once dirname(dirname(__DIR__)) . '/config/config.php';
require_once dirname(dirname(__DIR__)) . '/src/helpers/Session.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

header('Content-Type: application/json');

// Get action
$action = $_POST['action'] ?? $_GET['action'] ?? '';

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
    case 'count':
        getCartCount();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function addToCart() {
    $productId = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    
    if ($productId <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        return;
    }
    
    // Check if product exists and has stock
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM products WHERE id = :id AND status = 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $productId);
    $stmt->execute();
    $product = $stmt->fetch();
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
        return;
    }
    
    if ($product['stock'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không đủ số lượng']);
        return;
    }
    
    // Add to session cart
    Session::addToCart($productId, $quantity);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Đã thêm vào giỏ hàng',
        'cart_count' => Session::getCartCount()
    ]);
}

function updateCart() {
    $productId = $_POST['product_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 0;
    
    if ($productId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
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
    $productId = $_POST['product_id'] ?? 0;
    
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
