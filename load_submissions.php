<?php
session_start();
require 'config.php';

if (!isset($_SESSION['role'])) {
	header("Location: index.php");
	exit();
}
if ($_SESSION['role'] === 'admin') {
	$stmt = $pdo->query("
		SELECT s.id, s.score, s.status, s.language, p.name AS problem_name, 
			   p.total_score, u.username, s.submitted_at
		FROM submissions s
		JOIN users u ON s.user_id = u.id
		JOIN problems p ON s.problem_id = p.id
		ORDER BY s.submitted_at DESC
	");
} else {
	$stmt = $pdo->query("
		SELECT s.id, s.score, s.status, s.language, p.name AS problem_name, 
			   p.total_score, u.username, s.submitted_at
		FROM submissions s
		JOIN users u ON s.user_id = u.id
		JOIN problems p ON s.problem_id = p.id AND p.order_id >= 1
		ORDER BY s.submitted_at DESC
	");
}

$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($submissions as $submission) {
	$submittedTime = date("H:i d/m/Y", strtotime($submission['submitted_at']));
	if ($submission['status'] === 'PENDING' || $submission['status'] === 'NOT SUBMITTED') {
		$scoreDisplay = "<div class=\"spinning\"><i class=\"bi bi-arrow-clockwise\"></i></div>";
	} else {
		$scoreDisplay = "{$submission['score']}/{$submission['total_score']}";
	}

	echo "
		<tr class='text-center align-middle'>
			<td>
				<span class='badge {$submission['status']} p-2'>{$submission['status']}</span><br>
				<small class='badge'>{$submission['language']}</small>
			</td>
			<td><strong>$scoreDisplay</strong></td>
			<td>
				<strong>{$submission['problem_name']}</strong><br>
				<small style='color:white;'>Bởi <b>{$submission['username']}</b> lúc $submittedTime</small>
			</td>
			<td>
				<button class='btn btn-primary btn-sm viewSubmission' data-id='{$submission['id']}'>Xem Chi Tiết</button>
				<button class='btn btn-warning btn-sm rejudgeSubmission' data-id='{$submission['id']}'>Chấm Lại</button>
				<button class='btn btn-danger btn-sm deleteSubmission' data-id='{$submission['id']}'>Xóa</button>
			</td>
		</tr>
	";
}
?>
