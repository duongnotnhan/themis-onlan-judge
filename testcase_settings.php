<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
	header("Location: index.php");
	exit();
}

if (!isset($_GET['name'])) {
	echo "Tên đề bài không hợp lệ!";
	exit();
}

$problem_name = $_GET['name'];

$stmt = $pdo->prepare("SELECT * FROM problems WHERE name = ?");
$stmt->execute([$problem_name]);
$problem_info = $stmt->fetch();

$useStdIn = isset($problem_info['stdin']) ? (int)$problem_info['stdin'] : 0;
$useStdOut = isset($problem_info['stdout']) ? (int)$problem_info['stdout'] : 0;
$mark = isset($problem_info['total_score']) ? $problem_info['total_score'] : 100;
$timeLimit = isset($problem_info['time_limit']) ? $problem_info['time_limit'] : 1;
$memoryLimit = isset($problem_info['memory_limit']) ? $problem_info['memory_limit'] : 1024;

$stmt = $pdo->query("SELECT testcase_path FROM contest_settings LIMIT 1");
$contest_settings = $stmt->fetch();
$testcase_path = rtrim($contest_settings['testcase_path'], '/') . "/$problem_name/";

if (!is_dir($testcase_path)) {
	echo "Không tìm thấy thư mục testcase cho đề bài $problem_name!";
	exit();
}

$cfg_file = $testcase_path . "Settings.cfg";

