<?php
session_start();
require 'config.php';
date_default_timezone_set("Asia/Ho_Chi_Minh");

$problems = $pdo->query("SELECT id, name FROM problems WHERE order_id >= 1 ORDER BY order_id ASC")->fetchAll(PDO::FETCH_ASSOC);
if (isset($_GET['logout'])) {
	session_destroy();
	header("Location: ranking.php");
	exit;
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
	<title>Bảng Xếp Hạng</title>
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap-icons.css">
	<link rel="stylesheet" href="assets/css/styles.css">
	<style>
		table th, table td { text-align: center; vertical-align: middle; }
	</style>
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
<div class="container mt-4">
	<h2 class="text-center">Bảng Xếp Hạng</h2>
	<hr>
	<a href="export_results.php" class="btn btn-success mb-3">
		<i class="bi bi-download"></i> Tải xuống CSV
	</a>

	<div class="table-responsive">
		<table class="table table-dark table-striped table-hover" id="rankingTable">
			<thead class="table-light text-dark text-center">
				<tr>
					<th>#</th>
					<th>Thí Sinh</th>
					<?php foreach ($problems as $problem): ?>
						<th><?= htmlspecialchars($problem['name']) ?></th>
					<?php endforeach; ?>
					<th>Tổng Điểm</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
	function updateRanking() {
		fetch('fetch_fullranking.php')
			.then(response => response.text())
			.then(html => {
				document.querySelector('#rankingTable tbody').innerHTML = html;
			})
			.catch(error => console.error('Lỗi cập nhật BXH:', error));
	}
	setInterval(updateRanking, 5000);
	updateRanking();
</script>
</body>
<footer>
	<div class="text-center mt-3">
		<p>from <b>DuongNhanAC</b> × <b>ayor</b> with love <i class="bi bi-hearts"></i><br />
		<a href="https://github.com/duongnotnhan/themis-onlan-judge"><i class="bi bi-github"></i> Source Code</a></p>
	</div>
</footer>
</html>
