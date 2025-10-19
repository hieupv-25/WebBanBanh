<?php
require_once '../../../backend/config/config.php';
require_once '../../../backend/src/helpers/Session.php';

Session::clearUser();
Session::setFlash('success', 'Đăng xuất thành công!');
redirect('frontend/pages/index.php');
?>
