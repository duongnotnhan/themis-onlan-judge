<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
	header("Location: index.php");
	exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$problem_name = $_POST['name'];
	$problem_fullname = $_POST['full_name'];
	$time_limit = $_POST['time_limit'];
	$memory_limit = $_POST['memory_limit'];
	$total_score = $_POST['total_score'];
	$description = $_POST['description'];
	$stdin = isset($_POST['use_stdin']) ? 1 : 0;
	$stdout = isset($_POST['use_stdout']) ? 1 : 0;
	
	$stmt = $pdo->prepare("SELECT COUNT(*) FROM problems WHERE name = ?");
	$stmt->execute([$problem_name]);
	$count = $stmt->fetchColumn();
	
	if ($count > 0) {
		$error = "Tên đề bài (theo Themis) đã tồn tại! Vui lòng sửa lại dữ liệu!";
	} else {
		$stmt = $pdo->prepare("INSERT INTO problems (name, full_name, time_limit, memory_limit, total_score, description, stdin, stdout) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->execute([$problem_name, $problem_fullname, $time_limit, $memory_limit, $total_score, $description, $stdin, $stdout]);
		
		header("Location: problems.php");
		exit();
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
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/site.webmanifest">
	<title>Tạo Đề Bài</title>
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap-icons.css">
	<link rel="stylesheet" href="assets/css/styles.css">
	<link href="assets/css/prism.css" rel="stylesheet">
	<script src="assets/js/prism.js"></script>
	<script src="assets/js/markdown-it.min.js"></script>
	<link rel="stylesheet" href="assets/css/katex.min.css">
	<script src="assets/js/katex.min.js"></script>
	<script src="assets/js/auto-render.min.js"></script>
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
<div class="container mt-5">
	<h1>Tạo Đề Bài</h1>
	<?php if ($error): ?>
		<div class="alert alert-danger"> <?php echo $error; ?> </div>
	<?php endif; ?>
	<form method="POST">
		<div class="mb-3">
			<label class="form-label">Tên Đề Bài (Themis)</label>
			<input type="text" class="form-control" name="name" required>
		</div>
		<div class="mb-3">
			<label class="form-label">Tên Đề Bài (Đầy Đủ)</label>
			<input type="text" class="form-control" name="full_name" required>
		</div>
		<div class="mb-3">
			<label class="form-label">Tổng Điểm</label>
			<input type="number" class="form-control" name="total_score" step="any" required>
		</div>
		<div class="mb-3 form-check">
			<input type="checkbox" class="form-check-input" id="use_stdin" name="use_stdin">
			<label class="form-check-label" for="use_stdin">Sử dụng đầu vào chuẩn (stdin)?</label>
		</div>
		<div class="mb-3 form-check">
			<input type="checkbox" class="form-check-input" id="use_stdout" name="use_stdout">
			<label class="form-check-label" for="use_stdout">Sử dụng đầu ra chuẩn (stdout)?</label>
		</div>
		<div class="mb-3">
			<label class="form-label">Giới Hạn Thời Gian (s)</label>
			<input type="number" class="form-control" name="time_limit" value="<?php echo 1.0; ?>" step="any" required>
		</div>
		<div class="mb-3">
			<label class="form-label">Giới Hạn Bộ Nhớ (MiB)</label>
			<input type="number" class="form-control" name="memory_limit" value="<?php echo 1024; ?>" required>
		</div>
		<div class="mb-3">
			<label class="form-label">Mô Tả</label>
			<textarea id="markdown-input" class="form-control language-markdown" name="description" rows="6" required></textarea>
		</div>
		<h4>Xem Trước Mô Tả</h4>
		<div id="preview" class="bg-light text-dark p-3 rounded"></div>
		<hr>
		<button type="submit" class="btn btn-primary">Lưu</button>
		<a href="problems.php" class="btn btn-secondary">Hủy</a>
	</form>
</div>

<script>
	const md = markdownit({
		html: true,
		breaks: true
	});

	function renderMarkdown() {
		let inputText = document.getElementById('markdown-input').value;
		let renderedHTML = md.render(inputText);
		
		document.getElementById('preview').innerHTML = renderedHTML;

		renderMathInElement(document.getElementById('preview'), {
			delimiters: [
				{ left: "$$", right: "$$", display: true },
				{ left: "$", right: "$", display: false }
			],
			throwOnError: false
		});
	}

	document.getElementById('markdown-input').addEventListener('input', function() {
		let inputText = this.value;

		document.getElementById('markdown-input').textContent = inputText;

		renderMarkdown();
		Prism.highlightAll();
	});

	renderMarkdown();
	Prism.highlightAll();
</script>
</body>
<footer>
	<div class="text-center mt-3">
		<p>from <b>DuongNhanAC</b> × <b>ayor</b> with love <i class="bi bi-hearts"></i><br />
		<a href="https://github.com/duongnotnhan/themis-onlan-judge"><i class="bi bi-github"></i> Source Code</a></p>
	</div>
</footer>
</html>
