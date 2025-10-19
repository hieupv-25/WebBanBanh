<?php
$pageTitle = 'Chi tiết khách hàng';
require_once 'includes/header.php';
require_once '../../../backend/config/database.php';

$database = new Database();
$db = $database->getConnection();

$customerId = (int)($_GET['id'] ?? 0);
if ($customerId <= 0) {
    Session::setFlash('error', 'Khách hàng không tồn tại');
    header('Location: customers.php');
    exit();
}

// Lấy thông tin khách hàng
$stmt = $db->prepare("SELECT * FROM users WHERE id = :id AND role = 'customer'");
$stmt->execute([':id' => $customerId]);
$customer = $stmt->fetch();

if (!$customer) {
    Session::setFlash('error', 'Khách hàng không tồn tại');
    header('Location: customers.php');
    exit();
}

// Cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = (int)$_POST['status'];
    $stmt = $db->prepare("UPDATE users SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $status, ':id' => $customerId]);
    
    Session::setFlash('success', 'Cập nhật trạng thái thành công!');
    header('Location: customer-detail.php?id=' . $customerId);
    exit();
}

// Lấy thống kê đơn hàng
$statsStmt = $db->prepare("
    SELECT 
        COUNT(*) as total_orders,
        SUM(total_amount) as total_spent,
        MAX(created_at) as last_order_date
    FROM orders 
    WHERE user_id = :user_id
");
$statsStmt->execute([':user_id' => $customerId]);
$stats = $statsStmt->fetch();

// Lấy danh sách đơn hàng
$ordersStmt = $db->prepare("
    SELECT * FROM orders 
    WHERE user_id = :user_id 
    ORDER BY created_at DESC 
    LIMIT 20
");
$ordersStmt->execute([':user_id' => $customerId]);
$orders = $ordersStmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Chi tiết khách hàng</h2>
    <a href="customers.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="row">
    <div class="col-md-4">
        <!-- Customer Info Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Thông tin khách hàng</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px; border-radius: 50%; font-size: 2rem;">
                        <?= strtoupper(substr($customer['name'], 0, 1)) ?>
                    </div>
                </div>
                
                <table class="table table-borderless table-sm">
                    <tr>
                        <th>Họ tên:</th>
                        <td><?= e($customer['name']) ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?= e($customer['email']) ?></td>
                    </tr>
                    <tr>
                        <th>Điện thoại:</th>
                        <td><?= e($customer['phone'] ?: '-') ?></td>
                    </tr>
                    <tr>
                        <th>Địa chỉ:</th>
                        <td><?= e($customer['address'] ?: '-') ?></td>
                    </tr>
                    <tr>
                        <th>Ngày đăng ký:</th>
                        <td><?= date('d/m/Y', strtotime($customer['created_at'])) ?></td>
                    </tr>
                </table>
                
                <hr>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Trạng thái tài khoản:</label>
                        <select name="status" class="form-select">
                            <option value="1" <?= $customer['status'] ? 'selected' : '' ?>>Hoạt động</option>
                            <option value="0" <?= !$customer['status'] ? 'selected' : '' ?>>Khóa</option>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Statistics Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Thống kê</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Tổng đơn hàng</small>
                    <h4 class="mb-0"><?= $stats['total_orders'] ?? 0 ?> đơn</h4>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Tổng chi tiêu</small>
                    <h4 class="mb-0 text-success"><?= formatCurrency($stats['total_spent'] ?? 0) ?></h4>
                </div>
                <?php if ($stats['last_order_date']): ?>
                <div>
                    <small class="text-muted">Đơn hàng gần nhất</small>
                    <h6 class="mb-0"><?= date('d/m/Y', strtotime($stats['last_order_date'])) ?></h6>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Orders History -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Lịch sử đơn hàng</h5>
            </div>
            <div class="card-body">
                <?php if (empty($orders)): ?>
                    <p class="text-muted text-center py-4">Khách hàng chưa có đơn hàng nào.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><strong><?= e($order['order_number']) ?></strong></td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                    <td><?= formatCurrency($order['total_amount']) ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'confirmed' => 'info',
                                            'processing' => 'primary',
                                            'shipping' => 'secondary',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $statusTexts = [
                                            'pending' => 'Chờ xác nhận',
                                            'confirmed' => 'Đã xác nhận',
                                            'processing' => 'Đang xử lý',
                                            'shipping' => 'Đang giao',
                                            'completed' => 'Hoàn thành',
                                            'cancelled' => 'Đã hủy'
                                        ];
                                        ?>
                                        <span class="badge bg-<?= $statusColors[$order['status']] ?>">
                                            <?= $statusTexts[$order['status']] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="order-detail.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
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

<?php require_once 'includes/footer.php'; ?>
