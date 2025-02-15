<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$error = "";

if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM problems WHERE id=?");
    $stmt->execute([$id]);
    header("Location: problems.php");
    exit();
}
if (isset($_GET['logout'])) {
	session_destroy();
	header("Location: auth.php");
	exit;
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
</head>
<body class="bg-dark text-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
	<div class="container">
		<a class="navbar-brand" href="index.php">OnLAN Judge</a>
		<div class="d-flex align-items-center">
			<?php if (isset($_SESSION['user_id'])): ?>
				<span class="navbar-text me-3">Xin chào, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
				<?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="submissions.php" class="btn btn-outline-light me-2">Lịch Sử Nộp Bài</a>
					<a href="problems.php" class="btn btn-outline-light me-2 active">Danh Sách Đề Bài</a>
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
    <div class="container mt-5">
        <h1>Danh Sách Đề Bài</h1>
        <hr>
        <a href="create_problem.php" class="btn btn-success float-end">Tạo Đề Bài</a>
        <table class="table table-dark table-striped mt-3">
            <thead>
                <tr>
                    <th>Tên Đề Bài</th>
                    <th>Tổng Điểm</th>
                    <th>Time-limit (s)</th>
                    <th>Memory-limit (MiB)</th>
                    <th>Sửa Đề Bài</th>
                    <th>Xóa Bài</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($problems as $problem): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($problem['name']); ?></td>
                        <td><?php echo htmlspecialchars($problem['total_score']); ?></td>
                        <td><?php echo htmlspecialchars($problem['time_limit']); ?></td>
                        <td><?php echo htmlspecialchars($problem['memory_limit']); ?></td>
                        <td>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <a href="edit_problem.php?id=<?php echo $problem['id']; ?>" class="btn btn-warning">Sửa</a>
                            <?php else: ?>
                                <span class="text-muted"><small>Đừng Nhìn Tôi</small></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <form method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa đề bài này?');">
                                    <input type="hidden" name="delete_id" value="<?php echo $problem['id']; ?>">
                                    <button type="submit" class="btn btn-danger">Xóa</button>
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
</body>
<footer class="footer">
    <div class="text-center mt-3">
        <p>Một cái footer bị lỗi...</p>
    </div>
</footer>
</html>
