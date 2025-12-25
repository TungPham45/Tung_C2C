<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - Bước 1</title>
    <link rel="stylesheet" href="/quanlyc2c/Public/Css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="step-indicator">Bước 1/2: Thiết lập tài khoản</div>
        <h2>ĐĂNG KÝ</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="/quanlyc2c/Public/auth/registerStep1" method="POST">
            <div class="form-group">
                <label>Tên đăng nhập</label>
                <input type="text" name="tentaikhoan" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="matkhau" required>
            </div>
            <div class="form-group">
                <label>Bạn là ai?</label>
                <div class="radio-group">
                    <label><input type="radio" name="loaitaikhoan" value="Người dùng" checked> Người dùng</label>
                    <label><input type="radio" name="loaitaikhoan" value="Quản lý"> Quản lý</label>
                </div>
            </div>
            <button type="submit" class="btn">Tiếp theo &raquo;</button>
        </form>
    </div>
</body>
</html>