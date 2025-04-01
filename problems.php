<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    $stmt = $pdo->prepare("DELETE FROM submissions WHERE problem_id=?");
    $stmt->execute([$id]);

    $stmt = $pdo->prepare("DELETE FROM problems WHERE id=?");
    $stmt->execute([$id]);

    header("Location: problems.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: auth.php");
    exit();
}

$stmt = $pdo->query("SELECT id, name, total_score, time_limit, memory_limit FROM problems");
$problems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Đề Bài</title>
    <link href="assets/css/prism.css" rel="stylesheet">
    <script src="assets/js/prism.js"></script>
    <script src="assets/js/jquery-3.6.0.min.js"></script>
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
    <h2>Danh Sách Đề Bài</h2>
    <hr>
    <a href="create_problem.php" class="btn btn-success float-end"><i class="bi bi-plus-circle"></i> Tạo Đề Bài</a>
    <table class="table table-dark table-striped table-hover mt-3 table-bordered">
        <thead class="table-light text-dark text-center">
            <tr>
            <th style="width: 20%;">Tên bài</th>
			<th style="width: 5%;">Điểm</th>
			<th style="width: 20%;">Time-limit (s)</th>
			<th style="width: 20%;">Memory-limit (MiB)</th>
            <th>Sửa Đề Bài</th>
            <th>Xóa Bài</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($problems as $problem): ?>
                <tr>
                    <td><?php echo htmlspecialchars($problem['name']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($problem['total_score']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($problem['time_limit']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($problem['memory_limit']); ?></td>
                    <td class="text-center">
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <a href="edit_problem.php?id=<?php echo $problem['id']; ?>" class="btn btn-warning"><i class="bi bi-pencil-square"></i> Sửa</a>
                        <?php else: ?>
                            <span class="text-muted"><small>Đừng Nhìn Tôi</small></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <form method="POST" onsubmit="return confirmDelete(<?php echo $problem['id']; ?>, '<?php echo htmlspecialchars($problem['name'], ENT_QUOTES, 'UTF-8'); ?>');">
                                <input type="hidden" name="delete_id" value="<?php echo $problem['id']; ?>">
                                <button type="submit" class="btn btn-danger"><i class="bi bi-trash3"></i> Xóa</button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted"><small>Đừng Nhìn Tôi</small></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function confirmDelete(problemId, problemName) {
    return confirm(`CẢNH BÁO!\n\nBạn sắp xóa đề bài: "${problemName}".\nĐiều này sẽ xóa toàn bộ bài nộp liên quan đến đề bài này!\n\nBạn có chắc chắn muốn tiếp tục?`);
}
</script>

</body>
<footer>
    <div class="text-center mt-3">
        <p>DuongNhanAC × ayor</p>
    </div>
</footer>
</html>