if (!file_exists($cfg_file)) {
	$subfolders = array_filter(glob($testcase_path . '*'), 'is_dir');
	if (empty($subfolders)) {
		echo "Đề bài $problem_name có testcase nào không thế?";
		exit();
	}

	$xml = new SimpleXMLElement('<ExamInformation/>');
	$xml->addAttribute('Name', $problem_name);
	$xml->addAttribute('InputFile', $problem_name . '.INP');
	$xml->addAttribute('UseStdIn', $useStdIn);
	$xml->addAttribute('OutputFile', $problem_name . '.OUT');
	$xml->addAttribute('UseStdOut', $useStdOut);
	$xml->addAttribute('EvaluatorName', 'C1LinesWordsIgnoreCase.dll');
	$num_subfolders = count($subfolders) > 0 ? count($subfolders) : 1;
	$xml->addAttribute('Mark', $mark / $num_subfolders);
	$xml->addAttribute('TimeLimit', $timeLimit);
	$xml->addAttribute('MemoryLimit', $memoryLimit);

	foreach ($subfolders as $folder) {
		$name = basename($folder);
		$testcase = $xml->addChild('TestCase');
		$testcase->addAttribute('Name', $name);
		$testcase->addAttribute('Mark', '-1');
		$testcase->addAttribute('TimeLimit', '-1');
		$testcase->addAttribute('MemoryLimit', '-1');
	}

	$xml_content = $xml->asXML();
	$xml_content = preg_replace('/<\?xml.*?\?>/', '', $xml_content);

	$compressed_content = gzcompress($xml_content);
	file_put_contents($cfg_file, $compressed_content);

	$binary_content = file_get_contents($cfg_file);
	$decompressed_content = gzuncompress($binary_content);
	$xml = simplexml_load_string($decompressed_content);
} else {
	function decompressSettings($binaryData) {
		return gzuncompress($binaryData);
	}

	function compressSettings($plainText) {
		return gzcompress($plainText);
	}

	$binary_content = file_get_contents($cfg_file);
	$decompressed_content = decompressSettings($binary_content);

	$xml = simplexml_load_string($decompressed_content);
	if (!$xml) {
		echo "Gặp lỗi khi đọc tệp Settings.cfg!";
		exit();
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$xml['UseStdIn'] = $_POST['UseStdIn'];
	$xml['UseStdOut'] = $_POST['UseStdOut'];
	$xml['Mark'] = $_POST['Mark'];
	$xml['TimeLimit'] = $_POST['TimeLimit'];
	$xml['MemoryLimit'] = $_POST['MemoryLimit'];

	foreach ($_POST['testcases'] as $index => $testcase) {
		$xml->TestCase[$index]['Mark'] = $testcase['Mark'];
		$xml->TestCase[$index]['TimeLimit'] = $testcase['TimeLimit'];
		$xml->TestCase[$index]['MemoryLimit'] = $testcase['MemoryLimit'];
	}
	$stdin = $_POST['UseStdIn'];
	$stdout = $_POST['UseStdOut'];
	if ($_POST['UseStdIn'] == 'true') {
		$xml['UseStdIn'] = 1;
		$stdin = '1';
	} else {
		$xml['UseStdIn'] = 0;
		$stdin = '0';
	} if ($_POST['UseStdOut'] == 'true') {
		$xml['UseStdOut'] = 1;
		$stdout = '1';
	} else {
		$xml['UseStdOut'] = 0;
		$stdout = '0';
	}

	$updated_content = $xml->asXML();
	$updated_content = preg_replace('/<\?xml.*?\?>/', '', $updated_content);
	$compressed_content = gzcompress($updated_content);
	file_put_contents($cfg_file, $compressed_content);
	$stmt = $pdo->prepare("UPDATE problems SET time_limit = :time_limit, memory_limit = :memory_limit, stdin = :stdin, stdout = :stdout WHERE name = :name");
	$stmt->execute([
		':time_limit' => $_POST['TimeLimit'],
		':memory_limit' => $_POST['MemoryLimit'],
		':stdin' => $stdin,
		':stdout' => $stdout,
		':name' => $problem_name
	]);
	echo "<script>alert('Cập nhật cài đặt testcase thành công!'); window.location.href = 'testcase_settings.php?name=$problem_name';</script>";
	exit();
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
	<title>Chỉnh Sửa Settings.cfg</title>
	<link href="assets/css/prism.css" rel="stylesheet">
	<script src="assets/js/prism.js"></script>
	<script src="assets/js/jquery-3.6.0.min.js"></script>
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap-icons.css">
	<link rel="stylesheet" href="assets/css/styles.css">
	<script src="assets/js/bootstrap.bundle.min.js"></script>
	<script src="assets/js/testcase_settings.js"></script>
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
		<h1>Sửa cài đặt testcase cho đề bài: <?= htmlspecialchars($problem_name) ?></h1>
		<hr>
		<small style="font-style:italic;">(*) Các phần được đánh dấu sao sẽ được cập nhật vào cơ sở dữ liệu.</small><br />
		<form method="POST" onsubmit="return confirmSave(event)">
			<h3>Cài đặt nhập/xuất:</h3>
			<small style="font-style:italic;">Cài đặt file đầu vào, đầu ra cần phải được cập nhật trong phần mềm Themis.</small>

			<div class="mb-3">
				<div class="form-check">
					<label for="UseStdIn" class="form-check-label"><i>(*)</i> Sử dụng đầu vào chuẩn (stdin)</label>
					<input type="checkbox" name="UseStdIn" id="UseStdIn" class="form-check-input" value="true" <?= $xml['UseStdIn'] == 'true' ? 'checked' : '' ?>>
				</div>
			</div>
			<div class="mb-3">
				<div class="form-check">
					<label for="UseStdOut" class="form-check-label"><i>(*)</i> Sử dụng đầu ra chuẩn (stdout)</label>
					<input type="checkbox" name="UseStdOut" id="UseStdOut" class="form-check-input" value="true" <?= $xml['UseStdOut'] == 'true' ? 'checked' : '' ?>>
				</div>
			</div>
			<h3>Cài đặt chung cho tất cả các testcase:</h3>
			<div class="mb-3">
				<label for="Mark" class="form-label">Điểm mỗi test:</label>
				<input type="text" name="Mark" id="Mark" class="form-control" value="<?= htmlspecialchars($xml['Mark']) ?>">
			</div>
			<div class="mb-3">
				<label for="TimeLimit" class="form-label"><i>(*)</i> Giới hạn thời gian mỗi test (giây):</label>
				<input type="text" name="TimeLimit" id="TimeLimit" class="form-control" value="<?= htmlspecialchars($xml['TimeLimit']) ?>">
			</div>
			<div class="mb-3">
				<label for="MemoryLimit" class="form-label"><i>(*)</i> Giới hạn bộ nhớ mỗi test (MiB):</label>
				<input type="text" name="MemoryLimit" id="MemoryLimit" class="form-control" value="<?= htmlspecialchars($xml['MemoryLimit']) ?>">
			</div>
			<h3>Cài đặt cho từng testcase:</h3>
			<table class="table table-dark table-bordered">
				<thead>
					<tr>
						<th>Tên testcase</th>
						<th>Điểm</th>
						<th>Giới hạn thời gian (s)</th>
						<th>Giới hạn bộ nhớ (MiB)</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($xml->TestCase as $index => $testcase): ?>
						<tr>
							<td>
								<?= htmlspecialchars($testcase['Name']) ?>
							</td>
							<td>
								<input type="text" name="testcases[<?= $index ?>][Mark]" class="form-control" value="<?= htmlspecialchars($testcase['Mark']) ?>">
							</td>
							<td>
								<input type="text" name="testcases[<?= $index ?>][TimeLimit]" class="form-control" value="<?= htmlspecialchars($testcase['TimeLimit']) ?>">
							</td>
							<td>
								<input type="text" name="testcases[<?= $index ?>][MemoryLimit]" class="form-control" value="<?= htmlspecialchars($testcase['MemoryLimit']) ?>">
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<button type="submit" class="btn btn-primary">Lưu thay đổi</button>
			<a href="problems.php" class="btn btn-secondary">Hủy</a>
		</form>
	</div>
</body>
<footer>
	<div class="text-center mt-3">
		<p>from <b>DuongNhanAC</b> × <b>ayor</b> with love <i class="bi bi-hearts"></i><br />
		<a href="https://github.com/duongnotnhan/themis-onlan-judge"><i class="bi bi-github"></i> Source Code</a></p>
	</div>
</footer>
</html>