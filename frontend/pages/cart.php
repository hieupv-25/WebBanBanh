<?php
$pageTitle = 'Giỏ hàng';
require_once '../components/header.php';
require_once '../../backend/config/database.php';
require_once '../../backend/src/helpers/Session.php';

// Lấy giỏ hàng từ Session
$cart = Session::getCart();

// Lấy thông tin chi tiết sản phẩm trong giỏ
$productsInfo = [];
$total = 0;

if (!empty($cart)) {
    $productIds = array_keys($cart);
    $idsStr = implode(',', array_map('intval', $productIds));

    $database = new Database();
    $db = $database->getConnection();
    $query = "SELECT * FROM products WHERE id IN ($idsStr)";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $productsInfo = $stmt->fetchAll();

    foreach ($productsInfo as $p) {
        $price = $p['sale_price'] > 0 ? $p['sale_price'] : $p['price'];
        $total += $cart[$p['id']] * $price;
    }
}
?>

<div class="container py-5">
    <h2 class="mb-4">Giỏ hàng của bạn</h2>
    <?php if (empty($cart)): ?>
        <div class="alert alert-info">Giỏ hàng hiện đang trống.</div>
        <a href="products/list.php" class="btn btn-brown">Tiếp tục mua hàng</a>
    <?php else: ?>
        <form id="cartForm">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Sản phẩm</th>
                            <th scope="col">Đơn giá</th>
                            <th scope="col">Số lượng</th>
                            <th scope="col">Thành tiền</th>
                            <th scope="col">Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productsInfo as $product): 
                            $price = $product['sale_price'] > 0 ? $product['sale_price'] : $product['price'];
                            $subtotal = $cart[$product['id']] * $price;
                        ?>
                        <tr>
                            <td>
                                <img src="<?= url('storage/uploads/products/' . $product['image']) ?>" alt="" style="width:60px;height:60px;object-fit:cover;">
                                <a href="products/detail.php?slug=<?= $product['slug'] ?>" class="ms-2"><?= e($product['name']) ?></a>
                            </td>
                            <td><?= formatCurrency($price) ?></td>
                            <td style="max-width:100px;">
                                <input type="number" min="1" max="<?= $product['stock'] ?>"
                                       class="form-control cart-qty"
                                       value="<?= $cart[$product['id']] ?>"
                                       data-id="<?= $product['id'] ?>">
                            </td>
                            <td><?= formatCurrency($subtotal) ?></td>
                            <td>
                                <button type="button" class="btn btn-danger btn-remove" data-id="<?= $product['id'] ?>">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-end mb-3">
                <h4>Tổng: <span class="text-danger"><?= formatCurrency($total) ?></span></h4>
                <a href="checkout.php" class="btn btn-brown btn-lg">Thanh toán</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.cart-qty').forEach(function(input) {
    input.addEventListener('change', function() {
        var id = this.dataset.id;
        var qty = this.value;
        fetch('../../backend/src/controllers/CartController.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=update&product_id=${id}&quantity=${qty}`
        }).then(() => location.reload());
    });
});
document.querySelectorAll('.btn-remove').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id = this.dataset.id;
        fetch('../../backend/src/controllers/CartController.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `action=remove&product_id=${id}`
        }).then(() => location.reload());
    });
});
</script>
<?php require_once '../components/footer.php'; ?>
