<?php
session_start();
require 'config.php';

date_default_timezone_set("Asia/Ho_Chi_Minh");

$user = isset($_SESSION['user_id']) ? $_SESSION['username'] : null;

$contest = $pdo->query("SELECT * FROM contest_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
$startTime = strtotime($contest['start_time']);
$endTime = strtotime($contest['end_time']);
$now = time();

$problems = $pdo->query("SELECT * FROM problems")->fetchAll(PDO::FETCH_ASSOC);

$rankings = $pdo->query("
	SELECT u.username, COALESCE(SUM(max_score), 0) AS total_score
	FROM users u
	LEFT JOIN (
		SELECT user_id, problem_id, MAX(score) AS max_score
		FROM submissions
		GROUP BY user_id, problem_id
	) s ON u.id = s.user_id
	GROUP BY u.id
	ORDER BY total_score DESC
")->fetchAll(PDO::FETCH_ASSOC);
if (isset($_GET['logout'])) {
	session_destroy();
	header("Location: index.php");
	exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Themis OnLAN Judge</title>
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link href="assets/css/prism.css" rel="stylesheet">
	<link rel="stylesheet" href="assets/css/styles.css">
	<link rel="stylesheet" href="assets/css/katex.min.css">
	<script src="assets/js/jquery-3.6.0.min.js"></script>
	<script src="assets/js/markdown-it.min.js"></script>
	<script src="assets/js/katex.min.js"></script>
	<script src="assets/js/auto-render.min.js"></script>
	<script src="assets/js/prism.js"></script>
	<script src="assets/js/main.js"></script>
</head>
<body class="bg-dark text-light">
	<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
	<div class="container">
		<a class="navbar-brand" href="#">OnLAN Judge</a>
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
	<div class="container my-4 text-center">
		<h3><?= htmlspecialchars($contest['title']) ?></h3>
		<p>
			<strong>Thời gian bắt đầu:</strong> <?= date("H:i:s d/m/Y", $startTime) ?><br>
			<strong>Thời gian kết thúc:</strong> <?= date("H:i:s d/m/Y", $endTime) ?>
		</p>
		<h4 id="countdown" class="text-warning" data-start-time="<?= $startTime ?>" data-end-time="<?= $endTime ?>"></h4>
	</div>

	<div class="container">
		<div class="row">
			<?php if ($now >= $startTime && $now < $endTime): ?>
				<div class="col-md-6">
					<h4 class="text-center">📜 Danh Sách Đề Bài</h4>
					<table class="table table-dark table-striped table-hover">
						<thead class="table-light text-dark text-center">
							<tr>
								<th style="width: 20%;">Tên bài</th>
								<th style="width: 5%;">Điểm</th>
								<th style="width: 20%;">Time-limit</th>
								<th style="width: 20%;">Memory-limit</th>
								<th style="width: 25%;"></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($problems as $problem): ?>
							<tr>
								<td><?= htmlspecialchars($problem['name']) ?></td>
								<td class="text-center"><?= htmlspecialchars($problem['total_score']) ?></td>
								<td class="text-center"><?= htmlspecialchars($problem['time_limit']) ?>s</td>
								<td class="text-center"><?= htmlspecialchars($problem['memory_limit']) ?>MiB</td>
								<td>
									<button class="btn btn-info btn-sm" onclick="viewProblem('<?= htmlspecialchars($problem['name']) ?>')">Xem</button>
									<?php if (isset($_SESSION['user_id'])): ?>
										<button class="btn btn-secondary btn-sm viewHistory" data-problem="<?php echo $problem['name']; ?>">
											Lịch sử nộp
										</button>
									<?php endif; ?>

									<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
										<div class="modal-dialog modal-lg">
											<div class="modal-content bg-dark text-light">
												<div class="modal-header">
													<h5 class="modal-title" id="historyModalLabel">Lịch sử nộp bài</h5>
													<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
												</div>
												<div class="modal-body">
													<table class="table table-dark table-striped table-hover text-center align-middle">
														<thead class="table-light text-dark text-center">
															<tr>
																<th style="width: 10%;">Thời gian</th>
																<th style="width: 8%;">Điểm</th>
																<th style="width: 8%;">Trạng thái</th>
																<th >Ngôn ngữ</th>
																<th style="width: 30%;">Mã nguồn</th>
																<th style="width: 32%;">Chi tiết chấm</th>
															</tr>
														</thead>
														<tbody id="historyTable"></tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php else: ?>
				<h4 class="text-center text-danger">⏳ Kỳ thi chưa bắt đầu hoặc đã kết thúc!</h4>
			<?php endif; ?>

			<div class="col-md-6">
				<h4 class="text-center">🏆 Bảng Xếp Hạng</h4>
				<div class="table-responsive">
					<table class="table table-dark table-bordered table-hover rounded-3 shadow-lg" id="rankingTable">
						<thead class="table-light text-dark text-center">
							<tr>
								<th style="width: 10%;">#</th>
								<th>Thí Sinh</th>
								<th style="width: 35%;">Tổng Điểm</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="problemModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content bg-dark text-light">
			<div class="modal-header border-secondary">
				<h5 class="modal-title" id="problemTitle"></h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div class="d-flex justify-content-center text-center gap-4">
					<div><strong>Điểm tổng:</strong> <span id="problemScore"></span></div>
					<div><strong>Thời gian:</strong> <span id="problemTime"></span> giây</div>
					<div><strong>Bộ nhớ:</strong> <span id="problemMemory"></span> MiB</div>
				</div>
				<button class="btn btn-success float-end" onclick="showSubmitForm()">Nộp Bài</button>
				<div class="markdown-content">
					<div id="problemDescription"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="submitModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content bg-dark text-light">
			<div class="modal-header border-secondary">
				<h5 class="modal-title" style="margin-right:0;">Nộp Bài</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<form id="submitForm">
					<input type="hidden" name="problem_name" id="submitProblemName">
					
					<div class="mb-3">
						<label class="form-label">Chọn phương thức nộp:</label>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="submit_type" id="uploadOption" value="file" checked>
							<label class="form-check-label" for="uploadOption">Tải file lên</label>
						</div>
					</div>

					<div id="uploadSection">
						<label for="codeFile" class="form-label">Chọn file:</label>
						<input type="file" class="form-control" name="code_file" id="codeFile" required>
					</div>
				</form>
			</div>
			<div class="modal-footer border-secondary">
				<button type="submit" class="btn btn-primary" form="submitForm">Gửi Bài</button>
			</div>
		</div>
	</div>
</div>

	<script>
		// $("input[name='submit_type']").on("change", function () {
		// 	if ($(this).val() === "file") {
		// 		$("#uploadSection").show();
		// 		$("#editorSection").hide();
		// 	} else {
		// 		$("#uploadSection").hide();
		// 		$("#editorSection").show();
		// 	}
		// });

		$("#submitForm").on("submit", function (e) {
			e.preventDefault();

			let formData = new FormData(this);

			$.ajax({
				url: "submit.php",
				type: "POST",
				data: formData,
				contentType: false,
				processData: false,
				dataType: "json",
				success: function (response) {
					if (response.error) {
						alert(response.error);
					} else {
						alert(response.success);
						$("#submitModal").modal("hide");
						fetchRanking(); 
					}
				},
				error: function () {
					alert("Lỗi khi gửi bài nộp!");
				}
			});
		});
	</script>
	<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
<footer class="footer">
    <div class="text-center mt-3">
        <p>Một cái footer bị lỗi...</p>
    </div>
</footer>
</html>
