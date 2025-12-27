<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản trị hệ thống - C2C</title>
    <link rel="stylesheet" href="/quanlyc2c/Public/Css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include_once 'layouts/sidebar.php'; ?>

        <div class="main-layout">
            <?php include_once 'layouts/header.php'; ?>

            <main class="content-area">
                <?php 
                    if (isset($contentView) && file_exists($contentView)) {
                        include_once $contentView;
                    } else {
                        echo '<div class="welcome-box"><h3>Chào mừng bạn đến với trang quản trị</h3></div>';
                    }
                ?>
            </main>
        </div>
    </div>
    <script src="/quanlyc2c/Public/Js/script.js"></script>
</body>
</html>