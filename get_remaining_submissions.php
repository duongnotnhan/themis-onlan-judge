<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$problem_name = $_GET['problem_name'] ?? '';
if (empty($problem_name)) {
    echo json_encode(["error" => "Problem name is required"]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, submissions_limit FROM problems WHERE name = ?");
$stmt->execute([$problem_name]);
$problem = $stmt->fetch();

if (!$problem) {
    echo json_encode(["error" => "Problem not found"]);
    exit;
}

if ($problem['submissions_limit'] == -1) {
    echo json_encode(["remaining" => "unlimited"]);
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM submissions WHERE user_id = ? AND problem_id = ?");
$stmt->execute([$_SESSION['user_id'], $problem['id']]);
$submissions_count = $stmt->fetchColumn();

$remaining = max(0, $problem['submissions_limit'] - $submissions_count);
echo json_encode(["remaining" => $remaining]);