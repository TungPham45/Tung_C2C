<?php
class AuthModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    // 1. CHỨC NĂNG ĐĂNG NHẬP
    public function login($identifier) {
        // Chỉ nhận 1 tham số để tìm tài khoản
        $sql = "SELECT * FROM taikhoan WHERE tentaikhoan = :id OR email = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $identifier]);
        
        // Trả về toàn bộ dòng dữ liệu của tài khoản đó (bao gồm cả cột matkhau và trangthai)
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkExists($username, $email) {
        $sql = "SELECT * FROM taikhoan WHERE tentaikhoan = :user OR email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user' => $username, ':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function register($accountData, $personalData) {
    try {
        $this->db->beginTransaction();

        // 1. Chèn vào bảng taikhoan (trangthai lưu dạng chuỗi)
        $sqlAcc = "INSERT INTO taikhoan (tentaikhoan, matkhau, email, loaitaikhoan, trangthai, ngaytao) 
                   VALUES (:user, :pass, :email, 'Người dùng', :status, NOW())";
        $stmtAcc = $this->db->prepare($sqlAcc);
        $stmtAcc->execute([
            ':user'   => $accountData['username'],
            ':pass'   => password_hash($accountData['password'], PASSWORD_DEFAULT),
            ':email'  => $accountData['email'],
            ':status' => 'Chờ duyệt' // Mặc định trạng thái là chữ
        ]);

        $newUserId = $this->db->lastInsertId();

        // 2. Chèn vào bảng nguoidung
        $sqlProfile = "INSERT INTO nguoidung (id_nguoidung, hoten, gioitinh, ngaysinh, sdt, diachi, danhgia) 
                       VALUES (:id, :name, :gender, :dob, :phone, :address, 0.0)";
        $stmtProfile = $this->db->prepare($sqlProfile);
        $stmtProfile->execute([
            ':id'      => $newUserId,
            ':name'    => $personalData['name'],
            ':gender'  => $personalData['gioitinh'], // THÊM DÒNG NÀY
            ':dob'     => $personalData['ngaysinh'], // THÊM DÒNG NÀY
            ':phone'   => $personalData['phone'],
            ':address' => $personalData['address']
        ]);

        $this->db->commit();
        return true;
    } catch (Exception $e) {
        $this->db->rollBack();
        die("Lỗi SQL: " . $e->getMessage());
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