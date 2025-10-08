<?php
$pageTitle = 'Giới thiệu';
require_once '../components/header.php';
?>

<!-- Breadcrumb -->
<section class="breadcrumb-section py-3 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= url('frontend/pages/index.php') ?>">Trang chủ</a></li>
                <li class="breadcrumb-item active">Giới thiệu</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Hero Section -->
<section class="py-5 bg-brown text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold mb-4">Nguyễn Sơn Bakery</h1>
                <p class="lead mb-4">Hương vị truyền thống - Chất lượng tuyệt hảo</p>
                <p>Với hơn 20 năm kinh nghiệm trong nghề bánh, chúng tôi tự hào mang đến những sản phẩm chất lượng nhất cho gia đình bạn.</p>
            </div>
            <div class="col-lg-6 text-center">
                <img src="<?= url('frontend/assets/images/about-hero.jpg') ?>" 
                     alt="Nguyễn Sơn Bakery" 
                     class="img-fluid rounded shadow hover-shadow"
                     style="max-height: 400px; object-fit: cover;"
                     onerror="this.src='<?= url('frontend/assets/images/no-image.png') ?>'">
            </div>
        </div>
    </div>
</section>

<!-- Story Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title mb-4">Câu Chuyện Của Chúng Tôi</h2>
                <p class="mb-4">
                    Nguyễn Sơn Bakery được thành lập từ năm 2003, bắt đầu từ một cửa hàng nhỏ trong con hẻm nhỏ tại Hà Nội. 
                    Với niềm đam mê nghề bánh và mong muốn mang đến những chiếc bánh ngon nhất, chúng tôi đã không ngừng nỗ lực 
                    cải thiện chất lượng và mở rộng sản phẩm.
                </p>
                <p class="mb-0">
                    Đến nay, Nguyễn Sơn Bakery đã trở thành địa chỉ quen thuộc của hàng ngàn khách hàng yêu thích bánh ngọt, 
                    bánh mì tươi và các loại bánh kem cao cấp. Mỗi sản phẩm của chúng tôi đều được làm bằng tình yêu và sự tỉ mỉ, 
                    kết hợp giữa công thức truyền thống và kỹ thuật hiện đại.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Giá Trị Cốt Lõi</h2>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100 hover-shadow">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-star fa-3x text-brown mb-3"></i>
                        <h4 class="card-title mb-3">Chất Lượng</h4>
                        <p class="card-text text-muted">
                            Chúng tôi chỉ sử dụng nguyên liệu tươi ngon nhất, không chất bảo quản, 
                            đảm bảo an toàn vệ sinh thực phẩm.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100 hover-shadow">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-heart fa-3x text-brown mb-3"></i>
                        <h4 class="card-title mb-3">Tận Tâm</h4>
                        <p class="card-text text-muted">
                            Mỗi chiếc bánh đều được chăm chút tỉ mỉ, thể hiện tình yêu nghề và 
                            sự tận tâm với khách hàng.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100 hover-shadow">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-leaf fa-3x text-brown mb-3"></i>
                        <h4 class="card-title mb-3">Thân Thiện</h4>
                        <p class="card-text text-muted">
                            Sản phẩm thân thiện với sức khỏe, phù hợp với mọi lứa tuổi và 
                            nhu cầu ăn uống lành mạnh.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Đội Ngũ Của Chúng Tôi</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 hover-shadow">
                    <div class="card-body text-center">
                        <img src="<?= url('frontend/assets/images/about-home.jpg') ?>" 
                             alt="Bếp trưởng" 
                             class="img-fluid rounded-circle mb-3"
                             style="width: 120px; height: 120px; object-fit: cover; border: 3px solid var(--brown-primary);"
                             onerror="this.src='<?= url('frontend/assets/images/no-avatar.png') ?>'">
                        <h5 class="card-title mb-1">Nguyễn Văn Sơn</h5>
                        <p class="text-muted small mb-2">Chủ cửa hàng & Bếp trưởng</p>
                        <p class="card-text small text-muted">
                            Với 20 năm kinh nghiệm trong nghề bánh, thầy Sơn là linh hồn của Nguyễn Sơn Bakery.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 hover-shadow">
                    <div class="card-body text-center">
                        <img src="<?= url('frontend/assets/images/about-home.jpg') ?>" 
                             alt="Thợ làm bánh" 
                             class="img-fluid rounded-circle mb-3"
                             style="width: 120px; height: 120px; object-fit: cover; border: 3px solid var(--brown-primary);"
                             onerror="this.src='<?= url('frontend/assets/images/no-avatar.png') ?>'">
                        <h5 class="card-title mb-1">Trần Thị Hương</h5>
                        <p class="text-muted small mb-2">Thợ làm bánh chính</p>
                        <p class="card-text small text-muted">
                            Chuyên gia về bánh ngọt và bánh kem, với sự sáng tạo không ngừng.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 hover-shadow">
                    <div class="card-body text-center">
                        <img src="<?= url('frontend/assets/images/about-home.jpg') ?>" 
                             alt="Thợ làm bánh" 
                             class="img-fluid rounded-circle mb-3"
                             style="width: 120px; height: 120px; object-fit: cover; border: 3px solid var(--brown-primary);"
                             onerror="this.src='<?= url('frontend/assets/images/no-avatar.png') ?>'">
                        <h5 class="card-title mb-1">Lê Minh Anh</h5>
                        <p class="text-muted small mb-2">Thợ làm bánh mì</p>
                        <p class="card-text small text-muted">
                            Chuyên gia về các loại bánh mì với công thức độc quyền và hương vị đặc biệt.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5 bg-brown text-white">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="d-flex flex-column align-items-center">
                    <h3 class="display-4 fw-bold mb-2">20+</h3>
                    <p class="mb-0">Năm Kinh Nghiệm</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="d-flex flex-column align-items-center">
                    <h3 class="display-4 fw-bold mb-2">50+</h3>
                    <p class="mb-0">Loại Bánh</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="d-flex flex-column align-items-center">
                    <h3 class="display-4 fw-bold mb-2">10,000+</h3>
                    <p class="mb-0">Khách Hàng</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="d-flex flex-column align-items-center">
                    <h3 class="display-4 fw-bold mb-2">100+</h3>
                    <p class="mb-0">Sự Kiện</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="mb-4">Sẵn sàng thưởng thức những chiếc bánh tuyệt vời?</h2>
                <p class="lead mb-4">Hãy đến với Nguyễn Sơn Bakery để trải nghiệm hương vị đặc biệt của chúng tôi</p>
                <div class="d-flex justify-content-center flex-wrap gap-3">
                    <a href="<?= url('frontend/pages/products/list.php') ?>" class="btn btn-brown btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Mua ngay
                    </a>
                    <a href="<?= url('frontend/pages/contact.php') ?>" class="btn btn-outline-brown btn-lg">
                        <i class="fas fa-map-marker-alt me-2"></i>Liên hệ
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../components/footer.php'; ?>