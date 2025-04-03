<?php
session_start();
require 'config.php';
date_default_timezone_set("Asia/Ho_Chi_Minh");

$problems = $pdo->query("SELECT id, name FROM problems WHERE order_id >= 1 ORDER BY order_id ASC")->fetchAll(PDO::FETCH_ASSOC);
$problem_ids = array_column($problems, 'id');
$problem_ids_str = implode(',', $problem_ids);

$rankings = $pdo->query("
    WITH ranked_users AS (
        SELECT u.id, u.full_name, u.username, u.class, u.school, 
               COALESCE(SUM(CASE WHEN s.problem_id IN ($problem_ids_str) THEN s.max_score ELSE 0 END), 0) AS total_score,
               COALESCE(SUM(CASE WHEN s.problem_id IN ($problem_ids_str) THEN s.time_diff ELSE 0 END), 0) AS total_time
        FROM users u
        LEFT JOIN (
            SELECT sub.user_id, sub.problem_id, sub.score AS max_score, 
                   TIMESTAMPDIFF(SECOND, cs.start_time, sub.submitted_at) AS time_diff
            FROM (
                SELECT s.user_id, s.problem_id, s.score, s.submitted_at, 
                       RANK() OVER (PARTITION BY s.user_id, s.problem_id ORDER BY s.score DESC, s.submitted_at ASC) AS rnk
                FROM submissions s
                WHERE s.problem_id IN ($problem_ids_str)
            ) sub
            JOIN contest_settings cs ON cs.id = 1
            WHERE sub.rnk = 1
        ) s ON u.id = s.user_id
        GROUP BY u.id, u.full_name, u.username, u.class, u.school
    )
    SELECT full_name, username, class, school, total_score, total_time,
           RANK() OVER (ORDER BY total_score DESC, total_time ASC) AS rank, id AS user_id
    FROM ranked_users
")->fetchAll(PDO::FETCH_ASSOC);

$user_scores = [];
foreach ($rankings as $user) {
    $user_id = $user['user_id'];
    $scores = $pdo->query("
        SELECT s.problem_id, s.score, TIMESTAMPDIFF(SECOND, cs.start_time, s.submitted_at) AS time_diff
        FROM submissions s
        JOIN contest_settings cs ON cs.id = 1
        JOIN problems p ON s.problem_id = p.id AND p.order_id >= 1
        WHERE s.user_id = $user_id
        AND (s.score, s.submitted_at) = (
            SELECT sub2.score, sub2.submitted_at
            FROM submissions AS sub2
            WHERE sub2.user_id = s.user_id
            AND sub2.problem_id = s.problem_id
            ORDER BY sub2.score DESC, sub2.submitted_at ASC
            LIMIT 1
        )
    ")->fetchAll(PDO::FETCH_ASSOC);

    $user_scores[$user_id] = [];
    foreach ($scores as $score) {
        $user_scores[$user_id][$score['problem_id']] = [
            'score' => $score['score'],
            'time' => $score['time_diff']
        ];
    }
}

foreach ($rankings as $user):
?>
    <tr>
        <td><strong><?= $user['rank'] ?></strong></td>
        <td>
            <strong class="text-info"><?= htmlspecialchars($user['full_name']) ?></strong><br>
            <small><?= htmlspecialchars($user['username']) ?></small><br>
            <small><strong><?= htmlspecialchars($user['class']) ?></strong> - <strong><?= htmlspecialchars($user['school']) ?></strong></small>
        </td>
        <?php foreach ($problems as $problem): 
            $score_data = $user_scores[$user['user_id']][$problem['id']] ?? ['score' => 0, 'time' => null];
        ?>
            <td>
                <strong class="score"><?= $score_data['score'] ?></strong>
                <br>
                <small class="time">
                    <?= $score_data['time'] !== null ? gmdate("H:i:s", $score_data['time']) : '-' ?>
                </small>
            </td>
        <?php endforeach; ?>
        <td>
            <strong class="score"><?= $user['total_score'] ?></strong>
            <br>
            <small class="time"><?= gmdate("H:i:s", $user['total_time']) ?></small>
        </td>
    </tr>
<?php endforeach; ?>
