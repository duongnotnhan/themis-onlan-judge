-- --------------------------------------------------------
-- Máy chủ:                      127.0.0.1
-- Server version:               11.6.2-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Phiên bản:           12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for online_judge
CREATE DATABASE IF NOT EXISTS `online_judge` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci */;
USE `online_judge`;

-- Dumping structure for table online_judge.contest_settings
CREATE TABLE IF NOT EXISTS `contest_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `submission_path` varchar(255) NOT NULL,
  `allow_registration` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table online_judge.contest_settings: ~1 rows (approximately)
DELETE FROM `contest_settings`;
INSERT INTO `contest_settings` (`id`, `title`, `start_time`, `end_time`, `submission_path`, `allow_registration`) VALUES
	(1, 'Test01', '2025-03-31 19:30:00', '2025-04-30 15:12:00', 'D:\\Themis\\data\\uploadDir', 1);

-- Dumping structure for table online_judge.problems
CREATE TABLE IF NOT EXISTS `problems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `total_score` int(11) NOT NULL,
  `time_limit` float NOT NULL,
  `memory_limit` int(11) NOT NULL,
  `description` longtext NOT NULL,
  `order_id` int(11) DEFAULT -1,
  `submissions_limit` int(11) DEFAULT -1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table online_judge.problems: ~2 rows (approximately)
DELETE FROM `problems`;
INSERT INTO `problems` (`id`, `name`, `total_score`, `time_limit`, `memory_limit`, `description`, `order_id`, `submissions_limit`) VALUES
	(1, 'snt', 50, 1, 1024, 'Cho một số nguyên dương $N$, hãy kiểm tra xem số đó có phải là số nguyên tố hay không.\r\n\r\n### Đầu Vào (từ file `snt.inp`)\r\n- Một dòng duy nhất chứa số nguyên dương $N$ ($1 \\le N \\le 10^{12}$).\r\n\r\n### Đầu Ra (ra file `snt.out`)\r\n- Một dòng duy nhất chứa câu trả lời "`True`" hoặc "`False`".\r\n\r\n### Ví Dụ\r\n#### Sample Input #1\r\n\r\n```txt\r\n3\r\n```\r\n\r\n#### Sample Output #1\r\n\r\n```txt\r\nTrue\r\n```\r\n\r\n#### Sample Input #2\r\n\r\n```txt\r\n15\r\n```\r\n\r\n#### Sample Output #2\r\n\r\n```txt\r\nFalse\r\n```', 1, -1),
	(2, 'snt_', 50, 1.5, 1024, 'Cho $T$ số nguyên dương $N$, hãy kiểm tra xem mỗi số trong các số $N$ đó có phải là số nguyên tố hay không.\r\n\r\n### Đầu Vào (từ file `snt_.inp`)\r\n- Dòng đầu tiên chứa số nguyên dương $T$ ($1 \\le T \\le 10^6$)\r\n- Mỗi dòng trong $T$ dòng tiếp theo chứa một số nguyên dương $N$ ($1 \\le N \\le 10^9$).\r\n\r\n### Đầu Ra (ra file `snt_.out`)\r\n- Gồm $T$ dòng, mỗi dòng chưa câu trả lời "`True`" hoặc "`False`".\r\n\r\n### Ví Dụ\r\n#### Sample Input #1\r\n```txt\r\n2\r\n2\r\n9\r\n```\r\n\r\n#### Sample Output #1\r\n```txt\r\nTrue\r\nFalse\r\n```\r\n\r\n#### Sample Input #2\r\n```txt\r\n3\r\n5\r\n10\r\n15\r\n```\r\n\r\n#### Sample Output #2\r\n```txt\r\nTrue\r\nFalse\r\nFalse\r\n```', 2, 5);

