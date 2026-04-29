<?php
// session_start();

include '../../backend/config/connect.php'; 

$error = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

  
    $sql = "SELECT MaUser, MatKhau, Role FROM users WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
    
        if (password_verify($password, $user['MatKhau'])) {
            $_SESSION['user_id'] = $user['MaUser'];
            $_SESSION['role'] = $user['Role'];
         
            if ($user['Role'] == 'BenhNhan') {
                $q = "SELECT HoTen FROM benhnhan WHERE MaUser = " . $user['MaUser'];
            } else if ($user['Role'] == 'BacSi') {
                $q = "SELECT HoTen FROM bacsi WHERE MaUser = " . $user['MaUser'];
            } else {
                $q = "SELECT HoTen FROM admin WHERE MaUser = " . $user['MaUser'];
            }

            $resName = $conn->query($q);
            if ($resName && $rowName = $resName->fetch_assoc()) {
                $_SESSION['ho_ten'] = $rowName['HoTen'];
            } else {
                $_SESSION['ho_ten'] = "Người dùng";
            }


            // header("Location: ../../index.php"); 
            include'../../backend/auth/login_process.php';
            exit();
        } else {
            $error = "Mật khẩu không chính xác.";
        }
    } else {
        $error = "Email không tồn tại.";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinical Curator - Đăng nhập</title>
    <link rel="stylesheet" href="../auth/css/style_login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <main class="login-card">
            <section class="info-side">
                <div class="logo">Phòng khám TH </div>
                <div class="info-content">
                    <h1>Trải nghiệm sự an tâm tuyệt đối trong chăm sóc sức khỏe.</h1>
                    <p>Cổng thông tin lâm sàng cá nhân của bạn. Truy cập hồ sơ y tế, đặt lịch chuyên gia và quản lý hành trình sức khỏe với sự minh bạch.</p>
                </div>
                <div class="hipaa-badge">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <strong>Truy cập bảo mật</strong>
                        <span>Tuân thủ HIPAA</span>
                    </div>
                </div>
            </section>

            <section class="form-side">
                <div class="form-wrapper">
                    <h2>Chào mừng trở lại</h2>
                    <p class="subtitle">Vui lòng nhập thông tin để truy cập bảng điều khiển của bạn.</p>

                    <?php if(!empty($error)): ?>
                        <div style="color: #e74c3c; background: #fdeaea; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #fabebb;">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="input-group">
                            <label>Email hoặc Tên đăng nhập</label>
                            <div class="input-field">
                                <i class="far fa-envelope"></i>
                                <input type="text" name="email" placeholder="dr.curator@clinical.com" required>
                            </div>
                        </div>

                        <div class="input-group">
                            <div class="label-row">
                                <label>Mật khẩu</label>
                                <a href="#" class="forgot-link">Quên mật khẩu?</a>
                            </div>
                            <div class="input-field">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" id="passwordInput" placeholder="••••••••••••" required>
                                <i class="far fa-eye toggle-password" id="togglePassword" style="cursor: pointer;"></i>
                            </div>
                        </div>

                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Ghi nhớ tôi</label>
                        </div>

                        <button type="submit" class="btn-login">
                            Đăng nhập <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>

                    <p class="register-text">Mới sử dụng Clinical Curator? <a href="register.php">Đăng ký tài khoản</a></p>

                    <footer class="form-footer">
                        <a href="#">Chính sách bảo mật</a>
                        <a href="#">Tiêu chuẩn bảo mật</a>
                        <a href="#">Liên hệ hỗ trợ</a>
                    </footer>
                </div>
            </section>
        </main>
        
        <footer class="page-footer">
            <span>© 2026 Phòng khám TH. EDITORIAL HEALTH SYSTEMS INC.</span>
            <span class="status"><span class="dot"></span> TRẠNG THÁI HỆ THỐNG: ỔN ĐỊNH</span>
        </footer>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#passwordInput');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>