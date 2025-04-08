<?php
$env = parse_ini_file(__DIR__ . '/.env');
if (!$env) {
	die("Lỗi: không thể đọc tệp .env!");
}
$host = $env['DB_HOST'] ?? 'localhost';
$dbname = $env['DB_NAME'] ?? 'online_judge';
$username = $env['DB_USER'] ?? 'root';
$password = $env['DB_PASS'] ?? 'root';

try {
	$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
	]);	
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Lỗi: Kết nối database thất bại: " . $e->getMessage());
}

function getDatabaseConnection() {
	global $pdo;
	return $pdo;
}
?>
