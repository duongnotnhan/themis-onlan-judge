<?php
session_start();
require 'config.php';

if ($_SESSION['role'] !== 'admin') {
    exit("Bạn không có quyền thực hiện thao tác này!");
}

if (isset($_POST['id'])) {
    $rejudge_id = $_POST['id'];
    $stmt = $pdo->prepare("UPDATE submissions SET score = 0, status = 'PENDING', backup_logs = '' WHERE id = ?");
    if ($stmt->execute([$rejudge_id])) {
        echo "Đã thành công gửi lệnh chấm lại bài nộp.";
    } else {
        echo "Lỗi khi gửi lệnh chấm lại bài nộp!";
    }
}
?>