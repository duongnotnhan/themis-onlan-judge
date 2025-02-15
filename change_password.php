<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$message = "";
if (isset($_GET['logout'])) {
	session_destroy();
	header("Location: auth.php");
	exit;
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = "Vui lòng điền đầy đủ thông tin!";
    } elseif ($new_password !== $confirm_password) {
        $message = "Mật khẩu mới và xác nhận mật khẩu không khớp!";
    } elseif (strlen($new_password) < 8) {
        $message = "Mật khẩu mới phải có ít nhất 8 ký tự!";
    } else {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
            $update_stmt->execute([$hashed_password, $username]);

            $message = "✅ Đổi mật khẩu thành công!";
        } else {
            $message = "Mật khẩu cũ không đúng!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi Mật Khẩu</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-dark text-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
	<div class="container">
		<a class="navbar-brand" href="index.php">OnLAN Judge</a>
		<div class="d-flex align-items-center">
			<?php if (isset($_SESSION['user_id'])): ?>
				<span class="navbar-text me-3">Xin chào, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                <a href="submissions.php" class="btn btn-outline-light me-2">Lịch Sử Nộp Bài</a>
				<?php if ($_SESSION['role'] === 'admin'): ?>
					<a href="problems.php" class="btn btn-outline-light me-2">Danh Sách Đề Bài</a>
					<a href="admin_dashboard.php" class="btn btn-outline-light me-2">Bảng Điều Khiển</a>
				<?php endif; ?>
				<a href="change_password.php" class="btn btn-warning me-2">Đổi Mật Khẩu</a>
				<a href="?logout" class="btn btn-danger">Đăng Xuất</a>
			<?php else: ?>
				<a href="auth.php" class="btn btn-success">Đăng Nhập/Đăng Ký</a>
			<?php endif; ?>
		</div>
	</div>
</nav>
    <div class="container mt-5">
        <h1 class="text-center">Đổi Mật Khẩu</h1>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <form method="POST" class="bg-secondary p-4 rounded">
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100"><i class="fa fa-key"></i>Đổi Mật Khẩu</button>
                </form>

                <div class="text-center mt-3">
                    <a href="index.php" class="btn btn-success">Quay lại Trang Chủ</a>
                </div>
            </div>
        </div>
    </div>
</body>
<footer class="footer">
    <hr>
    <div class="text-center mt-3">
        <p>Một cái footer bị lỗi...</p>
    </div>
</footer>
</html>
