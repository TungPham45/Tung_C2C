<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Quản lý</title>
    <link rel="stylesheet" href="/quanlyc2c/Public/Css/style.css">
</head>
<body>
    <div class="auth-container wide">
        <h2 style="color: #e74a3b;">DASHBOARD QUẢN LÝ</h2>
        <div class="alert alert-success">
            Chào mừng Quản lý: <strong><?php echo $username; ?></strong>
        </div>
        <p style="text-align: center;">Đây là trang dành cho <strong>Quản lý</strong>. Bạn có quyền điều hành hệ thống.</p>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="/quanlyc2c/Public/auth/logout" class="btn" style="background: #e74a3b; text-decoration: none; display: inline-block; width: auto; padding: 10px 20px;">Đăng xuất</a>
        </div>
    </div>
</body>
</html>