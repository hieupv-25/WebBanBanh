<?php
$pageTitle = 'Giới thiệu';
require_once '../components/header.php';
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="bg-light py-3">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= url('frontend/pages/index.php') ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Giới thiệu</li>
        </ol>
    </div>
</nav>

<!-- About Section -->
<div class="container py-5">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="mb-4">Về Nguyễn Sơn Bakery</h1>
            
            <div class="row mb-5">
                <div class="col-md-6 mb-4">
                    <img src="<?= url('frontend/assets/images/about-1.jpg') ?>" 
                         alt="Nguyễn Sơn Bakery" 
                         class="img-fluid rounded shadow"
                         onerror="this.src='<?= url('frontend/assets/images/no-image.png') ?>'">
                </div>
                <div class="col-md-6">
                    <h3 class="mb-3">Câu chuyện của chúng tôi</h3>
                    <p class="text-muted">
                        Có lẽ những người yêu thích bánh ngọt, đặc biệt là bánh được làm theo phong cách Pháp 
                        không xa lạ gì với thương hiệu Nguyễn Sơn Bakery. Xuất thân trong gia đình có nghề làm 
                        bánh mỳ truyền thống, Chef Nguyễn Sơn cũng có thời gian dài làm việc tại Công ty Bodega 
                        rồi Sofitel Metropole.
                    </p>
                    <p class="text-muted">
                        Mỗi chiếc bánh ở Nguyễn Sơn Bakery lại mang một vẻ riêng, từ hương vị đến cách trang trí. 
                        Bánh có vị ngọt không quá đậm, vị béo thì thanh nên không gây cảm giác ngán cho người 
                        thưởng thức.
                    </p>
                </div>
            </div>
            
            <div class="row mb-5">
                <div class="col-md-6 order-md-2 mb-4">
                    <img src="<?= url('frontend/assets/images/about-2.jpg') ?>" 
                         alt="Đội ngũ" 
                         class="img-fluid rounded shadow"
                         onerror="this.src='<?= url('frontend/assets/images/no-image.png') ?>'">
                </div>
                <div class="col-md-6 order-md-1">
                    <h3 class="mb-3">Tầm nhìn và Sứ mệnh</h3>
                    <p class="text-muted">
                        <strong>Tầm nhìn:</strong> Trở thành thương hiệu bánh Pháp hàng đầu tại Việt Nam, 
                        mang đến cho khách hàng những sản phẩm chất lượng cao với giá cả hợp lý.
                    </p>
                    <p class="text-muted">
                        <strong>Sứ mệnh:</strong> Không ngừng nghiên cứu và phát triển các loại bánh mới, 
                        kết hợp giữa truyền thống và hiện đại, giữa văn hóa Pháp và Việt Nam để tạo ra 
                        những sản phẩm độc đáo và hấp dẫn.
                    </p>
                </div>
            </div>
            
            <div class="row mb-5">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-brown mb-3">
                                <i class="fas fa-star fa-3x"></i>
                            </div>
                            <h5>Chất lượng hàng đầu</h5>
                            <p class="text-muted small">
                                Cam kết sử dụng nguyên liệu tươi ngon, an toàn, nguồn gốc rõ ràng
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-brown mb-3">
                                <i class="fas fa-heart fa-3x"></i>
                            </div>
                            <h5>Làm bằng tâm huyết</h5>
                            <p class="text-muted small">
                                Mỗi sản phẩm đều được làm thủ công bởi đội ngũ thợ bánh giàu kinh nghiệm
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <div class="text-brown mb-3">
                                <i class="fas fa-truck fa-3x"></i>
                            </div>
                            <h5>Giao hàng tận nơi</h5>
                            <p class="text-muted small">
                                Dịch vụ giao hàng nhanh chóng, đảm bảo sản phẩm luôn tươi ngon
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-light p-5 rounded text-center">
                <h3 class="mb-3">Ghé thăm cửa hàng của chúng tôi</h3>
                <p class="text-muted mb-4">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    Số 15, hẻm 76 ngõch 51, ngõ Linh Quang, phường Văn Miếu - Quốc Tử Giám, Hà Nội
                </p>
                <p class="text-muted mb-4">
                    <i class="fas fa-phone me-2"></i> Hotline: 02438222228 | 
                    <i class="fas fa-envelope ms-3 me-2"></i> info@nguyenson.vn
                </p>
                <a href="<?= url('frontend/pages/contact.php') ?>" class="btn btn-brown btn-lg">
                    <i class="fas fa-envelope me-2"></i>Liên hệ với chúng tôi
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../components/footer.php'; ?>
