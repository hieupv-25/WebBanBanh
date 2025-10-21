
<?php
class Order {
    private $db;

    public function __construct($db = null) {
        if ($db) {
            $this->db = $db;
        } else {
            require_once __DIR__ . '/../../config/database.php';
            $database = new Database();
            $this->db = $database->getConnection();
        }
    }

    // Hàm tạo đơn hàng mới
    public function createOrder($userId, $productId, $quantity, $address, $paymentMethod) {
        try {
            $stmt = $this->db->prepare("INSERT INTO orders (user_id, product_id, quantity, shipping_address, payment_method, status, created_at) VALUES (:uid, :pid, :qty, :addr, :pay, 'pending', NOW())");
            return $stmt->execute([
                ':uid'  => $userId,
                ':pid'  => $productId,
                ':qty'  => $quantity,
                ':addr' => $address,
                ':pay'  => $paymentMethod
            ]);
        } catch (Throwable $e) {
            error_log("Order creation failed: " . $e->getMessage());
            return false;
        }
    }
}
