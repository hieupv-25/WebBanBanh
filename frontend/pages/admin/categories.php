<?php
$pageTitle = 'Quản lý danh mục';
require_once 'includes/header.php';
require_once '../../../backend/config/database.php';

$database = new Database();
$db = $database->getConnection();

// Xóa danh mục
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Kiểm tra xem danh mục có sản phẩm không
    $checkStmt = $db->prepare("SELECT COUNT(*) FROM products WHERE category_id = :id");
    $checkStmt->execute([':id' => $id]);
    $productCount = $checkStmt->fetchColumn();
    
    if ($productCount > 0) {
        Session::setFlash('error', 'Không thể xóa danh mục có sản phẩm!');
    } else {
        $stmt = $db->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        Session::setFlash('success', 'Xóa danh mục thành công!');
    }
    
    header('Location: categories.php');
    exit();
}

// Thêm/Sửa danh mục
$errors = [];
$editMode = false;
$category = null;

if (isset($_GET['edit'])) {
    $editMode = true;
    $id = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM categories WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $category = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $displayOrder = (int)$_POST['display_order'];
    $status = isset($_POST['status']) ? 1 : 0;
    
    if (empty($name)) {
        $errors['name'] = 'Tên danh mục là bắt buộc';
    }
    
    if (empty($errors)) {
        $slug = createSlug($name);
        
        if (isset($_POST['category_id']) && $_POST['category_id']) {
            // Update
            $id = (int)$_POST['category_id'];
            $query = "UPDATE categories SET name = :name, slug = :slug, description = :description, 
                      display_order = :display_order, status = :status WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':name' => $name,
                ':slug' => $slug,
                ':description' => $description,
                ':display_order' => $displayOrder,
                ':status' => $status,
                ':id' => $id
            ]);
            Session::setFlash('success', 'Cập nhật danh mục thành công!');
        } else {
            // Insert
            $query = "INSERT INTO categories (name, slug, description, display_order, status) 
                      VALUES (:name, :slug, :description, :display_order, :status)";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':name' => $name,
                ':slug' => $slug,
                ':description' => $description,
                ':display_order' => $displayOrder,
                ':status' => $status
            ]);
            Session::setFlash('success', 'Thêm danh mục thành công!');
        }
        
        header('Location: categories.php');
        exit();
    }
}

// Lấy danh sách danh mục
$categories = $db->query("SELECT c.*, COUNT(p.id) as product_count 
                          FROM categories c 
                          LEFT JOIN products p ON c.id = p.category_id 
                          GROUP BY c.id 
                          ORDER BY c.display_order ASC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Quản lý danh mục</h2>
    <?php if (!$editMode): ?>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
            <i class="fas fa-plus"></i> Thêm danh mục
        </button>
    <?php endif; ?>
</div>

<!-- Categories Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="60">STT</th>
                        <th>Tên danh mục</th>
                        <th>Slug</th>
                        <th>Số sản phẩm</th>
                        <th>Thứ tự</th>
                        <th>Trạng thái</th>
                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['display_order'] ?></td>
                        <td><strong><?= e($cat['name']) ?></strong></td>
                        <td><code><?= e($cat['slug']) ?></code></td>
                        <td><?= $cat['product_count'] ?> sản phẩm</td>
                        <td><?= $cat['display_order'] ?></td>
                        <td>
                            <?php if ($cat['status']): ?>
                                <span class="badge bg-success">Hiển thị</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td class="table-actions">
                            <a href="?edit=<?= $cat['id'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ($cat['product_count'] == 0): ?>
                                <a href="?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Bạn có chắc muốn xóa?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php else: ?>
                                <button class="btn btn-sm btn-secondary" disabled title="Có sản phẩm, không thể xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $editMode ? 'Sửa danh mục' : 'Thêm danh mục mới' ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if ($editMode): ?>
                        <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                               value="<?= e($category['name'] ?? '') ?>" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= e($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3"><?= e($category['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Thứ tự hiển thị</label>
                        <input type="number" name="display_order" class="form-control" 
                               value="<?= e($category['display_order'] ?? 0) ?>" min="0">
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="status" id="status" 
                               <?= !isset($category) || $category['status'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status">Hiển thị</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?= $editMode ? 'Cập nhật' : 'Thêm mới' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($editMode): ?>
<script>
    // Auto show modal when in edit mode
    var myModal = new bootstrap.Modal(document.getElementById('categoryModal'));
    myModal.show();
</script>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
