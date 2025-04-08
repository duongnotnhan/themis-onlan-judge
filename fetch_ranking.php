<?php
session_start();
require 'config.php';
date_default_timezone_set("Asia/Ho_Chi_Minh");

$rankings = $pdo->query("
	WITH ranked_users AS (
		SELECT u.full_name, u.username, u.class, u.school, 
			   COALESCE(SUM(s.max_score), 0) AS total_score,
			   COALESCE(SUM(s.time_diff), 0) AS total_time
		FROM users u
		LEFT JOIN (
			SELECT sub.user_id, sub.problem_id, sub.score AS max_score, 
				   TIMESTAMPDIFF(SECOND, cs.start_time, sub.submitted_at) AS time_diff
			FROM (
				SELECT s.user_id, s.problem_id, s.score, s.submitted_at, 
					   RANK() OVER (PARTITION BY s.user_id, s.problem_id ORDER BY s.score DESC, s.submitted_at ASC) AS rnk
				FROM submissions s
				JOIN problems p ON s.problem_id = p.id AND p.order_id >= 1
			) sub
			JOIN contest_settings cs ON cs.id = 1
			WHERE sub.rnk = 1
		) s ON u.id = s.user_id
		GROUP BY u.id, u.full_name, u.username, u.class, u.school
	)
	SELECT full_name, username, class, school, total_score, total_time,
		   RANK() OVER (ORDER BY total_score DESC, total_time ASC) AS rank
	FROM ranked_users
")->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rankings);
?>
