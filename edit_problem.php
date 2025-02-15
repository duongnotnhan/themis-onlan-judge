<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: problems.php");
    exit();
}

$id = $_GET['id'];
$error = "";

$stmt = $pdo->prepare("SELECT * FROM problems WHERE id = ?");
$stmt->execute([$id]);
$problem = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$problem) {
    header("Location: problems.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $total_score = trim($_POST['total_score']);
    $time_limit = trim($_POST['time_limit']);
    $memory_limit = trim($_POST['memory_limit']);
    $description = trim($_POST['description']);

    if (empty($name) || empty($total_score) || empty($time_limit) || empty($memory_limit) || empty($description)) {
        $error = "Vui lòng điền đầy đủ thông tin!";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM problems WHERE name = ? AND id != ?");
        $stmt->execute([$name, $id]);
        if ($stmt->fetch()) {
            $error = "Tên đề bài đã tồn tại!";
        } else {
            $stmt = $pdo->prepare("UPDATE problems SET name=?, total_score=?, time_limit=?, memory_limit=?, description=? WHERE id=?");
            $stmt->execute([$name, $total_score, $time_limit, $memory_limit, $description, $id]);
            header("Location: problems.php");
            exit();
        }
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
    <title>Chỉnh Sửa Đề Bài</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <link href="assets/css/prism.css" rel="stylesheet">
    <script src="assets/js/prism.js"></script>

    <script src="assets/js/markdown-it.min.js"></script>

    <link rel="stylesheet" href="assets/css/katex.min.css">
    <script src="assets/js/katex.min.js"></script>
    <script src="assets/js/auto-render.min.js"></script>
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

<div class="container mt-5">
    <h1>Chỉnh Sửa Đề Bài</h1>
    <?php if ($error): ?>
        <div class="alert alert-danger"> <?php echo $error; ?> </div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Tên Đề Bài</label>
            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($problem['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tổng Điểm</label>
            <input type="number" class="form-control" name="total_score" value="<?php echo htmlspecialchars($problem['total_score']); ?>" step="any" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Time Limit (s)</label>
            <input type="number" class="form-control" name="time_limit" value="<?php echo htmlspecialchars($problem['time_limit']); ?>" step="any" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Memory Limit (MiB)</label>
            <input type="number" class="form-control" name="memory_limit" value="<?php echo htmlspecialchars($problem['memory_limit']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mô Tả</label>
            <textarea id="markdown-input" class="form-control language-markdown" name="description" rows="6" required><?php echo htmlspecialchars($problem['description']); ?></textarea>
            <pre class="bg-dark text-light p-3 rounded"><code id="editor-preview" class="language-markdown"></code></pre>
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

        document.getElementById('editor-preview').textContent = inputText;

        renderMarkdown();
        Prism.highlightAll();
    });

    renderMarkdown();
    Prism.highlightAll();
</script>
</body>
<footer class="footer">
    <div class="text-center mt-3">
        <p>Một cái footer bị lỗi...</p>
    </div>
</footer>
</html>
