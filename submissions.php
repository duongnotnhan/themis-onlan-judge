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
    <title>Quản Lý Bài Nộp</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link href="assets/css/prism.css" rel="stylesheet">
	<script src="assets/js/prism.js"></script>
    <script src="assets/js/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-dark text-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">OnLAN Judge</a>
            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="navbar-text me-3">Xin chào, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                    <a href="submissions.php" class="btn btn-outline-light me-2 active">Lịch Sử Nộp Bài</a>
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

    <div class="container mt-5 pt-4">
        <h3 class="text-center mt-4">📜 Tất Cả Bài Nộp</h3>
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
        });
    </script>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
<footer class="footer">
    <hr>
    <div class="text-center mt-3">
        <p>Một cái footer bị lỗi...</p>
    </div>
</footer>
</html>
