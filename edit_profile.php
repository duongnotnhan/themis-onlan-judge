<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: auth.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT full_name, class, school FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $class = trim($_POST['class']);
    $school = trim($_POST['school']);

    if ($full_name === "" || $class === "" || $school === "") {
        $error = "Vui lòng điền đầy đủ thông tin!";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, class = ?, school = ? WHERE id = ?");
        $stmt->execute([$full_name, $class, $school, $user_id]);
        $success = "Cập nhật thành công!";
        $_SESSION['full_name'] = $full_name;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Thông Tin Cá Nhân</title>
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
<div class="container mt-5">
    <h2>Thông Tin Cá Nhân</h2>
    <hr>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" class="bg-secondary p-4 rounded" style="margin-bottom:15px;">
        <div class="mb-3">
            <label for="full_name" class="form-label">Họ và tên</label>
            <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="class" class="form-label">Lớp</label>
            <input type="text" class="form-control" id="class" name="class" value="<?= htmlspecialchars($user['class']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="school" class="form-label">Trường</label>
            <input type="text" class="form-control" id="school" name="school" value="<?= htmlspecialchars($user['school']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="index.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>
</body>
<footer>
    <div class="text-center mt-3">
        <p>DuongNhanAC × ayor</p>
    </div>
</footer>
</html>
