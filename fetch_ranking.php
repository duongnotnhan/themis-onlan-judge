<?php
require 'config.php';

$rankings = $pdo->query("
    SELECT u.full_name, u.username, u.class, COALESCE(SUM(max_score), 0) AS total_score
    FROM users u
    LEFT JOIN (
        SELECT user_id, problem_id, MAX(score) AS max_score
        FROM submissions
        GROUP BY user_id, problem_id
    ) s ON u.id = s.user_id
    GROUP BY u.id, u.full_name, u.username, u.class
    ORDER BY total_score DESC
")->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rankings);
?>
