<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username'])) {
	header("Location: login.php");
	exit();
}

$username = $_SESSION['username'];
$message = "";

if (!isset($_SESSION['captcha_a']) || !isset($_SESSION['captcha_b']) || !isset($_SESSION['captcha_op']) || isset($_GET['new_captcha'])) {
	$ops = ['+', '-', '*'];
	$op = $ops[array_rand($ops)];
	$a = rand(1, 9);
	$b = rand(1, 9);

	switch ($op) {
		case '+':
			$answer = $a + $b;
			break;
		case '-':
			if ($a < $b) list($a, $b) = [$b, $a];
			$answer = $a - $b;
			break;
		case '*':
			$answer = $a * $b;
			break;
	}

	$_SESSION['captcha_a'] = $a;
	$_SESSION['captcha_b'] = $b;
	$_SESSION['captcha_op'] = $op;
	$_SESSION['captcha_answer'] = $answer;
}

if (isset($_GET['logout'])) {
	session_destroy();
	header("Location: auth.php");
	exit;
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$current_password = $_POST['current_password'];
	$new_password = $_POST['new_password'];
	$confirm_password = $_POST['confirm_password'];
	$captcha = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';

	if (empty($current_password) || empty($new_password) || empty($confirm_password) || empty($captcha)) {
		$message = "Vui lòng điền đầy đủ thông tin!";
	} elseif ($new_password !== $confirm_password) {
		$message = "Mật khẩu mới và xác nhận mật khẩu không khớp!";
	} elseif (
		strlen($new_password) < 8 ||
		!preg_match('/\d/', $new_password) ||
		!preg_match('/[\W_]/', $new_password)
	) {
		$message = "Mật khẩu cần ít nhất 8 ký tự và đảm bảo chứa ít nhất 1 chữ số, 1 ký tự đặc biệt!";
	} elseif (
		!isset($_SESSION['captcha_answer']) ||
		$captcha !== strval($_SESSION['captcha_answer'])
	) {
		$message = "Captcha không đúng!";
	} else {
		$stmt = $pdo->prepare("SELECT password FROM users WHERE username = ?");
		$stmt->execute([$username]);
		$user = $stmt->fetch();

		if ($user && password_verify($current_password, $user['password'])) {
			$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
			$update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
			$update_stmt->execute([$hashed_password, $username]);

			$message = "✓ Đổi mật khẩu thành công!";
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
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/site.webmanifest">
	<title>Đổi Mật Khẩu</title>
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap-icons.css">
	<link rel="stylesheet" href="assets/css/styles.css">
	<script src="assets/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-dark text-light">
		<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
		<div class="container">
			<a class="navbar-brand" href="index.php">Themis OnLAN Judge</a>
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
		<h2 class="text-center">Đổi Mật Khẩu</h2>
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
	<div class="mb-3">
		<label for="captcha" class="form-label">Xác nhận:</label>
		<div class="input-group align-items-center">
			<img src="captcha.php" alt="captcha" style="height:36px;margin-right:10px;" id="captcha-img">
			<input type="text" name="captcha" id="captcha" placeholder="Trả lời" required class="form-control">
			<button type="button" class="btn btn-outline-light" onclick="refreshCaptcha()">Làm mới</button>
		</div>
	</div>
	<button type="submit" class="btn btn-warning w-100"><i class="bi bi-key-fill"></i> Đổi Mật Khẩu</button>
</form>

				<div class="text-center mt-3">
					<a href="index.php" class="btn btn-success">Quay lại Trang Chủ</a>
				</div>
			</div>
		</div>
	</div>
</body>
<footer>
	<div class="text-center mt-3">
		<p>from <b>DuongNhanAC</b> × <b>ayor</b> with love <i class="bi bi-hearts"></i><br />
		<a href="https://github.com/duongnotnhan/themis-onlan-judge"><i class="bi bi-github"></i> Source Code</a></p>
	</div>
</footer>
<script>
function refreshCaptcha() {
	const img = document.getElementById('captcha-img');
	img.src = 'captcha.php?new=' + Date.now();
}
</script>
</html>
