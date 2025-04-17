<?php
require 'config.php';

date_default_timezone_set("Asia/Ho_Chi_Minh");

$contest = $pdo->query("SELECT submission_path FROM contest_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
if (!$contest) {
	die("Không tìm thấy thông tin cuộc thi!\n");
}
$stmt = $pdo->query("SELECT testcase_path FROM contest_settings LIMIT 1");
$contest_settings = $stmt->fetch();

$submission_path = rtrim($contest['submission_path'], '/') . '/';
$logs_path = $submission_path . "Logs/";

if (!is_dir($logs_path)) {
	die("Thư mục nhật ký không tồn tại!\n");
}

echo "Bắt đầu theo dõi bài nộp...\n";

while (true) {
	$stmt = $pdo->query("SELECT id, user_id, problem_id, backup_code, language FROM submissions 
						 WHERE status = 'PENDING' ORDER BY submitted_at ASC LIMIT 1");
	$submission = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!$submission) {
		usleep(1000000);
		continue;
	}

	$submission_id = $submission['id'];
	$user_id = $submission['user_id'];
	$problem_id = $submission['problem_id'];
	$backup_code = $submission['backup_code'];
	$language = strtolower($submission['language']);

	$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
	$stmt->execute([$user_id]);
	$user = $stmt->fetch(PDO::FETCH_ASSOC);

	$stmt = $pdo->prepare("SELECT name FROM problems WHERE id = ?");
	$stmt->execute([$problem_id]);
	$problem = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!$user || !$problem) {
		usleep(1000000);
		continue;
	}

	$username = $user['username'];
	$problem_name = $problem['name'];

	$stmt = $pdo->prepare("SELECT COUNT(*) FROM submissions WHERE user_id = ? AND problem_id = ? 
						   AND status = 'PENDING' AND id < ?");
	$stmt->execute([$user_id, $problem_id, $submission_id]);
	$pending_before = $stmt->fetchColumn();

	if ($pending_before == 0) {
		$user_submission_folder = $submission_path;
		if (!is_dir($user_submission_folder)) {
			mkdir($user_submission_folder, 0777, true);
		}

		$filename = "{$user_submission_folder}[{$username}][{$problem_name}].{$language}";

		file_put_contents($filename, $backup_code);
		echo "Đã lưu mã nguồn bài nộp của {$username} - {$problem_name} vào tệp: {$filename}\n";

		foreach (glob($logs_path . "*") as $log_file) {
			if (stripos($log_file, "[{$username}][{$problem_name}]") !== false) {
				unlink($log_file);
				echo "Đã xóa nhật ký cũ: {$log_file}\n";
			}
		}
	}

	echo "Chờ tệp nhật ký chấm cho bài nộp ID: $submission_id...\n";
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
		echo "Không nhận được tệp nhật ký chấm bài!\n";
		continue;
	}

	$testcase_path = rtrim($contest_settings['testcase_path'], '/') . "/$problem_name/";
	$settings_path = $testcase_path . "Settings.cfg";
	if (!file_exists($settings_path)) {
		echo "Không tìm thấy tệp Settings.cfg!\n";
		continue;
	}

	$settings_content = file_get_contents($settings_path);
	$decompressed_settings = gzuncompress($settings_content);
	$settings_xml = simplexml_load_string($decompressed_settings);

	if (!$settings_xml) {
		echo "Không thể đọc tệp Settings.cfg!\n";
		continue;
	}

	$score_per_test = floatval($settings_xml['Mark']);
	$total_score_from_settings = 0;
	$testcase_scores = [];

	foreach ($settings_xml->TestCase as $testcase) {
		$testcase_name = (string) $testcase['Name'];
		$testcase_mark = floatval($testcase['Mark']);

		if ($testcase_mark == -1) {
			$testcase_mark = $score_per_test;
		}

		$testcase_scores[$testcase_name] = $testcase_mark;
		$total_score_from_settings += $testcase_mark;
	}

	$backup_logs = file_get_contents($log_file);
	$lines = explode("\n", $backup_logs);
	$total_score_earned = 0;
	$status = "WA";
	$found_testcase = false;

	$pattern = '/^' . preg_quote(mb_strtolower($username, 'UTF-8'), '/') . '‣' . preg_quote(mb_strtolower($problem_name, 'UTF-8'), '/') . '‣(.*): ([0-9.]+)/i';

	foreach ($lines as $line) {
		$line_lower = mb_strtolower($line, 'UTF-8');

		if (preg_match($pattern, $line_lower, $matches)) {
			$testcase_name = trim($matches[1]);
			$testcase_score = floatval($matches[2]);

			if (isset($testcase_scores[$testcase_name]) && $testcase_score > 0) {
				$total_score_earned += $testcase_scores[$testcase_name];
			}

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
		$total_score_earned = 0;
		$status = "CE/IE";
	}

	$stmt = $pdo->prepare("SELECT total_score FROM problems WHERE id = ?");
	$stmt->execute([$problem_id]);
	$max_score = $stmt->fetchColumn();

	$final_score = ($total_score_earned / $total_score_from_settings) * $max_score;
	$final_score = min($final_score, $max_score);

	if ($final_score == $max_score) {
		$status = "AC";
	}

	$stmt = $pdo->prepare("UPDATE submissions SET score = ?, status = ?, backup_logs = ? WHERE id = ?");
	$stmt->execute([round($final_score, 2), $status, $backup_logs, $submission_id]);

	echo "Chấm xong bài nộp ID: $submission_id - Kết quả: $status - Số điểm: $final_score\n";

	unlink($log_file);

	$stmt = $pdo->prepare("SELECT id FROM submissions WHERE user_id = ? AND problem_id = ? AND status = 'NOT SUBMITTED' ORDER BY submitted_at ASC LIMIT 1");
	$stmt->execute([$user_id, $problem_id]);
	$next_submission = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($next_submission) {
		$stmt = $pdo->prepare("UPDATE submissions SET status = 'PENDING' WHERE id = ?");
		$stmt->execute([$next_submission['id']]);
		echo "Chuyển bài tiếp theo thành PENDING: " . $next_submission['id'] . "\n";
	}

	usleep(1000000);
}
