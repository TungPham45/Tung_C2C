<?php
require_once '../App/Model/AdminModel.php';

class AdminController {
    private $adminModel;

    public function __construct() {
        // 1. Kiểm tra quyền truy cập: Chỉ cho phép 'Quản lý'
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Quản lý') {
            header("Location: /quanlyc2c/Public/auth/login");
            exit();
        }
        $this->adminModel = new AdminModel();
    }

    /**
     * Trang chủ quản trị (Dashboard)
     */
    public function dashboard() {
        $active_page = 'user_management';
        $accounts = $this->adminModel->getAllAccounts(); // Lấy danh sách
        $functionTitle = "Hệ thống quản lý tài khoản";
        $contentView = '../App/View/admin/user_management.php';
        require_once '../App/View/admin/dashboard.php';
    }
    /**
     * AJAX: Lấy thông tin chi tiết tài khoản để hiển thị Modal
     */
    public function getDetail($id = null) {
        if (!$id) {
            echo json_encode(['error' => 'Thiếu ID người dùng']);
            return;
        }

        $user = $this->adminModel->getAccountById($id);
        if ($user) {
            echo json_encode($user);
        } else {
            echo json_encode(['error' => 'Không tìm thấy người dùng']);
        }
    }

    /**
     * AJAX: Cập nhật trạng thái (Phê duyệt, Khóa, Mở lại)
     */
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $status = $_POST['status'];

            // Gọi model cập nhật cột 'trangthai' trong bảng 'taikhoan'
            $result = $this->adminModel->updateStatus($id, $status);

            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật trạng thái vào cơ sở dữ liệu.']);
            }
        }
    }

    /**
     * AJAX: Xóa tài khoản vĩnh viễn
     */
    public function deleteAccount($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Xóa dữ liệu ở cả 2 bảng dựa trên id_nguoidung
            $result = $this->adminModel->deleteAccount($id);

            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa tài khoản. Vui lòng kiểm tra lại.']);
            }
        }
    }
}