<?php
require_once '../App/Model/ProfileModel.php';

class ProfileController {
    private $profileModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /quanlyc2c/Public/auth/login");
            exit();
        }
        $this->profileModel = new ProfileModel();
    }

    // Hiển thị chi tiết
    public function show() {
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        $user = $this->profileModel->getProfile($userId, $role);
        
        require_once '../App/View/profile/show.php';
    }

    // Hiển thị form chỉnh sửa
    public function edit() {
        $userId = $_SESSION['user_id'];
        $user = $this->profileModel->getProfile($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $avatarName = $user['anhdaidien']; // Mặc định dùng ảnh cũ

            // Xử lý Upload ảnh
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['avatar']['tmp_name'];
                $fileName = $_FILES['avatar']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                // Các định dạng cho phép
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    // Đặt tên file mới: user_ID_timestamp.ext
                    $newFileName = 'user_' . $userId . '_' . time() . '.' . $fileExtension;
                    $uploadFileDir = '../Public/Uploads/Avatars/';
                    $dest_path = $uploadFileDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        // Xóa ảnh cũ nếu không phải ảnh mặc định (tùy chọn)
                        if ($avatarName && file_exists($uploadFileDir . $avatarName)) {
                            unlink($uploadFileDir . $avatarName);
                        }
                        $avatarName = $newFileName;
                    }
                }
            }

            $data = [
                'name'    => $_POST['fullname'],
                'phone'   => $_POST['sdt'],
                'address' => $_POST['diachi'],
                'avatar'  => $avatarName,
                'bio'     => $_POST['gioithieu']
            ];

            if ($this->profileModel->updateProfile($userId, $data)) {
                $_SESSION['success'] = "Cập nhật thành công!";
                header("Location: /quanlyc2c/Public/profile/show");
                exit();
            }
        }
        
        require_once '../App/View/profile/edit.php';
    }
}
