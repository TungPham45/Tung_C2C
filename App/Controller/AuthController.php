<?php
// 1. Nạp thủ công các file thư viện PHPMailer
require_once __DIR__ . '/../PHPMailer/Exception.php';
require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';

// 2. Khai báo sử dụng Namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once '../App/Model/AuthModel.php';

class AuthController {
    private $authModel;

    public function __construct() {
        $this->authModel = new AuthModel();
    }

    /**
     * Hàm phụ trợ gửi Email OTP sử dụng SMTP Gmail
     */
    private function sendEmailOTP($recipientEmail, $otpCode) {
        $mail = new PHPMailer(true);

        try {
            // Cấu hình Server
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'tungp788@gmail.com'; // Email của bạn
            $mail->Password   = 'ndwm hwrw rjxl ahyg';    // MẬT KHẨU ỨNG DỤNG (16 ký tự)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // Người gửi và người nhận
            $mail->setFrom('your_email@gmail.com', 'Hệ thống C2C');
            $mail->addAddress($recipientEmail);

            // Nội dung Email
            $mail->isHTML(true);
            $mail->Subject = 'Mã xác thực OTP đặt lại mật khẩu';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; border: 1px solid #ddd; padding: 20px;'>
                    <h2 style='color: #4e73df;'>Xác thực tài khoản</h2>
                    <p>Chào bạn, mã OTP để đặt lại mật khẩu của bạn là:</p>
                    <div style='font-size: 24px; font-weight: bold; color: #e74a3b; letter-spacing: 5px;'>$otpCode</div>
                    <p>Mã này có hiệu lực trong vòng <b>5 phút</b>.</p>
                    <p>Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này.</p>
                </div>";

            return $mail->send();
        } catch (Exception $e) {
            // Có thể ghi log lỗi ở đây: $mail->ErrorInfo
            die("Lỗi gửi mail chi tiết: " . $mail->ErrorInfo);
        }
    }

    // --- 1. ĐĂNG NHẬP ---
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = $_POST['username_or_email'];
            $password = $_POST['password'];

            $user = $this->authModel->login($identifier);

