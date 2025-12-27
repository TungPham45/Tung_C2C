<?php
class AdminModel {
    private $db;
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    // Lấy tất cả tài khoản kèm họ tên từ bảng nguoidung
    public function getAllAccounts() {
        // JOIN bảng taikhoan và nguoidung
        $sql = "SELECT tk.id_nguoidung, nd.hoten, tk.email, tk.trangthai 
                FROM taikhoan tk 
                JOIN nguoidung nd ON tk.id_nguoidung = nd.id_nguoidung";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAccountById($id) {
        $sql = "SELECT tk.*, nd.* FROM taikhoan tk 
                JOIN nguoidung nd ON tk.id_nguoidung = nd.id_nguoidung 
                WHERE tk.id_nguoidung = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật trạng thái (Phê duyệt, Khóa, Mở lại)
    public function updateStatus($id, $status) {
        $sql = "UPDATE taikhoan SET trangthai = :status WHERE id_nguoidung = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    // Xóa tài khoản (Xóa cả 2 bảng)
    public function deleteAccount($id) {
        try {
            $this->db->beginTransaction();
            $this->db->prepare("DELETE FROM nguoidung WHERE id_nguoidung = :id")->execute([':id' => $id]);
            $this->db->prepare("DELETE FROM taikhoan WHERE id_nguoidung = :id")->execute([':id' => $id]);
            return $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}