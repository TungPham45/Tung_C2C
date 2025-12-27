<?php
class ProfileModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function getProfile($userId) {
        // JOIN đơn giản hơn vì dùng chung khóa manguoidung
        $sql = "SELECT tk.tentaikhoan, tk.email, tk.loaitaikhoan, tk.trangthai, 
                       nd.hoten, nd.sdt, nd.diachi, nd.anhdaidien, nd.gioithieu, nd.danhgia 
                FROM taikhoan tk 
                JOIN nguoidung nd ON tk.id_nguoidung = nd.id_nguoidung
                WHERE tk.id_nguoidung = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($userId, $data) {
        // Cập nhật các trường mới bao gồm ảnh đại diện và giới thiệu
        $sql = "UPDATE nguoidung SET 
                hoten = :name, 
                sdt = :phone, 
                diachi = :address, 
                anhdaidien = :avatar, 
                gioithieu = :bio 
                WHERE id_nguoidung = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name'    => $data['name'],
            ':phone'   => $data['phone'],
            ':address' => $data['address'],
            ':avatar'  => $data['avatar'],
            ':bio'     => $data['bio'],
            ':id'      => $userId
        ]);
    }
}