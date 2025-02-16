<?php
session_start();
require 'config.php';

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

$start_time = strtotime($contest['start_time'] . ' UTC');
$end_time = strtotime($contest['end_time'] . ' UTC');
$current_time = time() + 7 * 3600;

if ($current_time < $start_time) {
    echo json_encode(["error" => "Kỳ thi chưa bắt đầu, vui lòng quay lại sau!"]);
    exit;
}

if ($current_time > $end_time) {
    echo json_encode(["error" => "Kỳ thi đã kết thúc, không thể nộp bài nữa!"]);
    exit;
}

$submission_path = rtrim($contest['submission_path'], '/') . '/';
$logs_path = $submission_path . "Logs/";

if (!is_dir($submission_path) || !is_dir($logs_path)) {
    echo json_encode(["error" => "Thư mục nộp bài hoặc Logs không tồn tại"]);
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
$filename = "[{$username}][{$problem_name}]";

$backup_code = "";
$language = "";

if ($submit_type === "file") {
    if (!isset($_FILES["code_file"])) {
        echo json_encode(["error" => "Vui lòng chọn file để nộp"]);
        exit;
    }

    $file_ext = pathinfo($_FILES["code_file"]["name"], PATHINFO_EXTENSION);
    
    $supported_languages = ['c' => 'C', 'cpp' => 'CPP', 'py' => 'PY', 'pas' => 'PAS'];
    if (!mb_strtolower($supported_languages[$file_ext])) {
        echo json_encode(["error" => "File không hợp lệ hoặc ngôn ngữ không được hỗ trợ."]);
        exit;
    }

    $language = $supported_languages[$file_ext];
    $target_file = $submission_path . $filename . "." . $file_ext;

    $backup_code = file_get_contents($_FILES["code_file"]["tmp_name"]);

    if (!move_uploaded_file($_FILES["code_file"]["tmp_name"], $target_file)) {
        echo json_encode(["error" => "Lỗi khi tải file lên"]);
        exit;
    }

    foreach (glob($logs_path . "*") as $log_file_) {
        if (stripos($log_file_, "[{$username}][{$problem_name}]") !== false) {
            unlink($log_file_);
            break;
        }
    }
}

set_time_limit(0);

$log_file = "";

while (true) {
    foreach (glob($logs_path . "*") as $file) {
        if (stripos($file, "[{$username}][{$problem_name}]") !== false) {
            $log_file = $file;
            break 2;
        }
    }
    usleep(1000000);
}

if (!$log_file) {
    echo json_encode(["error" => "\"Ultra Time Limit Exceeded\" hoặc Máy Chấm lỗi!"]);
    exit;
}

$backup_logs = file_get_contents($log_file);
$lines = explode("\n", $backup_logs);
$total_score = 0;
$status = "WA";
$found_testcase = false;

$pattern = '/^' . preg_quote(mb_strtolower($username, 'UTF-8'), '/') . '‣' . preg_quote(mb_strtolower($problem_name, 'UTF-8'), '/') . '‣.*: ([0-9.]+)/i';

$stmt = $pdo->prepare("SELECT total_score FROM problems WHERE id = ?");
$stmt->execute([$problem_id]);
$problem_data = $stmt->fetch(PDO::FETCH_ASSOC);
$max_score = $problem_data['total_score'];

foreach ($lines as $line) {
    $line_lower = mb_strtolower($line, 'UTF-8');

    if (preg_match($pattern, $line_lower, $matches)) {
        $total_score += floatval($matches[1]);
        $found_testcase = true;
    }

    if (strpos($line_lower, "quá bộ nhớ") !== false) {
        $status = "MLE"; 
    } elseif (strpos($line_lower, "chạy sinh lỗi") !== false) {
        $status = "ER/IR";
    } elseif (strpos($line_lower, "quá thời gian") !== false) {
        $status = "TLE"; 
    } elseif (strpos($line_lower, "không thấy file") !== false) {
        $status = "WA";
    } elseif (strpos($line_lower, "khác đáp án") !== false) {
        $status = "WA";
    }
}

if (!$found_testcase) {
    $total_score = 0;
    $status = "CE";
}

if ($total_score >= $max_score) {
    $total_score = $max_score;
    $status = "AC";
}

$stmt = $pdo->prepare("INSERT INTO submissions (user_id, problem_id, submitted_at, score, status, backup_code, backup_logs, language) 
                       VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)");
$stmt->execute([$user_id, $problem_id, $total_score, $status, $backup_code, $backup_logs, $language]);

if (file_exists($log_file)) {
    unlink($log_file);
}

echo json_encode(["success" => "Chấm thành công!", "score" => $total_score, "status" => $status, "language" => $language]);
exit;
