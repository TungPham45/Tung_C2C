<?php
class AuthModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    // 1. CHỨC NĂNG ĐĂNG NHẬP
    public function login($identifier, $password) {
        $sql = "SELECT * FROM taikhoan WHERE (tentaikhoan = :id OR email = :id) AND trangthai = 'Hoạt động'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $identifier);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            die("Lỗi: Không tìm thấy tài khoản này trong Database!"); 
        }

        if (password_verify($password, $user['matkhau'])) {
            return $user;
        } else {
            // Dòng này sẽ giúp bạn biết mã hash trong DB đang là gì
            die("Lỗi: Mật khẩu không khớp. Hash trong DB: " . $user['matkhau']);
        }
    }


    public function checkExists($username, $email) {
        $sql = "SELECT * FROM taikhoan WHERE tentaikhoan = :user OR email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user' => $username, ':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Trả về thông tin nếu đã tồn tại
    }   
    // 2. CHỨC NĂNG ĐĂNG KÝ (Sử dụng Transaction để đảm bảo dữ liệu toàn vẹn)
    public function register($accountData, $personalData, $role) {
        try {
            $this->db->beginTransaction();

            // Bước A: Chèn thông tin cá nhân vào bảng tương ứng để lấy mã tự động tăng
            if ($role === 'Quản lý') {
                $sqlProfile = "INSERT INTO quanly (tenquanly, gioitinh, ngaysinh, sdt, diachi) 
                               VALUES (:name, :gender, :dob, :phone, :address)";
            } else {
                $sqlProfile = "INSERT INTO nguoidung (tennguoidung, gioitinh, ngaysinh, sdt, diachi) 
                               VALUES (:name, :gender, :dob, :phone, :address)";
            }

            $stmtProfile = $this->db->prepare($sqlProfile);
            $stmtProfile->execute([
                ':name'    => $personalData['name'],
                ':gender'  => $personalData['gender'],
                ':dob'     => $personalData['dob'],
                ':phone'   => $personalData['phone'],
                ':address' => $personalData['address']
            ]);

            // Lấy ID vừa sinh ra
            $profileId = $this->db->lastInsertId();

            // Bước B: Chèn vào bảng tài khoản
            $sqlAcc = "INSERT INTO taikhoan (tentaikhoan, email, matkhau, loaitaikhoan, maquanly, manguoidung, trangthai, ngaytao) 
                       VALUES (:username, :email, :password, :role, :ma_ql, :ma_nd, 'Hoạt động', CURDATE())";
            
            $stmtAcc = $this->db->prepare($sqlAcc);
            
            // Hash mật khẩu trước khi lưu
            $hashedPassword = password_hash($accountData['password'], PASSWORD_DEFAULT);
            
            $stmtAcc->execute([
                ':username' => $accountData['username'],
                ':email'    => $accountData['email'],
                ':password' => $hashedPassword,
                ':role'     => $role,
                ':ma_ql'    => ($role === 'Quản lý') ? $profileId : null,
                ':ma_nd'    => ($role === 'Người dùng') ? $profileId : null
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    // 3. CHỨC NĂNG QUÊN MẬT KHẨU
    // Kiểm tra email tồn tại để gửi OTP
    public function findByEmail($email) {
        $sql = "SELECT * FROM taikhoan WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật mật khẩu mới sau khi xác thực OTP thành công
    public function updatePassword($email, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE taikhoan SET matkhau = :password WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':password' => $hashedPassword,
            ':email'    => $email
        ]);
    }
}