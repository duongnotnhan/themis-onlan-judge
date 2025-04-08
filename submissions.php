<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
	header("Location: index.php");
	exit;
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
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/site.webmanifest">
	<title>Danh Sách Bài Nộp</title>
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap-icons.css">
	<link rel="stylesheet" href="assets/css/styles.css">
	<link href="assets/css/prism.css" rel="stylesheet">
	<script src="assets/js/prism.js"></script>
	<script src="assets/js/jquery-3.6.0.min.js"></script>
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
	<div class="container mt-5 pt-4">
		<h3 class="text-center mt-4"><i class="bi bi-list-ul"></i> Tất Cả Bài Nộp</h3>
		<hr>
		<table class="table table-dark table-striped table-hover">
			<thead class="table-light text-dark text-center">
				<tr>
					<th style="width: 10%;">Trạng Thái</th>
					<th style="width: 10%;">Điểm Số</th>
					<th style="width: 20%;">Tên Bài</th>
					<th style="width: 20%;">Quản Trị</th>
				</tr>
			</thead>
			<tbody id="submissionTable">
			</tbody>
		</table>
	</div>

	<div class="modal fade" id="viewSubmissionModal" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content bg-dark text-light">
				<div class="modal-header border-secondary">
					<h5 class="modal-title" id="submissionTitle"></h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<p>
						<strong>Điểm:</strong> <span id="submissionScore"></span><br>
						<strong>Trạng thái:</strong> <span id="submissionStatus"></span><br>
						<strong>Ngôn ngữ:</strong> <span id="submissionLanguage"></span>
					</p>
					<h4>Mã Nguồn</h4>
					<pre><code id="submissionCode" class="language-cpp"></code></pre>
					<h4>Chi Tiết Chấm</h4>
					<pre><code id="submissionLogs" class="language-log"></code></pre>
				</div>
			</div>
		</div>
	</div>

	<script>
		function loadSubmissions() {
			$.ajax({
				url: "load_submissions.php",
				type: "GET",
				success: function(response) {
					$("#submissionTable").html(response);
				}
			});
		}

		$(document).ready(function () {
			loadSubmissions();
			setInterval(loadSubmissions, 5000);

			$(document).on("click", ".viewSubmission", function () {
				let submissionId = $(this).data("id");
				$.ajax({
					url: "view_submission.php",
					type: "GET",
					dataType: "json",
					data: { id: submissionId },
					success: function(data) {
						if (data.error) {
							alert(data.error);
							return;
						}
						//let data = JSON.parse(response);
						$("#submissionTitle").text(`Bài nộp ${data.problem_name} của ${data.username}`);
						$("#submissionScore").text(`${data.score}`);
						$("#submissionStatus").text(data.status);
						$("#submissionLanguage").text(`${data.language}`);
						let prismClass = "language-cpp";
						switch (data.language) {
							case "C": prismClass = "language-c"; break;
							case "CPP": prismClass = "language-cpp"; break;
							case "PY": prismClass = "language-python"; break;
							case "PAS": prismClass = "language-pascal"; break;
						}
						$("#submissionCode").attr("class", `code-block ${prismClass}`).text(data.code);
						$("#submissionLogs").attr("class", "code-block language-log").text(data.logs);
						
						Prism.highlightAll();
						$("#viewSubmissionModal").modal("show");
					}
				});
			});

			$(document).on("click", ".deleteSubmission", function () {
				let submissionId = $(this).data("id");
				if (confirm("Bạn có chắc chắn muốn xóa bài nộp này?")) {
					$.ajax({
						url: "delete_submission.php",
						type: "POST",
						data: { id: submissionId },
						success: function(response) {
							alert(response);
							loadSubmissions();
						}
					});
				}
			});
			$(document).on("click", ".rejudgeSubmission", function () {
				let submissionId = $(this).data("id");
				if (confirm("Bạn có chắc chắn muốn chấm lại bài nộp này?")) {
					$.ajax({
						url: "rejudge_submission.php",
						type: "POST",
						data: { id: submissionId },
						success: function(response) {
							alert(response);
							loadSubmissions();
						}
					});
				}
			});
		});
	</script>

	<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
<footer>
	<div class="text-center mt-3">
		<p>from <b>DuongNhanAC</b> × <b>ayor</b> with love <i class="bi bi-hearts"></i><br />
		<a href="https://github.com/duongnotnhan/themis-onlan-judge"><i class="bi bi-github"></i> Source Code</a></p>
	</div>
</footer>
</html>
