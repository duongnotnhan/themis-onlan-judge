<?php
session_start();
require 'config.php';

if ($_SESSION['role'] !== 'admin') {
	exit("Bạn không có quyền thực hiện thao tác này!");
}

if (isset($_POST['id'])) {
	$id = $_POST['id'];
	$stmt = $pdo->prepare("DELETE FROM submissions WHERE id = ?");
	if ($stmt->execute([$id])) {
		echo "Xóa bài nộp thành công!";
	} else {
		echo "Lỗi khi xóa bài nộp!";
	}
}
?>
