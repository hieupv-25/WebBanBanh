<?php
require_once '../../../backend/config/config.php';
require_once '../../../backend/config/database.php';

$database = new Database();
$db = $database->getConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = PRODUCTS_PER_PAGE;
$offset = ($page - 1) * $limit;

// Filters
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// ✅ SỬA: Dùng positional parameters (?) thay vì named parameters (:name)
$where = ["p.status = 1"];
$params = [];

if (!empty($category)) {
    $where[] = "c.slug = ?";
    $params[] = $category;
}

if (!empty($search)) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereClause = implode(' AND ', $where);

// Sorting
switch ($sort) {
    case 'price_asc':
        $orderBy = 'p.price ASC';
        break;
    case 'price_desc':
        $orderBy = 'p.price DESC';
        break;
    case 'name':
        $orderBy = 'p.name ASC';
        break;
    case 'newest':
    default:
        $orderBy = 'p.created_at DESC';
        break;
}

// Count total products
$countQuery = "SELECT COUNT(*) as total
               FROM products p
               LEFT JOIN categories c ON p.category_id = c.id
               WHERE $whereClause";

$countStmt = $db->prepare($countQuery);
$countStmt->execute($params);
$totalProducts = $countStmt->fetch()['total'];
$totalPages = ceil($totalProducts / $limit);

// Get products
$query = "SELECT p.*, c.name as category_name, c.slug as category_slug
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          WHERE $whereClause
          ORDER BY $orderBy
          LIMIT ? OFFSET ?";

$stmt = $db->prepare($query);

// ✅ SỬA: Merge tất cả params vào 1 array
$allParams = array_merge($params, [$limit, $offset]);
$stmt->execute($allParams);

$products = $stmt->fetchAll();

// Get all categories for filter
$categoriesQuery = "SELECT * FROM categories WHERE status = 1 ORDER BY display_order ASC";
$categoriesStmt = $db->prepare($categoriesQuery);
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll();

// Get current category name
$categoryName = 'Tất cả sản phẩm';
if (!empty($category)) {
    foreach ($categories as $cat) {
        if ($cat['slug'] === $category) {
            $categoryName = $cat['name'];
            break;
        }
    }
}

$pageTitle = $categoryName;
require_once '../../components/header.php';
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="bg-light py-3">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('frontend/pages/index.php') ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= url('frontend/pages/products/list.php') ?>">Sản phẩm</a></li>
            <?php if (!empty($category)): ?>
                <li class="breadcrumb-item active"><?= e($categoryName) ?></li>
            <?php else: ?>
                <li class="breadcrumb-item active">Tất cả sản phẩm</li>
            <?php endif; ?>
        </ol>
    </div>
</nav>

