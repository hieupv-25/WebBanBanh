<?php
$pageTitle = 'Trang chủ';
require_once '../components/header.php';
require_once '../../backend/config/database.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get featured products
$queryFeatured = "SELECT p.*, c.name as category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.is_featured = 1 AND p.status = 1 
                  ORDER BY p.created_at DESC LIMIT 8";
$stmtFeatured = $db->prepare($queryFeatured);
$stmtFeatured->execute();
$featuredProducts = $stmtFeatured->fetchAll();

// Get new products
$queryNew = "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.is_new = 1 AND p.status = 1 
             ORDER BY p.created_at DESC LIMIT 8";
$stmtNew = $db->prepare($queryNew);
$stmtNew->execute();
$newProducts = $stmtNew->fetchAll();

// Get categories
$queryCategories = "SELECT * FROM categories WHERE status = 1 ORDER BY display_order ASC";
$stmtCategories = $db->prepare($queryCategories);
$stmtCategories->execute();
$categories = $stmtCategories->fetchAll();
?>

<!-- Hero Slider -->
<section class="hero-slider">
    <div id="carouselHero" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselHero" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#carouselHero" data-bs-slide-to="1"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="<?= url('frontend/assets/images/slider/banner1.jpg') ?>" class="d-block w-100" alt="Banner 1">
                <div class="carousel-caption">
                    <h1 class="display-4 fw-bold">Trọn Vị Yêu Thương</h1>
                    <p class="lead">Đã Đầy Đoàn Viên</p>
                    <a href="<?= url('frontend/pages/products/list.php?category=banh-trung-thu') ?>" class="btn btn-brown btn-lg">
                        Xem ngay
                    </a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="<?= url('frontend/assets/images/slider/banner2.jpg') ?>" class="d-block w-100" alt="Banner 2">
                <div class="carousel-caption">
                    <h1 class="display-4 fw-bold">Bánh Ngọt Pháp</h1>
                    <p class="lead">Hương vị tinh tế, chất lượng tuyệt hảo</p>
                    <a href="<?= url('frontend/pages/products/list.php') ?>" class="btn btn-brown btn-lg">
                        Khám phá
                    </a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselHero" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselHero" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title">Danh mục sản phẩm</h2>
            <p class="text-muted">Khám phá các dòng sản phẩm của chúng tôi</p>
        </div>
        <div class="row g-4">
            <?php foreach($categories as $category): ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= url('frontend/pages/products/list.php?category=' . $category['slug']) ?>" class="category-card text-decoration-none">
                    <div class="card h-100 text-center border-0 shadow-sm hover-shadow">
                        <div class="card-body">
                            <div class="category-icon mb-3">
                                <i class="fas fa-birthday-cake fa-3x text-brown"></i>
                            </div>
                            <h5 class="card-title"><?= e($category['name']) ?></h5>
                            <p class="card-text small text-muted"><?= e($category['description']) ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="featured-products py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title">Sản phẩm bán chạy</h2>
            <p class="text-muted">Những sản phẩm được yêu thích nhất</p>
        </div>
        <div class="row g-4">
            <?php foreach($featuredProducts as $product): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <?php include '../components/product-card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?= url('frontend/pages/products/list.php') ?>" class="btn btn-outline-brown">
                Xem tất cả sản phẩm <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- New Products -->
<section class="new-products py-5 bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title">Sản phẩm mới</h2>
            <p class="text-muted">Những món bánh mới nhất từ Nguyễn Sơn Bakery</p>
        </div>
        <div class="row g-4">
            <?php foreach($newProducts as $product): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <?php include '../components/product-card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <img src="<?= url('frontend/assets/images/about-home.jpg') ?>" alt="About" class="img-fluid rounded shadow">
            </div>
            <div class="col-md-6">
                <h2 class="mb-4">Về chúng tôi</h2>
                <p class="text-muted">
                    Có lẽ những người yêu thích bánh ngọt, đặc biệt là bánh được làm theo phong 
                    cách Pháp không xa lạ gì với thương hiệu Nguyễn Sơn Bakery.
                </p>
                <p class="text-muted">
                    Mỗi chiếc bánh ở Nguyễn Sơn Bakery lại mang một vẻ riêng, từ hương vị đến cách trang trí.
                    Hình thức giản dị chỉ với hai màu đen trắng làm chủ đạo nhưng chất lượng nhờ cách làm tinh tế và tỉ mỉ. 
                    Bánh có vị ngọt không quá đậm, vị béo thì thanh nên không gây cảm giác ngán cho người thưởng thức.
                    Cũng rất hiếm khi tìm thấy sự trùng lặp trong các loại bánh ở Nguyễn Sơn Bakery vì tất cả chúng, từ bánh mì, bánh ngọt, bánh quy đều được làm 100% hand-made.
                    Hơn nữa, ông chủ của tiệm bánh, Chef Nguyễn Sơn, cũng là người khá khó tính trong việc lựa chọn nguyên liệu cho các sản phẩm của cửa hàng.
                </p>
                <p class="text-muted">
                    Xuất thân trong gia đình có nghề làm bánh mỳ truyền thống, Chef Nguyễn Sơn cũng có thời gian dài làm việc tại Công ty Bodega rồi Sofitel Metropole.
                    Anh có hơn 10 năm kinh nghiệm làm Chef bánh tại khách sạn danh tiếng Sofitel Metropole Legende Hanoi.
                </p>
                <p class="text-muted">
                    Và cũng chính ông chủ Nguyễn Sơn vẫn tự tay làm ra những chiếc bánh ngọt độc đáo. 
                    Bên cạnh việc kinh doanh, với ông chủ trẻ này thì “ làm bánh là một nghệ thuật đầy sáng tạo, được thể hiện cầu kỳ và nghiêm ngặt từ khâu chế biến cho đến việc trang trí, trình bày các họa tiết ".
                    Mỗi chiếc bánh được anh làm ra đều thỏa mãn hai ước mơ: nghệ thuật và kinh doanh.
                </p>
                <p class="text-muted">
                    Đến nay Nguyễn Sơn Bakery đã phát triển với một chuỗi cửa hàng tại Hà Nội, Hải Phòng, Bắc Ninh, Hưng Yên.
                    Mỗi nơi có một phong cách, một ấn tượng riêng nhưng tất cả đều hướng tới một điều là chất lượng và trang nhã.
                </p>
                <a href="<?= url('frontend/pages/about.php') ?>" class="btn btn-brown">
                    Tìm hiểu thêm
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once '../components/footer.php'; ?>
