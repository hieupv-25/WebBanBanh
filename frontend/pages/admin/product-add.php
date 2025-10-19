<?php
$pageTitle = 'Thêm sản phẩm';
require_once 'includes/header.php';
require_once '../../../backend/config/database.php';

$database = new Database();
$db = $database->getConnection();

// Lấy danh sách categories
$categories = $db->query("SELECT * FROM categories WHERE status = 1 ORDER BY name")->fetchAll();

$errors = [];
$oldData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $categoryId = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $salePrice = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $stock = (int)$_POST['stock'];
    $description = trim($_POST['description']);
    $ingredients = trim($_POST['ingredients']);
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $isNew = isset($_POST['is_new']) ? 1 : 0;
    $status = isset($_POST['status']) ? 1 : 0;
    
    $oldData = $_POST;
    
    // Validation
    if (empty($name)) $errors['name'] = 'Tên sản phẩm là bắt buộc';
    if ($categoryId <= 0) $errors['category_id'] = 'Vui lòng chọn danh mục';
    if ($price <= 0) $errors['price'] = 'Giá phải lớn hơn 0';
    
    // Upload image
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileType = $_FILES['image']['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            $errors['image'] = 'Chỉ chấp nhận file ảnh JPG, PNG';
        } elseif ($_FILES['image']['size'] > MAX_FILE_SIZE) {
            $errors['image'] = 'Kích thước file không được vượt quá 5MB';
        } else {
            $imageName = time() . '_' . $_FILES['image']['name'];
            $uploadPath = UPLOAD_PATH . 'products/' . $imageName;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $errors['image'] = 'Lỗi upload ảnh';
            }
        }
    }
    
    if (empty($errors)) {
        $slug = createSlug($name);
        
        $query = "INSERT INTO products 
                  (category_id, name, slug, description, ingredients, price, sale_price, image, stock, is_featured, is_new, status) 
                  VALUES 
                  (:category_id, :name, :slug, :description, :ingredients, :price, :sale_price, :image, :stock, :is_featured, :is_new, :status)";
        
        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            ':category_id' => $categoryId,
            ':name' => $name,
            ':slug' => $slug,
            ':description' => $description,
            ':ingredients' => $ingredients,
            ':price' => $price,
            ':sale_price' => $salePrice,
            ':image' => $imageName,
            ':stock' => $stock,
            ':is_featured' => $isFeatured,
            ':is_new' => $isNew,
            ':status' => $status
        ]);
        
        if ($result) {
            Session::setFlash('success', 'Thêm sản phẩm thành công!');
            header('Location: products.php');
            exit();
        } else {
            $errors['general'] = 'Có lỗi xảy ra, vui lòng thử lại';
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Thêm sản phẩm mới</h2>
    <a href="products.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger"><?= e($errors['general']) ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                               value="<?= e($oldData['name'] ?? '') ?>" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= e($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="4"><?= e($oldData['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Thành phần</label>
                        <textarea name="ingredients" class="form-control" rows="3"><?= e($oldData['ingredients'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" 
                                   value="<?= e($oldData['price'] ?? '') ?>" min="0" step="1000" required>
                            <?php if (isset($errors['price'])): ?>
                                <div class="invalid-feedback"><?= e($errors['price']) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giá khuyến mãi</label>
                            <input type="number" name="sale_price" class="form-control" 
                                   value="<?= e($oldData['sale_price'] ?? '') ?>" min="0" step="1000">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số lượng tồn kho <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control" 
                                   value="<?= e($oldData['stock'] ?? 0) ?>" min="0" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>" required>
                                <option value="">Chọn danh mục</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($oldData['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                        <?= e($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['category_id'])): ?>
                                <div class="invalid-feedback"><?= e($errors['category_id']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Ảnh sản phẩm</label>
                        <input type="file" name="image" class="form-control <?= isset($errors['image']) ? 'is-invalid' : '' ?>" 
                               accept="image/*" onchange="previewImage(this)">
                        <?php if (isset($errors['image'])): ?>
                            <div class="invalid-feedback"><?= e($errors['image']) ?></div>
                        <?php endif; ?>
                        <div class="mt-2">
                            <img id="preview" src="<?= url('frontend/assets/images/no-image.png') ?>" 
                                 alt="Preview" style="max-width: 100%; height: auto;">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label d-block">Tùy chọn</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" 
                                   <?= isset($oldData['is_featured']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_featured">Sản phẩm nổi bật</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_new" id="is_new"
                                   <?= isset($oldData['is_new']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_new">Sản phẩm mới</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="status" id="status" checked>
                            <label class="form-check-label" for="status">Hiển thị</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu sản phẩm
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