-- Dumping structure for table online_judge.submissions
CREATE TABLE IF NOT EXISTS `submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `problem_id` int(11) NOT NULL,
  `status` enum('AC','WA','TLE','MLE','ER/IR','CE','PENDING','NOT SUBMITTED') DEFAULT 'PENDING',
  `score` float DEFAULT 0,
  `submitted_at` timestamp NULL DEFAULT current_timestamp(),
  `backup_code` longtext DEFAULT NULL,
  `backup_logs` longtext DEFAULT NULL,
  `language` varchar(10) NOT NULL DEFAULT 'UNKNOWN',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `problem_id` (`problem_id`),
  CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`problem_id`) REFERENCES `problems` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table online_judge.submissions: ~2 rows (approximately)
DELETE FROM `submissions`;
INSERT INTO `submissions` (`id`, `user_id`, `problem_id`, `status`, `score`, `submitted_at`, `backup_code`, `backup_logs`, `language`) VALUES
	(1, 1, 2, 'TLE', 43, '2025-03-31 12:58:27', '#include <stdio.h>\r\n#include <math.h>\r\n\r\nint is_prime(long long n) {\r\n    if (n < 2) return 0;\r\n    if (n == 2) return 1;\r\n    if (n % 2 == 0) return 0;\r\n    for (long long i = 3; i * i <= n; i += 2) {\r\n        if (n % i == 0) return 0;\r\n    }\r\n    return 1;\r\n}\r\n\r\nint main() {\r\n    FILE *inp = fopen("snt_.inp", "r");\r\n    FILE *outp = fopen("snt_.out", "w");\r\n    \r\n    if (!inp || !outp) {\r\n        printf("Không thể mở file.\\n");\r\n        return 1;\r\n    }\r\n\r\n    int T;\r\n    fscanf(inp, "%d", &T);\r\n    \r\n    while (T--) {\r\n        long long a;\r\n        fscanf(inp, "%lld", &a);\r\n        fprintf(outp, "%s\\n", is_prime(a) ? "True" : "False");\r\n    }\r\n\r\n    fclose(inp);\r\n    fclose(outp);\r\n    return 0;\r\n}\r\n', '﻿admin‣snt_: 43.00\r\nsnt_.c\r\n"C:\\Program Files (x86)\\Themis\\gcc\\bin\\gcc.exe" -std=c11 "snt_.c" -pipe -O2 -s -static -lm -x c -o"snt_.exe" -Wl,--stack,66060288|@WorkDir=C:\\ProgramData\\ThemisWorkSpace\\WaitRoom30261\\\r\nDịch thành công.\r\n\r\nadmin‣snt_‣test01: 1.00\r\nThời gian ≈ 0.668122900 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test02: 0.00\r\nChạy quá thời gian\r\nadmin‣snt_‣test03: 1.00\r\nThời gian ≈ 0.042717800 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test04: 1.00\r\nThời gian ≈ 0.053300200 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test05: 0.00\r\nChạy quá thời gian\r\nadmin‣snt_‣test06: 1.00\r\nThời gian ≈ 0.092038900 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test07: 1.00\r\nThời gian ≈ 0.031703300 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test08: 1.00\r\nThời gian ≈ 0.032896700 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test09: 1.00\r\nThời gian ≈ 0.067952000 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test10: 1.00\r\nThời gian ≈ 0.051351400 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test11: 1.00\r\nThời gian ≈ 0.054703700 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test12: 1.00\r\nThời gian ≈ 0.079526300 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test13: 1.00\r\nThời gian ≈ 0.076418400 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test14: 0.00\r\nChạy quá thời gian\r\nadmin‣snt_‣test15: 1.00\r\nThời gian ≈ 0.036765100 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test16: 1.00\r\nThời gian ≈ 0.047707400 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test17: 1.00\r\nThời gian ≈ 0.025599200 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test18: 1.00\r\nThời gian ≈ 0.075384100 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test19: 1.00\r\nThời gian ≈ 0.065457900 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test20: 1.00\r\nThời gian ≈ 0.079563500 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test21: 1.00\r\nThời gian ≈ 0.092857200 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test22: 0.00\r\nChạy quá thời gian\r\nadmin‣snt_‣test23: 1.00\r\nThời gian ≈ 0.027495300 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test24: 1.00\r\nThời gian ≈ 0.060938600 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test25: 1.00\r\nThời gian ≈ 0.086855700 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test26: 1.00\r\nThời gian ≈ 0.060615500 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test27: 1.00\r\nThời gian ≈ 0.045927200 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test28: 1.00\r\nThời gian ≈ 0.086617000 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test29: 1.00\r\nThời gian ≈ 0.753755400 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test30: 1.00\r\nThời gian ≈ 0.084959600 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test31: 1.00\r\nThời gian ≈ 0.086569800 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test32: 0.00\r\nChạy quá thời gian\r\nadmin‣snt_‣test33: 1.00\r\nThời gian ≈ 0.029363700 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test34: 1.00\r\nThời gian ≈ 0.082768600 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test35: 1.00\r\nThời gian ≈ 0.035265400 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test36: 1.00\r\nThời gian ≈ 0.085671400 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test37: 1.00\r\nThời gian ≈ 0.050652000 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test38: 1.00\r\nThời gian ≈ 0.075766700 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test39: 1.00\r\nThời gian ≈ 0.050966400 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test40: 1.00\r\nThời gian ≈ 0.077405100 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test41: 1.00\r\nThời gian ≈ 0.071531200 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test42: 0.00\r\nChạy quá thời gian\r\nadmin‣snt_‣test43: 0.00\r\nChạy quá thời gian\r\nadmin‣snt_‣test44: 1.00\r\nThời gian ≈ 0.042754700 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test45: 1.00\r\nThời gian ≈ 0.051933700 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test46: 1.00\r\nThời gian ≈ 0.086011300 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test47: 1.00\r\nThời gian ≈ 0.072795100 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test48: 1.00\r\nThời gian ≈ 0.030349300 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test49: 1.00\r\nThời gian ≈ 0.080973900 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt_‣test50: 1.00\r\nThời gian ≈ 0.055862400 giây\r\nKết quả khớp đáp án!\r\n', 'C'),
	(2, 1, 1, 'AC', 50, '2025-03-31 13:03:35', '#include <stdio.h>\r\n#include <math.h>\r\n\r\nint is_prime(long long n) {\r\n    if (n < 2) return 0;\r\n    if (n == 2) return 1;\r\n    if (n % 2 == 0) return 0;\r\n    for (long long i = 3; i * i <= n; i += 2) {\r\n        if (n % i == 0) return 0;\r\n    }\r\n    return 1;\r\n}\r\n\r\nint main() {\r\n    FILE *inp = fopen("snt.inp", "r");\r\n    FILE *outp = fopen("snt.out", "w");\r\n\r\n    int T=1;;\r\n    \r\n    while (T--) {\r\n        long long a;\r\n        fscanf(inp, "%lld", &a);\r\n        fprintf(outp, "%s\\n", is_prime(a) ? "True" : "False");\r\n    }\r\n\r\n    fclose(inp);\r\n    fclose(outp);\r\n    return 0;\r\n}\r\n', '﻿admin‣snt: 50.00\r\nsnt.c\r\n"C:\\Program Files (x86)\\Themis\\gcc\\bin\\gcc.exe" -std=c11 "snt.c" -pipe -O2 -s -static -lm -x c -o"snt.exe" -Wl,--stack,66060288|@WorkDir=C:\\ProgramData\\ThemisWorkSpace\\WaitRoom10945\\\r\nDịch thành công.\r\n\r\nadmin‣snt‣test01: 1.00\r\nThời gian ≈ 0.030238900 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test02: 1.00\r\nThời gian ≈ 0.029344300 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test03: 1.00\r\nThời gian ≈ 0.031359800 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test04: 1.00\r\nThời gian ≈ 0.030013500 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test05: 1.00\r\nThời gian ≈ 0.033351600 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test06: 1.00\r\nThời gian ≈ 0.031395800 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test07: 1.00\r\nThời gian ≈ 0.030361200 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test08: 1.00\r\nThời gian ≈ 0.030020900 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test09: 1.00\r\nThời gian ≈ 0.032439400 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test10: 1.00\r\nThời gian ≈ 0.031094800 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test11: 1.00\r\nThời gian ≈ 0.029330300 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test12: 1.00\r\nThời gian ≈ 0.028663300 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test13: 1.00\r\nThời gian ≈ 0.029349600 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test14: 1.00\r\nThời gian ≈ 0.030231500 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test15: 1.00\r\nThời gian ≈ 0.029533200 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test16: 1.00\r\nThời gian ≈ 0.029040300 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test17: 1.00\r\nThời gian ≈ 0.030499900 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test18: 1.00\r\nThời gian ≈ 0.029369300 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test19: 1.00\r\nThời gian ≈ 0.032383500 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test20: 1.00\r\nThời gian ≈ 0.033382900 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test21: 1.00\r\nThời gian ≈ 0.032523100 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test22: 1.00\r\nThời gian ≈ 0.034900400 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test23: 1.00\r\nThời gian ≈ 0.032155700 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test24: 1.00\r\nThời gian ≈ 0.031002100 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test25: 1.00\r\nThời gian ≈ 0.029576500 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test26: 1.00\r\nThời gian ≈ 0.029927200 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test27: 1.00\r\nThời gian ≈ 0.030230100 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test28: 1.00\r\nThời gian ≈ 0.029873400 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test29: 1.00\r\nThời gian ≈ 0.028725400 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test30: 1.00\r\nThời gian ≈ 0.033345800 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test31: 1.00\r\nThời gian ≈ 0.032523300 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test32: 1.00\r\nThời gian ≈ 0.029852000 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test33: 1.00\r\nThời gian ≈ 0.029689600 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test34: 1.00\r\nThời gian ≈ 0.027498300 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test35: 1.00\r\nThời gian ≈ 0.029263700 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test36: 1.00\r\nThời gian ≈ 0.030480900 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test37: 1.00\r\nThời gian ≈ 0.033596000 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test38: 1.00\r\nThời gian ≈ 0.030489900 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test39: 1.00\r\nThời gian ≈ 0.029033600 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test40: 1.00\r\nThời gian ≈ 0.030853700 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test41: 1.00\r\nThời gian ≈ 0.027719800 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test42: 1.00\r\nThời gian ≈ 0.028617900 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test43: 1.00\r\nThời gian ≈ 0.028188100 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test44: 1.00\r\nThời gian ≈ 0.029075900 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test45: 1.00\r\nThời gian ≈ 0.028043800 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test46: 1.00\r\nThời gian ≈ 0.028839800 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test47: 1.00\r\nThời gian ≈ 0.031823000 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test48: 1.00\r\nThời gian ≈ 0.029001200 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test49: 1.00\r\nThời gian ≈ 0.030100200 giây\r\nKết quả khớp đáp án!\r\nadmin‣snt‣test50: 1.00\r\nThời gian ≈ 0.032775400 giây\r\nKết quả khớp đáp án!\r\n', 'C');

-- Dumping structure for table online_judge.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `full_name` varchar(255) NOT NULL,
  `class` varchar(50) NOT NULL,
  `school` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table online_judge.users: ~1 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `full_name`, `class`, `school`) VALUES
	(1, 'admin', '$2y$10$x1G1/YojYAsAl1.j8uSKnueanSm1mITN/yGs21fDN2BBwTW7GnFAu', 'admin', '2025-02-13 07:51:35', 'đây là họ và tên', '11 Tin', 'THPT Chuyên Bảo Lộc');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
