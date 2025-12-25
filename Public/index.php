<?php
session_start();

// Import các file cốt lõi
require_once '../Core/Database.php';
// Tự động load các Controller/Model nếu cần (hoặc dùng require thủ công)

// Lấy tham số url từ .htaccess
$url = isset($_GET['url']) ? explode('/', rtrim($_GET['url'], '/')) : ['auth', 'login'];

// Ví dụ URL: localhost/quanlyc2c/Public/auth/login
// $url[0] sẽ là Controller (auth)
// $url[1] sẽ là Action (login)

$controllerName = ucfirst($url[0]) . 'Controller';
$action = isset($url[1]) ? $url[1] : 'index';

// Kiểm tra xem file Controller có tồn tại không
$controllerPath = "../App/Controller/" . $controllerName . ".php";

if (file_exists($controllerPath)) {
    require_once $controllerPath;
    $controller = new $controllerName();
    
    if (method_exists($controller, $action)) {
        // Gọi hàm xử lý (Ví dụ: AuthController->login())
        $controller->$action();
    } else {
        echo "404 - Action không tồn tại!";
    }
} else {
    echo "404 - Controller không tồn tại!";
}