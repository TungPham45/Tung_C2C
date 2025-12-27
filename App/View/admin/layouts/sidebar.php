<?php
// Lấy giá trị url hiện tại từ tham số GET (giống trong file index.php của bạn)
$current_url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home/index';
?>

<aside class="sidebar">
    <div class="logo-box">
        <img src="/quanlyc2c/Public/Images/logo.png" alt="Logo" class="admin-logo">
        <span>C2C ADMIN</span>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="/quanlyc2c/Public/home/index" 
                   class="<?php echo ($current_url == 'home/index') ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
            </li>

            <li>
                <a href="/quanlyc2c/Public/admin/dashboard" 
                class="<?php echo (isset($active_page) && $active_page == 'user_management') ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Quản lý tài khoản
                </a>
            </li>

            <!-- <li>
                <a href="/quanlyc2c/Public/product/manage" 
                   class="<?php echo (strpos($current_url, 'product') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i> Quản lý sản phẩm
                </a>
            </li> -->
        </ul>
    </nav>
</aside>