<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card mb-3">
                <div class="card-header" style="background-color: #8B4513; color: white;">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Danh mục</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= url('frontend/pages/products/list.php') ?>" 
                       class="list-group-item list-group-item-action <?= empty($category) ? 'active' : '' ?>">
                        Tất cả sản phẩm
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?= url('frontend/pages/products/list.php?category=' . $cat['slug']) ?>" 
                           class="list-group-item list-group-item-action <?= $category === $cat['slug'] ? 'active' : '' ?>">
                            <?= e($cat['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Price Range Filter -->
            <div class="card mb-3">
                <div class="card-header" style="background-color: #8B4513; color: white;">
                    <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Khoảng giá</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= url('frontend/pages/products/list.php?price=0-20000') ?>" 
                       class="list-group-item list-group-item-action">Dưới 20,000₫</a>
                    <a href="<?= url('frontend/pages/products/list.php?price=20000-50000') ?>" 
                       class="list-group-item list-group-item-action">20,000₫ - 50,000₫</a>
                    <a href="<?= url('frontend/pages/products/list.php?price=50000-100000') ?>" 
                       class="list-group-item list-group-item-action">50,000₫ - 100,000₫</a>
                    <a href="<?= url('frontend/pages/products/list.php?price=100000-999999') ?>" 
                       class="list-group-item list-group-item-action">Trên 100,000₫</a>
                </div>
            </div>

            <!-- Featured Products -->
            <div class="card">
                <div class="card-header" style="background-color: #8B4513; color: white;">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Sản phẩm nổi bật</h5>
                </div>
                <div class="card-body">
                    <?php
                    $featuredQuery = "SELECT * FROM products WHERE is_featured = 1 AND status = 1 LIMIT 3";
                    $featuredStmt = $db->prepare($featuredQuery);
                    $featuredStmt->execute();
                    $featuredProducts = $featuredStmt->fetchAll();
                    
                    foreach ($featuredProducts as $featured):
                    ?>
                        <div class="d-flex mb-3 pb-3 border-bottom">
                            <?php if ($featured['image']): ?>
                                <img src="<?= url('storage/uploads/products/' . $featured['image']) ?>" 
                                     alt="<?= e($featured['name']) ?>" 
                                     class="me-2 rounded" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light me-2 rounded d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <a href="<?= url('frontend/pages/products/detail.php?id=' . $featured['id']) ?>" 
                                   class="text-decoration-none text-dark">
                                    <small><?= e($featured['name']) ?></small>
                                </a>
                                <br>
                                <strong style="color: #8B4513;"><?= formatCurrency($featured['price']) ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><?= e($categoryName) ?></h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-box me-1"></i>
                        Hiển thị <?= count($products) ?> trên <?= $totalProducts ?> sản phẩm
                    </p>
                </div>
                <div class="d-flex align-items-center">
                    <label class="me-2 mb-0"><i class="fas fa-sort me-1"></i>Sắp xếp:</label>
                    <select class="form-select" style="width: 200px;" onchange="window.location.href=this.value">
                        <?php
                        $sortOptions = [
                            'newest' => 'Mới nhất',
                            'price_asc' => 'Giá: Thấp đến cao',
                            'price_desc' => 'Giá: Cao đến thấp',
                            'name' => 'Tên: A-Z'
                        ];
                        
                        foreach ($sortOptions as $value => $label):
                            $urlParams = $_GET;
                            $urlParams['sort'] = $value;
                            $url = '?' . http_build_query($urlParams);
                        ?>
                            <option value="<?= $url ?>" <?= $sort === $value ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php if (!empty($products)): ?>
                <!-- Products -->
                <div class="row">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 mb-4">
                            <?php include '../../components/product-card.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <?php
                                    $prevParams = $_GET;
                                    $prevParams['page'] = $page - 1;
                                    ?>
                                    <a class="page-link" href="?<?= http_build_query($prevParams) ?>">
                                        <i class="fas fa-chevron-left"></i> Trước
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php
                            $start = max(1, $page - 2);
                            $end = min($totalPages, $page + 2);
                            
                            for ($i = $start; $i <= $end; $i++):
                                $pageParams = $_GET;
                                $pageParams['page'] = $i;
                            ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?<?= http_build_query($pageParams) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <?php
                                    $nextParams = $_GET;
                                    $nextParams['page'] = $page + 1;
                                    ?>
                                    <a class="page-link" href="?<?= http_build_query($nextParams) ?>">
                                        Sau <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <!-- No Products -->
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                    <h4>Không tìm thấy sản phẩm</h4>
                    <p class="mb-3">
                        <?php if (!empty($search)): ?>
                            Không có kết quả nào cho từ khóa "<strong><?= e($search) ?></strong>"
                        <?php else: ?>
                            Danh mục này chưa có sản phẩm nào.
                        <?php endif; ?>
                    </p>
                    <p class="text-muted">Vui lòng thử tìm kiếm với từ khóa khác hoặc xem tất cả sản phẩm.</p>
                    <a href="<?= url('frontend/pages/products/list.php') ?>" class="btn btn-primary">
                        <i class="fas fa-list me-2"></i>Xem tất cả sản phẩm
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../../components/footer.php'; ?>
