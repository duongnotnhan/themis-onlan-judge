<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM contest_settings LIMIT 1");
$contest = $stmt->fetch();

if (isset($_POST['reset'])) {
    $reset_option = $_POST['reset_option'];

    if ($reset_option === 'problems') {
        $pdo->query("DELETE FROM problems");
    } elseif ($reset_option === 'submissions') {
        $pdo->query("DELETE FROM submissions");
    } elseif ($reset_option === 'users') {
        $pdo->query("DELETE FROM users WHERE role != 'admin'");
    }

    echo "<script>
        alert('Reset dữ liệu thành công!');
        window.location.href = 'admin_dashboard.php';
    </script>";
    exit();
}

if (isset($_POST['update_registration'])) {
    $allow_registration = $_POST['allow_registration'];

    $stmt = $pdo->prepare("UPDATE contest_settings SET title = ?, start_time = ?, end_time = ?, submission_path = ?, allow_registration = ?");
    $stmt->execute([$contest['title'], $contest['start_time'], $contest['end_time'], $contest['submission_path'], $allow_registration]);

    echo "<script>
        alert('Cập nhật cài đặt đăng ký thành công!');
        window.location.href = 'admin_dashboard.php';
    </script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resetstudentpassword'])) {
        $username = trim($_POST['username']);

        if (empty($username)) {
            echo "<script>alert('Vui lòng nhập tên người dùng!');</script>";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if (!$user) {
                echo "<script>alert('Không tìm thấy thí sinh/người dùng có tên \"$username\"!');</script>";
            } else {
                $newPassword = bin2hex(random_bytes(4)); 
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

                $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
                $updateStmt->execute([$hashedPassword, $username]);

                echo "<script>alert('Mật khẩu mới của \"$username\": $newPassword');</script>";
            }
        }
    } 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contest_settings'])) {
    $title = $_POST['title'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $submission_path = $_POST['submission_path'];

    $stmt = $pdo->prepare("UPDATE contest_settings SET title = ?, start_time = ?, end_time = ?, submission_path = ?, allow_registration = ?");
    $stmt->execute([$title, $start_time, $end_time, $submission_path, $contest['allow_registration']]);

    header("Location: admin_dashboard.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM problems");
$problems = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="assets/css/prism.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
	<div class="container">
		<a class="navbar-brand" href="index.php">OnLAN Judge</a>
		<div class="d-flex align-items-center">
			<?php if (isset($_SESSION['user_id'])): ?>
				<span class="navbar-text me-3">Xin chào, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
				<?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="submissions.php" class="btn btn-outline-light me-2">Lịch Sử Nộp Bài</a>
					<a href="problems.php" class="btn btn-outline-light me-2">Danh Sách Đề Bài</a>
					<a href="admin_dashboard.php" class="btn btn-outline-light me-2 active">Bảng Điều Khiển</a>
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
        <h1>Bảng Điều Khiển</h1>
        <hr>
        <h2>Quản Lý Kỳ Thi</h2>
        <form method="POST" class="bg-secondary p-3 rounded">
            <label class="form-label">Tên kỳ thi</label>
            <input type="text" name="title" value="<?= htmlspecialchars($contest['title']) ?>" class="form-control mb-3">
            
            <label class="form-label">Thời gian bắt đầu</label>
            <input type="datetime-local" name="start_time" value="<?= $contest['start_time'] ?>" class="form-control mb-3">
            
            <label class="form-label">Thời gian kết thúc</label>
            <input type="datetime-local" name="end_time" value="<?= $contest['end_time'] ?>" class="form-control mb-3">
            
            <label class="form-label">Thư mục bài nộp</label>
            <input type="text" name="submission_path" value="<?= htmlspecialchars($contest['submission_path']) ?>" class="form-control mb-3">
            
            <button type="submit" name="contest_settings" class="btn btn-primary">Lưu thay đổi</button>
        </form>

        <h2 class="mt-5">Danh Sách Đề Bài</h2><small>(tất cả đều sẽ được sử dụng trong kỳ thi)</small>
        <br>Xem tại <a href="problems.php">Đây</a>

        <h2 class="mt-5">Cài Đặt Đăng Ký</h2>
        <form method="POST" class="bg-secondary p-3 rounded">
            <label class="form-label">Cho phép đăng ký tài khoản?</label>
            <select name="allow_registration" class="form-control mb-3">
                <option value="1" <?= ($contest['allow_registration'] == 1) ? 'selected' : '' ?>>Bật</option>
                <option value="0" <?= ($contest['allow_registration'] == 0) ? 'selected' : '' ?>>Tắt</option>
            </select>
            <button type="submit" name="update_registration" class="btn btn-primary">Lưu Thay Đổi</button>
        </form>
        
        <h2 class="mt-5">Reset Dữ Liệu</h2>
        <form method="POST" class="bg-danger p-3 rounded">
            <label class="form-label">Chọn dữ liệu cần reset:</label>
            <select name="reset_option" class="form-control mb-3">
                <option value="problems">Reset Problems</option>
                <option value="submissions">Reset Submissions</option>
                <option value="users">Reset Users (trừ Admin)</option>
            </select>
            <button type="submit" name="reset" class="btn btn-warning">Reset</button>
        </form>

        <h2 class="mt-5">Đặt Lại Mật Khẩu</h2>
        <form method="POST" class="bg-danger p-3 rounded">
            <label class="form-label">Tên Người Dùng Của Thí Sinh/Người Dùng:</label>
            <input type="text" name="username" class="form-control mb-3" placeholder="Nhập Tên Người Dùng">
            <button type="submit" name="resetstudentpassword" class="btn btn-warning">Thực Thi</button>
        </form>
    </div>
</body>
<footer class="footer">
    <div class="text-center mt-3">
        <p>Một cái footer bị lỗi...</p>
    </div>
</footer>
</html>
