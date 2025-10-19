<?php
$pageTitle = 'Quản lý sản phẩm';
require_once 'includes/header.php';
require_once '../../../backend/config/database.php';

$database = new Database();
$db = $database->getConnection();

// Xóa sản phẩm
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    Session::setFlash('success', 'Xóa sản phẩm thành công!');
    header('Location: products.php');
    exit();
}

// Lấy danh sách sản phẩm
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$where = "1=1";
$params = [];

if ($search) {
    $where .= " AND p.name LIKE :search";
    $params[':search'] = "%$search%";
}

if ($category) {
    $where .= " AND c.id = :category";
    $params[':category'] = $category;
}

$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE $where 
          ORDER BY p.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Lấy danh mục để filter
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý sản phẩm</h2>
    <a href="product-add.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm sản phẩm
    </a>
</div>

<!-- Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sản phẩm..." 
                       value="<?= e($search) ?>">
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Lọc</button>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="80">Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Giá KM</th>
                        <th>Tồn kho</th>
                        <th>Trạng thái</th>
                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <img src="<?= url('storage/uploads/products/' . $product['image']) ?>" 
                                 alt="" style="width:50px;height:50px;object-fit:cover;"
                                 onerror="this.src='<?= url('frontend/assets/images/no-image.png') ?>'">
                        </td>
                        <td><?= e($product['name']) ?></td>
                        <td><?= e($product['category_name']) ?></td>
                        <td><?= formatCurrency($product['price']) ?></td>
                        <td><?= $product['sale_price'] ? formatCurrency($product['sale_price']) : '-' ?></td>
                        <td><?= $product['stock'] ?></td>
                        <td>
                            <?php if ($product['status']): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td class="table-actions">
                            <a href="product-edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?delete=<?= $product['id'] ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Bạn có chắc muốn xóa?')">
                                <i class="fas fa-trash"></i>
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
