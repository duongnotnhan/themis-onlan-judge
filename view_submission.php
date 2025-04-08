<?php
session_start();
require 'config.php';

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
	echo json_encode(["error" => "Bạn không có quyền truy cập!"], JSON_UNESCAPED_UNICODE);
	exit;
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['id']) || empty($_GET['id'])) {
	echo json_encode(["error" => "Thiếu ID bài nộp!"], JSON_UNESCAPED_UNICODE);
	exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("
	SELECT s.score, s.status, s.language, s.backup_code, s.backup_logs, 
		   p.name AS problem_name, p.total_score, u.username
	FROM submissions s
	JOIN users u ON s.user_id = u.id
	JOIN problems p ON s.problem_id = p.id
	WHERE s.id = ?
");
$stmt->execute([$id]);
$submission = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$submission) {
	echo json_encode(["error" => "Không tìm thấy bài nộp!"], JSON_UNESCAPED_UNICODE);
	exit;
}

echo json_encode([
	"problem_name" => $submission['problem_name'],
	"username" => $submission['username'],
	"score" => "{$submission['score']}/{$submission['total_score']}",
	"status" => $submission['status'],
	"language" => $submission['language'],
	"code" => $submission['backup_code'],
	"logs" => $submission['backup_logs']
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
?>
