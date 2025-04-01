<?php
session_start();
require 'config.php';

date_default_timezone_set("Asia/Ho_Chi_Minh");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Bạn cần đăng nhập để nộp bài"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$problem_name = $_POST['problem_name'] ?? null;
$submit_type = $_POST['submit_type'] ?? null;

if (!$problem_name || !$submit_type) {
    echo json_encode(["error" => "Thiếu thông tin bài nộp"]);
    exit;
}

$problem_name = mb_strtolower($problem_name, 'UTF-8');

$contest = $pdo->query("SELECT submission_path, start_time, end_time FROM contest_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
if (!$contest) {
    echo json_encode(["error" => "Không tìm thấy thông tin cuộc thi"]);
    exit;
}

$start_time = strtotime($contest['start_time']);
$end_time = strtotime($contest['end_time']);
$current_time = time();
$current_time_sql = date('Y-m-d H:i:s', $current_time);

if ($current_time < $start_time) {
    echo json_encode(["error" => "Kỳ thi chưa bắt đầu, vui lòng quay lại sau!"]);
    exit;
}

if ($current_time > $end_time) {
    echo json_encode(["error" => "Kỳ thi đã kết thúc, không thể nộp bài nữa!"]);
    exit;
}

$submission_path = rtrim($contest['submission_path'], '/') . '/';
if (!is_dir($submission_path)) {
    echo json_encode(["error" => "Thư mục nộp bài không tồn tại"]);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM problems WHERE LOWER(name) = LOWER(?)");
$stmt->execute([$problem_name]);
$problem = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$problem) {
    echo json_encode(["error" => "Bài tập không hợp lệ"]);
    exit;
}

$problem_id = $problem['id'];
$language = "";
$backup_code = "";

if ($submit_type === "file") {
    if (!isset($_FILES["code_file"])) {
        echo json_encode(["error" => "Vui lòng chọn file để nộp"]);
        exit;
    }

    $file_ext = pathinfo($_FILES["code_file"]["name"], PATHINFO_EXTENSION);
    $supported_languages = ['c' => 'C', 'cpp' => 'CPP', 'py' => 'PY', 'pas' => 'PAS'];

    if (!isset($supported_languages[$file_ext])) {
        echo json_encode(["error" => "File không hợp lệ hoặc ngôn ngữ không được hỗ trợ."]);
        exit;
    }

    $language = $supported_languages[$file_ext];
    $backup_code = file_get_contents($_FILES["code_file"]["tmp_name"]);
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM submissions WHERE user_id = ? AND problem_id = ? AND status = 'PENDING'");
$stmt->execute([$user_id, $problem_id]);
$pending_count = $stmt->fetchColumn();

$status = ($pending_count == 0) ? "PENDING" : "NOT SUBMITTED";

$stmt = $pdo->prepare("INSERT INTO submissions (user_id, problem_id, submitted_at, status, backup_code, language) 
                       VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$user_id, $problem_id, $current_time_sql, $status, $backup_code, $language]);

echo json_encode(["success" => "Nộp bài thành công!", "status" => $status]);
exit;
