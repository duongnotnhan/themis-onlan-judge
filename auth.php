<?php
session_start();
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['register'])) {
    $stmt = $pdo->query("SELECT allow_registration FROM contest_settings LIMIT 1");
    $contest = $stmt->fetch();

    if ($contest['allow_registration'] == 0) {
        echo "<script>alert('Chức năng đăng ký đã bị tắt!');</script>";
    } else {
        $full_name = trim($_POST['full_name']);
        $class = trim($_POST['class']);
        $school = trim($_POST['school']);
        $username = trim($_POST['username']);
        $prepassword = $_POST['password'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $role = 'student';
        if (strlen($prepassword) < 8) {
            echo "<script>alert('Mật khẩu cần phải có ít nhất 8 ký tự!');</script>";
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                echo "<script>alert('Tên người dùng đã tồn tại, hãy chọn tên khác!');</script>";
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (full_name, class, school, username, password, role) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$full_name, $class, $school, $username, $password, $role])) {
                    echo "<script>alert('Đăng ký thành công. Vui lòng đăng nhập.');</script>";
                } else {
                    echo "<script>alert('Lỗi đăng ký!');</script>";
                }
            }
        }
    }
}


if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: index.php");
        }
    } else {
        echo "<script>alert('Sai tài khoản hoặc mật khẩu!');</script>";
    }
}
if (isset($_GET['logout'])) {
	session_destroy();
	header("Location: auth.php");
	exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập & Đăng Ký</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <style>
        .p-4 a {
            color: #FFD700 !important;
            text-decoration: none;
        }
    </style>
</head>
<body class="bg-dark text-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
	<div class="container">
		<a class="navbar-brand" href="index.php">OnLAN Judge</a>
	</div>
</nav>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div id="login-form" class="p-4 bg-secondary rounded">
                    <h3 class="text-center">Đăng Nhập</h3>
                    <form action="" method="POST">
                        <input type="text" name="username" placeholder="Tên đăng nhập" required class="form-control mb-3">
                        <input type="password" name="password" placeholder="Mật khẩu" required class="form-control mb-3">
                        <button type="submit" name="login" class="btn btn-primary w-100">Đăng Nhập</button>
                    </form>
                    <p class="text-center mt-3">
                        Chưa có tài khoản? 
                        <a href="#" onclick="toggleForms()">Đăng Ký</a>
                    </p>
                </div>
                <div id="register-form" class="p-4 bg-secondary rounded hidden">
                    <h3 class="text-center">Đăng Ký</h3>
                    <form action="" method="POST">
                        <input type="text" name="full_name" placeholder="Họ và Tên" required class="form-control mb-3">
                        <input type="text" name="class" placeholder="Lớp" required class="form-control mb-3">
                        <input type="text" name="school" placeholder="Trường" required class="form-control mb-3">
                        <input type="text" name="username" placeholder="Tên đăng nhập" required class="form-control mb-3">
                        <input type="password" name="password" placeholder="Mật khẩu" required class="form-control mb-3">
                        <button type="submit" name="register" class="btn btn-success w-100">Đăng Ký</button>
                    </form>
                    <p class="text-center mt-3">
                        Đã có tài khoản? 
                        <a href="#" onclick="toggleForms()">Đăng Nhập</a>
                    </p>
                </div>
            </div>
            <p class="text-center mt-3">
                Quên mật khẩu? <b>Vui lòng liên hệ Quản Trị Viên</b>.
            </p>
        </div>
    </div>
    <script>
        function toggleForms() {
            document.getElementById("login-form").classList.toggle("hidden");
            document.getElementById("register-form").classList.toggle("hidden");
        }
    </script>
</body>
<footer>
    <div class="text-center mt-3">
        <p>DuongNhanAC × ayor</p>
    </div>
</footer>
</html>
