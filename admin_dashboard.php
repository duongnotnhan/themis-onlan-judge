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
        $pdo->query("DELETE FROM submissions WHERE problem_id IN (SELECT id FROM problems)");
        $pdo->query("DELETE FROM problems");
    } elseif ($reset_option === 'submissions') {
        $pdo->query("DELETE FROM submissions");
    } elseif ($reset_option === 'users') {
        $pdo->query("DELETE FROM submissions WHERE user_id IN (SELECT id FROM users WHERE role != 'admin')");
        $pdo->query("DELETE FROM users WHERE role != 'admin'");
    }

    echo "<script>
        alert('Đặt lại dữ liệu thành công!');
        window.location.href = 'admin_dashboard.php';
    </script>";
    exit();
}

if (isset($_POST['update_registration'])) {
    $allow_registration = $_POST['allow_registration'];
    $stmt = $pdo->prepare("UPDATE contest_settings SET allow_registration = ?");
    $stmt->execute([$allow_registration]);

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
            echo "<script>alert('Không tìm thấy người dùng có tên \"$username\"!');</script>";
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

    $stmt = $pdo->prepare("UPDATE contest_settings SET title = ?, start_time = ?, end_time = ?, submission_path = ?");
    $stmt->execute([$title, $start_time, $end_time, $submission_path]);

    header("Location: admin_dashboard.php");
    exit();
}
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: auth.php");
    exit;
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
    <link rel="stylesheet" href="assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-dark text-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
		<div class="container">
			<a class="navbar-brand" href="index.php">OnLAN Judge</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse justify-content-end" id="navbarNav">
				<a href="ranking.php" class="btn btn-outline-light">
					<i class="bi bi-bar-chart"></i> Bảng Xếp Hạng
				</a>
				<?php if (isset($_SESSION['user_id'])): ?>
					<a href="submissions.php" class="btn btn-outline-light">
						<i class="bi bi-clock-history"></i> Lịch Sử Nộp Bài
					</a>
					<div class="dropdown">
						<button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
							<i class="bi bi-person-circle"></i>
						</button>
						<ul class="dropdown-menu dropdown-menu-end">
							<li><span class="dropdown-item-text" style='color:white;'>Xin chào, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span></li>
							<li><hr class="dropdown-divider"></li>
							<?php if ($_SESSION['role'] === 'admin'): ?>
								<li>
									<a href="problems.php" class="dropdown-item">
										<i class="bi bi-journal-text"></i> Danh Sách Đề Bài
									</a>
								</li>
								<li>
									<a href="admin_dashboard.php" class="dropdown-item">
										<i class="bi bi-speedometer2"></i> Bảng Điều Khiển
									</a>
								</li>
							<?php endif; ?>
							<li>
								<a href="edit_profile.php" class="dropdown-item">
									<i class="bi bi-pencil-square"></i> Chỉnh Sửa Thông Tin
								</a>
							</li>
							<li>
								<a href="change_password.php" class="dropdown-item">
									<i class="bi bi-key"></i> Đổi Mật Khẩu
								</a>
							</li>
							<li><hr class="dropdown-divider"></li>
							<li>
								<a href="?logout" class="dropdown-item text-danger">
									<i class="bi bi-box-arrow-right"></i> Đăng Xuất
								</a>
							</li>
						</ul>
					</div>
				<?php else: ?>
					<a href="auth.php" class="btn btn-success">
						<i class="bi bi-person-plus-fill"></i> Đăng Nhập/Đăng Ký
					</a>
				<?php endif; ?>
			</div>
		</div>
	</nav>
    <div class="container mt-5" style="margin-bottom:15px;">
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
        
        <h2 class="mt-5">Đặt Lại Dữ Liệu</h2>
        <form method="POST" class="bg-danger p-3 rounded">
            <label class="form-label">Chọn dữ liệu cần đặt lại:</label>
            <select name="reset_option" class="form-control mb-3">
                <option value="problems">Đặt Lại Đề Bài</option>
                <option value="submissions">Đặt Lại Bài Nộp</option>
                <option value="users">Đặt Lại Người Dùng (trừ Quản Trị Viên)</option>
            </select>
            <button type="submit" name="reset" class="btn btn-warning">Thực Thi</button>
        </form>

        <h2 class="mt-5">Đặt Lại Mật Khẩu</h2>
        <form method="POST" class="bg-danger p-3 rounded">
            <label class="form-label">Tên Người Dùng Của Người Dùng:</label>
            <input type="text" name="username" class="form-control mb-3" placeholder="Nhập Tên Người Dùng">
            <button type="submit" name="resetstudentpassword" class="btn btn-warning">Thực Thi</button>
        </form>
    </div>
</body>
<footer>
    <div class="text-center mt-3">
        <p>DuongNhanAC × ayor</p>
    </div>
</footer>
</html>
