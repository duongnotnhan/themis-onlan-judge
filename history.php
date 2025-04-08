<?php
session_start();
require 'config.php';

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION['user_id'])) {
	echo json_encode(["error" => "Bạn cần đăng nhập để xem lịch sử"]);
	exit;
}

$user_id = $_SESSION['user_id'];
$problem_name = $_GET['problem_name'] ?? '';

if (!$problem_name) {
	echo json_encode(["error" => "Thiếu tên bài tập"]);
	exit;
}

$stmt = $pdo->prepare("
	SELECT s.submitted_at, s.score, s.status, s.language, 
		   LEFT(s.backup_code, 1080) AS backup_code, 
		   LEFT(s.backup_logs, 1080) AS backup_logs
	FROM submissions s
	JOIN problems p ON s.problem_id = p.id
	WHERE s.user_id = ? AND LOWER(p.name) = LOWER(?)
	ORDER BY s.submitted_at DESC
");
$stmt->execute([$user_id, $problem_name]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($history);
exit;
?>
