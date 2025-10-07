<?php
$pageTitle = 'Sản phẩm';
require_once '../../components/header.php';
require_once '../../../backend/config/database.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = PRODUCTS_PER_PAGE;
$offset = ($page - 1) * $limit;

// Filters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build query
$where = ["p.status = 1"];
$params = [];

if (!empty($category)) {
    $where[] = "c.slug = :category";
    $params[':category'] = $category;
}

if (!empty($search)) {
    $where[] = "(p.name LIKE :search OR p.description LIKE :search)";
    $params[':search'] = "%$search%";
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
          LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
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
?>

<!-- Breadcrumb -->
<section class="breadcrumb-section py-3 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('frontend/pages/index.php') ?>">Trang chủ</a></li>
                <li class="breadcrumb-item active">Sản phẩm</li>
                <?php if (!empty($category)): ?>
                    <li class="breadcrumb-item active"><?= e($categoryName) ?></li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
</section>

<!-- Products Section -->
<section class="products-section py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 mb-4">
                <div class="filters-sidebar">
                    <!-- Categories Filter -->
                    <div class="filter-group mb-4">
                        <h5 class="filter-title">Danh mục</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="<?= url('frontend/pages/products/list.php') ?>" 
                                   class="text-decoration-none <?= empty($category) ? 'fw-bold text-brown' : 'text-dark' ?>">
                                    Tất cả sản phẩm
                                </a>
                            </li>
                            <?php foreach ($categories as $cat): ?>
                                <li class="mb-2">
                                    <a href="<?= url('frontend/pages/products/list.php?category=' . $cat['slug']) ?>" 
                                       class="text-decoration-none <?= $category === $cat['slug'] ? 'fw-bold text-brown' : 'text-dark' ?>">
                                        <?= e($cat['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Price Range (Optional - can implement later) -->
                    <div class="filter-group mb-4">
                        <h5 class="filter-title">Khoảng giá</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="#" class="text-decoration-none text-dark">Dưới 20,000₫</a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-decoration-none text-dark">20,000₫ - 50,000₫</a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-decoration-none text-dark">50,000₫ - 100,000₫</a>
                            </li>
                            <li class="mb-2">
                                <a href="#" class="text-decoration-none text-dark">Trên 100,000₫</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Featured Products -->
                    <div class="filter-group">
                        <h5 class="filter-title">Sản phẩm nổi bật</h5>
                        <?php
                        $featuredQuery = "SELECT * FROM products WHERE is_featured = 1 AND status = 1 LIMIT 3";
                        $featuredStmt = $db->prepare($featuredQuery);
                        $featuredStmt->execute();
                        $featuredProducts = $featuredStmt->fetchAll();
                        ?>
                        <?php foreach ($featuredProducts as $featured): ?>
                            <div class="featured-product-item mb-3 d-flex">
                                <img src="<?= url('storage/uploads/products/' . $featured['image']) ?>" 
                                     alt="<?= e($featured['name']) ?>" 
                                     class="img-fluid rounded"
                                     style="width: 60px; height: 60px; object-fit: cover;"
                                     onerror="this.src='<?= url('frontend/assets/images/no-image.png') ?>'">
                                <div class="ms-2">
                                    <a href="<?= url('frontend/pages/products/detail.php?slug=' . $featured['slug']) ?>" 
                                       class="text-decoration-none text-dark">
                                        <small><?= e($featured['name']) ?></small>
                                    </a>
                                    <div class="text-brown fw-bold small">
                                        <?= formatCurrency($featured['price']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                <!-- Header with sorting -->
                <div class="products-header d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1"><?= e($categoryName) ?></h4>
                        <p class="text-muted small mb-0">Hiển thị <?= count($products) ?> trên <?= $totalProducts ?> sản phẩm</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <label class="me-2 small">Sắp xếp:</label>
                        <select class="form-select form-select-sm" id="sortSelect" style="width: auto;">
                            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Giá: Thấp đến cao</option>
                            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá: Cao đến thấp</option>
                            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Tên: A-Z</option>
                        </select>
                    </div>
                </div>

                <?php if (count($products) > 0): ?>
                    <!-- Products Grid -->
                    <div class="row g-4">
                        <?php foreach ($products as $product): ?>
                            <div class="col-lg-4 col-md-6">
                                <?php include '../../components/product-card.php'; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav class="mt-5">
                            <ul class="pagination justify-content-center">
                                <!-- Previous -->
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($category) ? '&category=' . $category : '' ?><?= !empty($search) ? '&search=' . $search : '' ?>&sort=<?= $sort ?>">
                                        Trước
                                    </a>
                                </li>

                                <!-- Page Numbers -->
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?><?= !empty($category) ? '&category=' . $category : '' ?><?= !empty($search) ? '&search=' . $search : '' ?>&sort=<?= $sort ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <!-- Next -->
                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($category) ? '&category=' . $category : '' ?><?= !empty($search) ? '&search=' . $search : '' ?>&sort=<?= $sort ?>">
                                        Sau
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- No products found -->
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-basket fa-4x text-muted mb-3"></i>
                        <h4>Không tìm thấy sản phẩm</h4>
                        <p class="text-muted">Vui lòng thử tìm kiếm với từ khóa khác hoặc xem tất cả sản phẩm.</p>
                        <a href="<?= url('frontend/pages/products/list.php') ?>" class="btn btn-brown">
                            Xem tất cả sản phẩm
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for sorting -->
<script>
document.getElementById('sortSelect').addEventListener('change', function() {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('sort', this.value);
    currentUrl.searchParams.set('page', '1'); // Reset to page 1 when sorting
    window.location.href = currentUrl.toString();
});
</script>

<?php require_once '../../components/footer.php'; ?>
