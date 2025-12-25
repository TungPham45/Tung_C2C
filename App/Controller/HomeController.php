<?php
class HomeController {
    public function index() {
        // Kiểm tra nếu chưa đăng nhập thì đá về trang login
        if (!isset($_SESSION['user_id'])) {
            header("Location: /quanlyc2c/Public/auth/login");
            exit();
        }

        $username = $_SESSION['username'];
        $role = $_SESSION['role'];

        // Dựa vào role để gọi View tương ứng
        if ($role === 'Quản lý') {
            require_once '../App/View/admin/dashboard.php';
        } else {
            require_once '../App/View/user/dashboard.php';
        }
    }
}