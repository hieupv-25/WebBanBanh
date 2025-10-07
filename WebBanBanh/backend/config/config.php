<?php
/**
 * General Configuration
 * Cấu hình chung của website
 */

// Bật hiển thị lỗi (tắt ở production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL
define('BASE_URL', 'http://localhost/WebBanBanh/');
define('ADMIN_URL', BASE_URL . 'frontend/pages/admin/');

// Path configurations
define('ROOT_PATH', dirname(dirname(__DIR__)) . '/');
define('UPLOAD_PATH', ROOT_PATH . 'storage/uploads/');
define('UPLOAD_URL', BASE_URL . 'storage/uploads/');

// Database configuration
require_once ROOT_PATH . 'backend/config/database.php';

// Số sản phẩm hiển thị mỗi trang
define('PRODUCTS_PER_PAGE', 12);
define('NEWS_PER_PAGE', 9);

// Upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Email configuration (có thể cấu hình sau)
define('ADMIN_EMAIL', 'admin@bakery.com');
define('SITE_NAME', 'Nguyễn Sơn Bakery');

// Pagination
define('PAGINATION_LIMIT', 10);

/**
 * Helper function để tạo URL
 */
function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}

/**
 * Helper function để redirect
 */
function redirect($path) {
    header('Location: ' . url($path));
    exit();
}

/**
 * Helper function format tiền tệ
 */
function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . '₫';
}

/**
 * Helper function để escape output
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Helper function tạo slug từ tiếng Việt
 */
function createSlug($str) {
    $str = trim(mb_strtolower($str));
    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
    $str = preg_replace('/(đ)/', 'd', $str);
    $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
    $str = preg_replace('/(\s+)/', '-', $str);
    return $str;
}

/**
 * Debug helper
 */
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}
?>