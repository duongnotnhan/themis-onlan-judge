<?php
$env = parse_ini_file(__DIR__ . '/.env');
if (!$env) {
	die("Lỗi: không thể đọc tệp .env!");
}
if (!isset($env['DB_HOST'], $env['DB_PORT'], $env['DB_NAME'], $env['DB_USER'], $env['DB_PASS'])) {
	die("Lỗi: Cài đặt database không hợp lệ!");
}

$host = $env['DB_HOST'];
$port = $env['DB_PORT'];
$dbname = $env['DB_NAME'];
$username = $env['DB_USER'];
$password = $env['DB_PASS'];

try {
	$pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password, [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
	]);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Lỗi: Kết nối database thất bại: " . $e->getMessage());
}

$requiredTables = [
	'contest_settings',
	'problems',
	'submissions',
	'users'
];

foreach ($requiredTables as $table) {
	try {
		$stmt = $pdo->query("SHOW TABLES LIKE '$table'");
		if (!$stmt->fetch()) {
			die("Lỗi: Bảng '$table' không tồn tại trong cơ sở dữ liệu!");
		}
	} catch (PDOException $e) {
		die("Lỗi: Không thể kiểm tra bảng '$table': " . $e->getMessage());
	}
}

$requiredColumns = [
	'contest_settings' => ['title', 'start_time', 'end_time', 'submission_path', 'allow_registration', 'testcase_path'],
	'problems' => ['name', 'full_name', 'total_score', 'time_limit', 'memory_limit', 'description', 'order_id', 'submissions_limit', 'stdin', 'stdout'],
	'submissions' => ['user_id', 'problem_id', 'score', 'status', 'submitted_at', 'backup_code', 'backup_logs', 'language'],
	'users' => ['username', 'password', 'role', 'full_name', 'class', 'school']
];

foreach ($requiredColumns as $table => $columns) {
	foreach ($columns as $column) {
		try {
			$stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
			if (!$stmt->fetch()) {
				die("Lỗi: Cột '$column' không tồn tại trong bảng '$table'!");
			}
		} catch (PDOException $e) {
			die("Lỗi: Không thể kiểm tra cột '$column' trong bảng '$table': " . $e->getMessage());
		}
	}
}

function getDatabaseConnection() {
	global $pdo;
	return $pdo;
}
?>
