<?php
$pageTitle = 'Quản lý khách hàng';
require_once 'includes/header.php';
require_once '../../../backend/config/database.php';

$database = new Database();
$db = $database->getConnection();

// Search
$search = $_GET['search'] ?? '';
$where = "role = 'customer'";
$params = [];

if ($search) {
    $where .= " AND (name LIKE :search OR email LIKE :search OR phone LIKE :search)";
    $params[':search'] = "%$search%";
}

// Get customers with order stats
$query = "SELECT u.*, 
          COUNT(DISTINCT o.id) as total_orders,
          SUM(o.total_amount) as total_spent
          FROM users u
          LEFT JOIN orders o ON u.id = o.user_id
          WHERE $where
          GROUP BY u.id
          ORDER BY u.created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$customers = $stmt->fetchAll();

// Thống kê
$stats = [
    'total_customers' => $db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn(),
    'active_customers' => $db->query("SELECT COUNT(*) FROM users WHERE role = 'customer' AND status = 1")->fetchColumn(),
    'customers_with_orders' => $db->query("SELECT COUNT(DISTINCT user_id) FROM orders WHERE user_id IS NOT NULL")->fetchColumn()
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý khách hàng</h2>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Tổng khách hàng</h6>
                        <h3 class="mb-0"><?= $stats['total_customers'] ?></h3>
                    </div>
                    <div class="text-primary fs-1">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Đang hoạt động</h6>
                        <h3 class="mb-0"><?= $stats['active_customers'] ?></h3>
                    </div>
                    <div class="text-success fs-1">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Đã mua hàng</h6>
                        <h3 class="mb-0"><?= $stats['customers_with_orders'] ?></h3>
                    </div>
                    <div class="text-info fs-1">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" 
                       placeholder="Tìm theo tên, email, số điện thoại..." 
                       value="<?= e($search) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
            </div>
        </form>
    </div>
</div>

<!-- Customers Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Số đơn hàng</th>
                        <th>Tổng chi tiêu</th>
                        <th>Ngày đăng ký</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= $customer['id'] ?></td>
                        <td>
                            <strong><?= e($customer['name']) ?></strong>
                        </td>
                        <td><?= e($customer['email']) ?></td>
                        <td><?= e($customer['phone'] ?: '-') ?></td>
                        <td>
                            <?php if ($customer['total_orders'] > 0): ?>
                                <span class="badge bg-info"><?= $customer['total_orders'] ?> đơn</span>
                            <?php else: ?>
                                <span class="text-muted">Chưa mua</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($customer['total_spent']): ?>
                                <strong><?= formatCurrency($customer['total_spent']) ?></strong>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($customer['created_at'])) ?></td>
                        <td>
                            <?php if ($customer['status']): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Khóa</span>
                            <?php endif; ?>
                        </td>
                        <td class="table-actions">
                            <a href="customer-detail.php?id=<?= $customer['id'] ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