            if ($user && password_verify($password, $user['matkhau'])) {
                
                // KIỂM TRA TRẠNG THÁI TÀI KHOẢN
                switch ($user['trangthai']) {
                    case 'Chờ duyệt':
                        $error = "Tài khoản của bạn đang chờ quản trị viên xét duyệt!";
                        require_once '../App/View/auth/login.php';
                        return; // Dùng return thay vì break để dừng hẳn hàm login

                    case 'Bị khóa':
                        $error = "Tài khoản này đã bị khóa do vi phạm chính sách!";
                        require_once '../App/View/auth/login.php';
                        return; // Dùng return thay vì break để dừng hẳn hàm login

                    case 'Hoạt động':
                        // 1. Lưu thông tin vào Session
                        $_SESSION['user_id'] = $user['id_nguoidung'];
                        $_SESSION['role'] = $user['loaitaikhoan'];
                        $_SESSION['username'] = $user['tentaikhoan'];
                        
                        // 2. PHÂN LUỒNG ĐIỀU HƯỚNG THEO LOẠI TÀI KHOẢN
                        header("Location: /quanlyc2c/Public/home/index");
                        exit();
                        break;

                    default:
                        $error = "Trạng thái không xác định: " . $user['trangthai'];
                        require_once '../App/View/auth/login.php';
                        break;
                }
            } else {
                $error = "Tài khoản hoặc mật khẩu không chính xác!";
                require_once '../App/View/auth/login.php';
            }
        } else {
            require_once '../App/View/auth/login.php';
        }
    }

    // --- 2. ĐĂNG KÝ (BƯỚC 1) ---
    public function registerStep1() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['tentaikhoan'];
            $email = $_POST['email'];
            
            if ($this->authModel->checkExists($username, $email)) {
                $error = "Tên đăng nhập hoặc Email đã được sử dụng!";
                require_once '../App/View/auth/register_step1.php';
            } else {
                $_SESSION['temp_account'] = [
                    'username' => $username,
                    'email'    => $email,
                    'password' => $_POST['matkhau']
                ];
                header("Location: /quanlyc2c/Public/auth/registerStep2");
                exit();
            }
        } else {
            require_once '../App/View/auth/register_step1.php';
        }
    }

    // --- ĐĂNG KÝ (BƯỚC 2) ---
    public function registerStep2() {
        if (!isset($_SESSION['temp_account'])) {
            header("Location: /quanlyc2c/Public/auth/registerStep1");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accountData = $_SESSION['temp_account'];
            

            $personalData = [
            'name'     => $_POST['fullname'],
            'gioitinh' => $_POST['gioitinh'], // THÊM DÒNG NÀY
            'ngaysinh' => $_POST['ngaysinh'], // THÊM DÒNG NÀY
            'phone'    => $_POST['sdt'],
            'address'  => $_POST['diachi']
            ];

            // SỬA TẠI ĐÂY: Truyền đủ 3 tham số vào hàm register
            $result = $this->authModel->register($accountData, $personalData);

            if ($result) {
                unset($_SESSION['temp_account']);
                $_SESSION['success'] = "Đăng ký thành công! Vui lòng chờ phê duyệt.";
                header("Location: /quanlyc2c/Public/auth/login");
                exit();
            } else {
                $error = "Có lỗi xảy ra trong quá trình lưu dữ liệu.";
                require_once '../App/View/auth/register_step2.php';
            }
        } else {
            require_once '../App/View/auth/register_step2.php';
        }
    }

    // --- 3. QUÊN MẬT KHẨU (BƯỚC 1: Gửi OTP) ---
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $user = $this->authModel->findByEmail($email);

            if ($user) {
                $otp = rand(100000, 999999);
                $_SESSION['reset_email'] = $email;
                $_SESSION['otp_code'] = $otp;
                $_SESSION['otp_expire'] = time() + 300; 

                // GỌI HÀM GỬI MAIL THẬT TẠI ĐÂY
                if ($this->sendEmailOTP($email, $otp)) {
                    header("Location: /quanlyc2c/Public/auth/verifyOtp");
                    exit();
                } else {
                    $error = "Hệ thống không thể gửi mail lúc này. Vui lòng thử lại!";
                    require_once '../App/View/auth/forgot_password.php';
                }
            } else {
                $error = "Email này không tồn tại trong hệ thống!";
                require_once '../App/View/auth/forgot_password.php';
            }
        } else {
            require_once '../App/View/auth/forgot_password.php';
        }
    }

    // QUÊN MẬT KHẨU (BƯỚC 2: Xác thực OTP)
    public function verifyOtp() {
        if (!isset($_SESSION['reset_email'])) {
            header("Location: /quanlyc2c/Public/auth/forgotPassword");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userOtp = $_POST['otp'];

            if ($userOtp == $_SESSION['otp_code'] && time() <= $_SESSION['otp_expire']) {
                $_SESSION['otp_verified'] = true;
                header("Location: /quanlyc2c/Public/auth/resetPassword");
                exit();
            } else {
                $error = "Mã OTP không đúng hoặc đã hết hạn!";
                require_once '../App/View/auth/verify_otp.php';
            }
        } else {
            require_once '../App/View/auth/verify_otp.php';
        }
    }

    // QUÊN MẬT KHẨU (BƯỚC 3: Đổi mật khẩu)
    public function resetPassword() {
        if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
            header("Location: /quanlyc2c/Public/auth/verifyOtp");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPass = $_POST['new_password'];
            $confirmPass = $_POST['confirm_password'];

            if ($newPass === $confirmPass) {
                $email = $_SESSION['reset_email'];
                $this->authModel->updatePassword($email, $newPass);
                
                unset($_SESSION['otp_code'], $_SESSION['otp_expire'], $_SESSION['reset_email'], $_SESSION['otp_verified']);
                
                $_SESSION['success'] = "Mật khẩu đã được cập nhật thành công!";
                header("Location: /quanlyc2c/Public/auth/login");
                exit();
            } else {
                $error = "Mật khẩu xác nhận không khớp!";
                require_once '../App/View/auth/reset_password.php';
            }
        } else {
            require_once '../App/View/auth/reset_password.php';
        }
    }

    // ĐĂNG XUẤT
    public function logout() {
        session_destroy();
        header("Location: /quanlyc2c/Public/auth/login");
    }
}