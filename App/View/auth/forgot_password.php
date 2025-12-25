<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="/quanlyc2c/Public/Css/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>QUÊN MẬT KHẨU</h2>
        <p style="text-align: center; font-size: 0.9rem; color: #666;">Nhập email tài khoản để nhận mã xác thực OTP.</p>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="/quanlyc2c/Public/auth/forgotPassword" method="POST">
            <div class="form-group">
                <label>Email đăng ký</label>
                <input type="email" name="email" required placeholder="example@gmail.com">
            </div>
            <button type="submit" class="btn">Gửi mã OTP</button>
        </form>
        <div class="auth-footer">
            <a href="/quanlyc2c/Public/auth/login">Quay lại đăng nhập</a>
        </div>
    </div>
</body>
</html>