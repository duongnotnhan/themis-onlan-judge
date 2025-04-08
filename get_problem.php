<?php
require 'config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['name'])) {
	echo json_encode(["error" => "Thiếu tham số bài tập"], JSON_UNESCAPED_UNICODE);
	exit;
}

$problem_name = $_GET['name'];

$stmt = $pdo->prepare("SELECT name, description, total_score, time_limit, memory_limit, submissions_limit FROM problems WHERE name = :name");
$stmt->execute(['name' => $problem_name]);
$problem = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$problem) {
	echo json_encode(["error" => "Không tìm thấy bài tập", "debug" => $_GET['name']], JSON_UNESCAPED_UNICODE);
	exit;
}

echo json_encode($problem, JSON_UNESCAPED_UNICODE);
exit;
?>
