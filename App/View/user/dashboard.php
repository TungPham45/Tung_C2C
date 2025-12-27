<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Người dùng</title>
    <link rel="stylesheet" href="/quanlyc2c/Public/Css/style.css">
</head>
<body>
    <div class="auth-container wide">
        <h2>TRANG CHỦ NGƯỜI DÙNG</h2>
        <div class="alert alert-success">
            Xin chào: <strong><?php echo $username; ?></strong>
        </div>
        <p style="text-align: center;">Đây là trang dành cho <strong>Người dùng</strong>. Bạn có thể mua bán và quản lý tin đăng của mình.</p>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="/quanlyc2c/Public/profile/show" class="btn" style="background: #4e73df; text-decoration: none; width: auto; padding: 10px 20px;">Thông tin cá nhân</a>
            <a href="/quanlyc2c/Public/auth/logout" class="btn" style="text-decoration: none; display: inline-block; width: auto; padding: 10px 20px;">Đăng xuất</a>
        </div>
    </div>
</body>
</html>