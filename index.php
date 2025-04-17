<?php
session_start();
require 'config.php';

date_default_timezone_set("Asia/Ho_Chi_Minh");

$user = isset($_SESSION['user_id']) ? $_SESSION['username'] : null;

$contest = $pdo->query("SELECT * FROM contest_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
$startTime = strtotime($contest['start_time']);
$endTime = strtotime($contest['end_time']);
$now = time();

$problems = $pdo->query("SELECT * FROM problems WHERE order_id >= 1 ORDER BY order_id ASC")->fetchAll(PDO::FETCH_ASSOC);

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
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/site.webmanifest">
	<title>Themis OnLAN Judge</title>
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap-icons.css">
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
			<a class="navbar-brand" href="#">Themis OnLAN Judge <span id="ping-result" class="fw-bold"></span></a>
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
					<h4 class="text-center"><i class="bi bi-list-ul"></i> Danh Sách Đề Bài</h4>
					<table class="table table-bordered table-dark table-striped table-hover">
						<thead class="table-light text-dark text-center align-middle">
							<tr>
								<th style="width: 10%;">ID</th>
								<th style="min-width: 25%;">Tên Đề Bài</th>
								<th style="width: 5%;">Điểm</th>
								<th style="min-width: 15%;">Thời Gian</th>
								<th style="min-width: 15%;">Bộ Nhớ</th>
								<th style="min-width: 10%;"></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($problems as $problem): ?>
							<tr class="align-middle">
								<td><?= htmlspecialchars($problem['name']) ?></td>
								<td><?= htmlspecialchars($problem['full_name']) ?></td>
								<td class="text-center"><?= htmlspecialchars($problem['total_score']) ?></td>
								<td class="text-center"><?= htmlspecialchars($problem['time_limit']) ?>s</td>
								<td class="text-center"><?= htmlspecialchars($problem['memory_limit']) ?>MiB</td>
								<td class="text-center">
									<button class="btn btn-info btn-sm" onclick="viewProblem('<?= htmlspecialchars($problem['name']) ?>')"><i class="bi bi-eye"></i></button>
									<?php if (isset($_SESSION['user_id'])): ?>
										<button class="btn btn-secondary btn-sm viewHistory" data-problem="<?php echo $problem['name']; ?>"><i class="bi bi-clock-history"></i></button>
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
				<h4 class="text-center text-danger"><i class="bi bi-hourglass-split"></i> Kỳ thi chưa bắt đầu hoặc đã kết thúc!</h4>
			<?php endif; ?>

			<div class="col-md-6">
				<h4 class="text-center"><i class="bi bi-bar-chart"></i> Bảng Xếp Hạng</h4>
				<div class="table-responsive">
					<table class="table table-dark table-bordered table-hover rounded-3 shadow-lg" id="rankingTable">
					<thead class="table-light text-dark text-center">
						<tr>
							<th style="width: 10%;">#</th>
							<th>Thí Sinh</th>
							<th style="min-width: 30%;">Tổng Điểm</th>
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
				<h5 class="modal-title" id="problemName"></h5> <i class="bi bi-dash-lg"></i> <h5 class="modal-title" id="problemFullName"></h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
				<div class="d-flex justify-content-center text-center gap-4" style="margin-bottom:15px;">
					<div><strong>Điểm tổng:</strong> <span id="problemScore"></span></div>
					<div><strong>Thời gian:</strong> <span id="problemTime"></span> s</div>
					<div><strong>Bộ nhớ:</strong> <span id="problemMemory"></span> MiB</div>
					<div><strong>Giới hạn lần nộp:</strong> <span id="submissionsLimit"></span></div>
				</div>
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
							<label class="form-check-label" for="uploadOption">Tải tệp tin lên</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="submit_type" id="editorOption" value="editor">
							<label class="form-check-label" for="editorOption">Soạn thảo trực tiếp</label>
						</div>
					</div>

					<div id="uploadSection">
						<label for="codeFile" class="form-label">Chọn tệp tin:</label>
						<input type="file" class="form-control" name="code_file" id="codeFile" required>
					</div>

					<div id="editorSection" style="display: none;">
						<label for="codeEditor" class="form-label">Soạn thảo mã nguồn:</label>
						<textarea class="form-control language-markdown" name="code_editor" id="codeEditor" rows="14"></textarea>
						<label for="languageSelect" class="form-label mt-2">Chọn ngôn ngữ:</label>
						<select class="form-select" name="language" id="languageSelect">
							<option value="C">C</option>
							<option value="CPP">C++</option>
							<option value="PY">Python</option>
							<option value="PAS">Pascal</option>
						</select>
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
		$("input[name='submit_type']").on("change", function () {
			if ($(this).val() === "file") {
				$("#uploadSection").show();
				$("#editorSection").hide();
				$("#codeFile").prop("required", true);
				$("#codeEditor").prop("required", false);
			} else {
				$("#uploadSection").hide();
				$("#editorSection").show();
				$("#codeFile").prop("required", false);
				$("#codeEditor").prop("required", true);
			}
		});

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
		fetchRanking();
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