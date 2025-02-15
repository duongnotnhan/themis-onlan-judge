-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 15, 2025 at 08:42 AM
-- Server version: 11.6.2-MariaDB
-- PHP Version: 8.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

--
-- Database: `online_judge`
--
CREATE DATABASE IF NOT EXISTS `online_judge` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
USE `online_judge`;

-- --------------------------------------------------------

--
-- Table structure for table `contest_settings`
--

CREATE TABLE `contest_settings` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `submission_path` varchar(255) NOT NULL,
  `allow_registration` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `contest_settings`
--

INSERT INTO `contest_settings` (`id`, `title`, `start_time`, `end_time`, `submission_path`, `allow_registration`) VALUES
(1, 'Test01', '2025-02-14 13:12:00', '2025-02-19 15:12:00', 'D:\\Themis\\data\\uploadDir', 1);

-- --------------------------------------------------------

--
-- Table structure for table `problems`
--

CREATE TABLE `problems` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_score` int(11) NOT NULL,
  `time_limit` float NOT NULL,
  `memory_limit` int(11) NOT NULL,
  `description` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `problems`
--

INSERT INTO `problems` (`id`, `name`, `total_score`, `time_limit`, `memory_limit`, `description`) VALUES
(1, 'abai', 1, 1, 1024, '# Tên API\r\n\r\n## Điểm cuối: `/example-endpoint`\r\n\r\n- **Phương thức:** `GET`\r\n- **Mô tả:** Lấy dữ liệu ví dụ.\r\n\r\n### Tham số yêu cầu\r\n\r\n- `param1` (bắt buộc): Mô tả của param1.\r\n- `param2` (tùy chọn): Mô tả của param2.\r\n\r\n### Phản hồi\r\n\r\n```json\r\n{\r\n  \"data\": \"Sample data\"\r\n}\r\n```\r\n\r\nhehe $\\sqrt{3x-1}+(1+x)^2$'),
(2, 'abai13', 10, 1, 1, '# Giới thiệu WOW\r\n\r\nĐã có ai dùng `GitHub` bao lâu nay vẫn không biết các tệp với đuôi mở rộng .md là gì không?\r\n\r\n`Markdown` là ngôn ngữ đánh dấu có cú pháp khá đơn giản và dễ hiểu, tạo thuận tiện cho việc chuyển đổi từ văn bản thuần sang `HTML`.\r\n\r\nThay vì dựa vào `HTML`, `Markdown` cho phép bạn định dạng văn bản mà trực quan hơn nhiều so với `HTML`.\r\n\r\nCó thể bạn chưa biết: `Markdown` có thể được sử dụng tại [Github](https://github.com) và [Discord](https://discord.com).\r\n\r\n> If you can, feel free to translate this repo into other languages, thanks!\r\n\r\n> Tài liệu được viết tay bởi [Lục Thiên Phong](https://github.com/lucthienphong1120), để giúp bạn có thêm hiểu biết và làm chủ về Markdown.\r\n\r\n# Mục lục\r\n\r\n[I. Sơ lược](#i-sơ-lược)\r\n- [1. MarkDown (Markup languages)](#1-markdown-markup-languages)\r\n- [2. Một số trình soạn thảo Markdown](#2-một-số-trình-soạn-thảo-markdown)\r\n\r\n[II. Cách sử dụng](#ii-cách-sử-dụng)\r\n- [1. Văn bản thuần](#1-văn-bản-thuần)\r\n  - [1. Tiêu đề - Heading](#1-tiêu-đề---heading)\r\n  - [2. Đoạn văn - Paragraph](#2-đoạn-văn---paragraph)\r\n  - [3. Chữ in nghiêng - Italic](#3-chữ-in-nghiêng---italic)\r\n  - [4. Chữ in đậm - Bold](#4-chữ-in-đậm---bold)\r\n  - [5. In đậm và in nghiêng](#5-in-đậm-và-in-nghiêng)\r\n  - [6. Chữ gạch giữa - Strikethrough](#6-chữ-gạch-giữa---strikethrough)\r\n  - [7. Code trong dòng - Inline Code](#7-code-trong-dòng---inline-code)\r\n- [2. Các khối](#2-các-khối)\r\n  - [1. Trích dẫn - Blockquote](#1-trích-dẫn---blockquote)\r\n  - [2. Danh sách có thứ tự - Ordered List](#2-danh-sách-có-thứ-tự---ordered-list)\r\n  - [3. Danh sách không có thứ tự - Unordered List](#3-danh-sách-không-có-thứ-tự---unordered-list)\r\n  - [4. Khối lệnh - Block Code](#4-khối-lệnh---block-code)\r\n  - [5. Bảng - Table](#5-bảng---table)\r\n- [3. Đặc biệt](#3-đặc-biệt)\r\n  - [1. Đường kẻ ngang - Horizonal rules](#1-đường-kẻ-ngang---horizonal-rules)\r\n  - [2. Liên kết - Link](#2-liên-kết---link)\r\n  - [3. Hình ảnh - Image](#3-hình-ảnh---image)\r\n  - [4. Biểu tượng cảm xúc - Icon](#4-biểu-tượng-cảm-xúc---icon)\r\n  - [5. Checkbox](#5-checkbox)\r\n  - [6. Escape markdown](#6-escape-markdown)\r\n\r\n[III. Kết thúc](#iii-kết-thúc)\r\n\r\n# I. Sơ lược\r\n\r\n## 1. MarkDown (Markup languages)\r\n\r\nSự thật là cái tên **\"Markdown\"** chính là một phép chơi chữ của từ **\"Markup\"**.\r\n\r\nMardown được sử dụng để xuất văn bản thô trên trình duyệt nhưng các ngôn ngữ đánh dấu khác lại có thể giao tiếp trực tiếp với máy tính. Đơn cử như `XML` là một ngôn ngữ đánh dấu văn bản mà cả con người lẫn máy móc có thể đọc được.\r\n\r\nMột ngôn ngữ đánh dấu văn bản khác mà mọi người chắc hẳn ai học CNTT cũng biết vì độ nổi tiếng của nó, chính là `HTML`, `Markdown` không mang trong mình sứ mệnh **\"Kẻ huỷ diệt HTML\"** hay gì, mà mục đích của nó chính là làm đơn giản hoá việc đánh dấu văn bản và tăng cường tốc độ viết lách một cách đáng kể.\r\n\r\n## 2. Một số trình soạn thảo Markdown\r\n\r\n- Mac, Windows, và Linux\r\n  - [Typora](https://typora.io/)\r\n  - [MacDown](https://macdown.uranusjr.com/)\r\n- Online\r\n  - [StackEdit](https://stackedit.io/) \r\n  - [Dillinger](https://dillinger.io/)\r\n  - [Hashify](https://hashify.me/)\r\n- Sau bài viết này, bạn có thể viết md mà không cần chuyển đổi\r\n  - Notepad\r\n  - Visual Studio Code\r\n  - Visual Code\r\n  - Notepad++\r\n  - Vi,nano,...\r\n  - Github,Discord,...\r\n\r\n# II. Cách sử dụng\r\n\r\n## 1. Văn bản thuần\r\n\r\n### 1 Tiêu đề - Heading\r\n\r\nBạn có thể viết loại tiêu đề `<h1>, <h2>,... <h6>` bằng cách thêm các dấu # tương ứng vào đầu dòng.\r\n\r\nMột dấu # tương đương với `<h1>`, hai dấu # tương đương với `<h2>` ...\r\n\r\nCú pháp:\r\n```\r\n# Tiêu đề loại 1\r\n## Tiêu đề loại 2\r\n### Tiêu đề loại 3\r\n#### Tiêu đề loại 4\r\n##### Tiêu đề loại 5\r\n###### Tiêu đề loại 6\r\n```\r\nKết quả:\r\n\r\n# Tiêu đề loại 1\r\n## Tiêu đề loại 2\r\n### Tiêu đề loại 3\r\n#### Tiêu đề loại 4\r\n##### Tiêu đề loại 5\r\n###### Tiêu đề loại 6\r\n\r\n### 2. Đoạn văn - Paragraph\r\n\r\nĐể xuống dòng giữa các văn bản `<p>`, sử dụng một dòng trống để tách các dòng văn bản.\r\n\r\nCú pháp:\r\n```\r\nĐây là dòng 1\r\n\r\nĐây là dòng 2\r\n```\r\nKết quả:\r\n\r\nĐây là dòng 1\r\n\r\nĐây là dòng 2\r\n\r\n### 3. Chữ in nghiêng - Italic\r\n\r\nĐể in nghiêng văn bản `<i>`, thêm một dấu * hoặc dấu _ trước và sau từ cần in nghiêng.\r\n\r\nCú pháp:\r\n```\r\n*Từ cần in nghiêng 1*\r\n\r\n_Từ cần in nghiêng 2_\r\n```\r\nKết quả:\r\n\r\n*Từ cần in nghiêng 1*\r\n\r\n_Từ cần in nghiêng 2_\r\n\r\n### 4. Chữ in đậm - Bold\r\n\r\nĐể in đậm văn bản `<b>`, thêm hai dấu * hoặc dấu _ trước và sau từ cần in đậm.\r\n\r\nCú pháp:\r\n```\r\n**Từ cần in đậm 1**\r\n\r\n__Từ cần in đậm 2__\r\n```\r\nKết quả:\r\n\r\n**Từ cần in đậm 1**\r\n\r\n__Từ cần in đậm 2__\r\n\r\n### 5. In đậm và in nghiêng\r\n\r\nĐơn giản, bạn chỉ cần ba dấu * hoặc dấu _ trước và sau từ đó.\r\n\r\nCú pháp:\r\n```\r\n***Từ in đậm và in nghiêng 1***\r\n\r\n___Từ in đậm và in nghiêng 2___\r\n```\r\nKết quả:\r\n\r\n***Từ in đậm và in nghiêng 1***\r\n\r\n___Từ in đậm và in nghiêng 2___\r\n\r\n### 6. Chữ gạch giữa - Strikethrough\r\n\r\nĐể tạo chữ gạch giữa, thêm 2 dấu ~ trước và sau từ đó.\r\n\r\nCú pháp:\r\n```\r\n~~Khuyến mại~~\r\n```\r\nKết quả:\r\n\r\n~~Khuyến mại~~\r\n\r\n### 7. Code trong dòng - Inline Code\r\n\r\nĐể viết inline `<code>`, bạn dùng 2 dấu ` ở trước và sau từ đó.\r\n\r\nCú pháp:\r\n```\r\n`inline code`\r\n```\r\nKết quả:\r\n\r\n`inline code`\r\n\r\n## 2. Các khối\r\n\r\n### 1. Trích dẫn - Blockquote\r\n\r\nĐể tạo một `<blockquote>`, thêm dấu > vào trước mỗi dòng trích dẫn.\r\n\r\nCú pháp:\r\n```\r\n> Trích dẫn dòng 1\r\n> Trích dẫn dòng 2\r\n```\r\nKết quả:\r\n\r\n> Trích dẫn dòng 1\r\n> Trích dẫn dòng 2\r\n\r\n### 2. Danh sách có thứ tự - Ordered List\r\n\r\nĐể tạo danh sách `<ol><li>`, bạn chỉ cần thêm các số, dấu chấm trước nội dung (dùng tab để phân cấp)\r\n\r\nCú pháp:\r\n```\r\n1. Mục thứ nhất\r\n2. Mục thứ hai\r\n3. Mục thứ ba\r\n```\r\nKết quả:\r\n\r\n1. Mục thứ nhất\r\n2. Mục thứ hai\r\n3. Mục thứ ba\r\n\r\n### 3. Danh sách không có thứ tự - Unordered List\r\n\r\nĐể tạo danh sách `<ul><li>`, bạn chỉ cần thêm dấu * hoặc - hoặc + trước nội dung (dùng tab để phân cấp)\r\n\r\nCú pháp:\r\n```\r\n- Mục thứ nhất\r\n- Mục thứ hai\r\n- Mục thứ ba\r\n```\r\nKết quả:\r\n\r\n- Mục thứ nhất\r\n- Mục thứ hai\r\n- Mục thứ ba\r\n\r\n### 4. Khối lệnh - Block Code\r\n\r\nĐể viết 1 đoạn `<code>`, bạn dùng 3 dấu ` ở trước và sau đoạn đó (có thể thêm format ngôn ngữ đó).\r\n\r\nCú pháp:\r\n\r\n![image](https://user-images.githubusercontent.com/90561566/160242871-aad90ad1-bd8d-4e5c-9146-3349fb7c8c98.png)\r\n\r\nKết quả:\r\n\r\n```python\r\nprint(\"hello world\")\r\n```\r\n\r\n### 5. Bảng - Table\r\n\r\nĐể tạo bảng `<table><tbody><tr><th><th>`, bạn chỉ cần ngăn cách bởi dấu | và cách đầu bảng với thân bảng bằng :--- (số dấu - tuỳ ý)\r\n\r\nCú pháp:\r\n```\r\n| Cột 1 | Cột 2 | Cột 3 | Cột 4 |\r\n| :--- | :--- | :--- | :--- |\r\n| A | B | C | D |\r\n| E | F | G | H |\r\n| I | K | L | M |\r\n```\r\nKết quả\r\n\r\n| Cột 1 | Cột 2 | Cột 3 | Cột 4 |\r\n| :--- | :--- | :--- | :--- |\r\n| A | B | C | D |\r\n| E | F | G | H |\r\n| I | K | L | M |\r\n\r\n## 3. Đặc biệt\r\n\r\n### 1. Đường kẻ ngang - Horizonal rules\r\n\r\nĐể tạo đường kẻ ngang, sử dụng ba dấu * hoặc - hoặc _ trên một dòng.\r\n\r\nCú pháp:\r\n```\r\n---\r\n***\r\n___\r\n```\r\nKết quả:\r\n\r\n---\r\n***\r\n___\r\n\r\n### 2. Liên kết - Link\r\n\r\nĐể chèn trực tiếp, bạn có thể paste thẳng nó như bình thường.\r\n\r\nĐể dẫn liên kết `<a href=\"https://github.com\">Github</a>`, bạn dùng `[text](link)`.\r\n\r\nCú pháp:\r\n```\r\nTrực tiếp: https://github.com/lucthienphong1120\r\n\r\nGián tiếp: [Github](https://github.com/lucthienphong1120)\r\n```\r\nKết quả:\r\n\r\nTrực tiếp: https://github.com/lucthienphong1120\r\n\r\nGián tiếp: [Github](https://github.com/lucthienphong1120)\r\n\r\n### 3. Hình ảnh - Image\r\n\r\nĐể chèn trực tiếp, bạn có thể paste thẳng nó như bình thường.\r\n\r\nĐể dẫn ảnh `<img src=\"https://avatars.githubusercontent.com/u/583231 alt=\"Github\">`, bạn dùng `![text](link ảnh)`.\r\n\r\nHoặc `![](link ảnh)` nếu không cần chữ khi hover.\r\n\r\nCú pháp:\r\n```\r\n![](https://avatars.githubusercontent.com/u/583231)\r\n```\r\nKết quả:\r\n\r\n![](https://avatars.githubusercontent.com/u/583231)\r\n\r\nĐể chèn liên kết vào ảnh `<a href=\"link\"><img src=\"link ảnh\" alt=\"chữ\"></a>` thì chỉ cần kết hợp đúng cú pháp là được.\r\n\r\n```\r\n[ ![chữ](link ảnh) ] (link)\r\n```\r\n\r\n### 4. Biểu tượng cảm xúc - Icon\r\n\r\nPhần này tuỳ vào nền tảng (Github, Discord, ...) có icon đó không, bạn ghi dấu : và tên icon.\r\n\r\nCú pháp:\r\n\r\n![image](https://user-images.githubusercontent.com/90561566/160245877-ccf277ff-094f-482c-801b-4a8fe46471b7.png)\r\n\r\nKết quả:\r\n\r\n👁️\r\n\r\n> More information: https://github.com/lucthienphong1120/Github-Emojis\r\n\r\n### 5. Checkbox\r\n\r\nĐể chèn `checkbox/checked` (thường dùng cho to do list trên github) thì ta đánh dấu như list và thêm 1 cặp ngoặc vuông.\r\n\r\nCú pháp:\r\n\r\n```\r\n- [ ] Checkbox\r\n- [x] Checked\r\n```\r\n\r\nKết quả:\r\n\r\n- [ ] Checkbox\r\n- [x] Checked\r\n\r\n### 6. Escape markdown\r\n\r\nĐôi khi bạn sẽ cần những kí hiệu trùng với cú pháp của markdown. Để phân biệt, bạn chỉ cần thêm dấu \\ trước những kí hiệu đó là được.\r\n\r\nCú pháp:\r\n```\r\n\\`hai dấu nháy\\`\r\n\r\n\\*\\*\\*ba dấu sao hai bên\\*\\*\\*\r\n```\r\nKết quả:\r\n\r\n\\`hai dấu nháy\\`\r\n\r\n\\*\\*\\*ba dấu sao hai bên\\*\\*\\*\r\n\r\n# III. Kết thúc\r\n\r\nHy vọng qua bài viết này, bạn sẽ không còn thấy Markdown khó nữa và sẽ nắm được cách dùng Markdown trong nhiều việc của mình hơn nhé.\r\n\r\nNếu thấy hay hãy đừng ngần ngại mà thả 1 sao cho tôi, chúc bạn 1 ngày làm việc thật tốt!\r\n\r\n> Bạn có thể thoải mái đóng góp (contribute) hoặc liên kết (fork) dự án này.\r\n\r\n> You are free to contribute or fork this repo.');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `problem_id` int(11) NOT NULL,
  `status` enum('AC','WA','TLE','MLE','ER/IR','CE') DEFAULT 'WA',
  `score` float DEFAULT 0,
  `submitted_at` timestamp NULL DEFAULT current_timestamp(),
  `backup_code` longtext DEFAULT NULL,
  `backup_logs` longtext DEFAULT NULL,
  `language` varchar(10) NOT NULL DEFAULT 'UNKNOWN'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`id`, `user_id`, `problem_id`, `status`, `score`, `submitted_at`, `backup_code`, `backup_logs`, `language`) VALUES
(5, 1, 1, 'ER/IR', 0, '2025-02-14 06:43:51', '#include <bits/stdc++.h>\r\n\r\nusing namespace std;\r\nlong long n ;\r\nint main()\r\n{\r\n   // freopen(\"ABAI14.inp\",\"r\",stdin);\r\n   // freopen(\"ABAI14.out\",\"w\",stdout);\r\n    cin>>n;\r\n    if (n%3==0&&n%5==0)\r\n    cout <<1 ;\r\n    else\r\n        cout<<0;\r\n    return 0;\r\n}\r\n', '﻿admin‣abai: 0.00\r\nabai.cpp\r\n\"C:\\Program Files (x86)\\Themis\\gcc\\bin\\g++.exe\" -std=c++14 \"abai.cpp\" -pipe -O2 -s -static -lm -x c++ -o\"abai.exe\" -Wl,--stack,66060288|@WorkDir=C:\\ProgramData\\ThemisWorkSpace\\WaitRoom10446\\\r\nDịch thành công.\r\n\r\nadmin‣abai‣test01: 0.00\r\nChạy sinh lỗi\r\nCommand: \"C:\\ProgramData\\ThemisWorkSpace\\ContestRoom40026\\abai.exe\" terminated with exit code: 3221225794 (Hexadecimal: C0000142)\r\n', 'CPP'),
(7, 1, 2, 'AC', 10, '2025-02-14 06:46:00', '#include <bits/stdc++.h>\r\n\r\nusing namespace std;\r\nlong long n ;\r\nint main()\r\n{\r\n    freopen(\"ABAI14.inp\",\"r\",stdin);\r\n    freopen(\"ABAI14.out\",\"w\",stdout);\r\n    cin>>n;\r\n    if (n%3==0&&n%5==0)\r\n    cout <<1 ;\r\n    else\r\n        cout<<0;\r\n    return 0;\r\n}\r\n', '﻿admin‣abai13: 10.00\r\nabai13.cpp\r\n\"C:\\Program Files (x86)\\Themis\\gcc\\bin\\g++.exe\" -std=c++14 \"abai13.cpp\" -pipe -O2 -s -static -lm -x c++ -o\"abai13.exe\" -Wl,--stack,66060288|@WorkDir=C:\\ProgramData\\ThemisWorkSpace\\WaitRoom20409\\\r\nDịch thành công.\r\n\r\nadmin‣abai13‣test01: 1.00\r\nThời gian ≈ 0.030227800 giây\r\nKết quả khớp đáp án!\r\nadmin‣abai13‣test02: 1.00\r\nThời gian ≈ 0.026791800 giây\r\nKết quả khớp đáp án!\r\nadmin‣abai13‣test03: 1.00\r\nThời gian ≈ 0.030131900 giây\r\nKết quả khớp đáp án!\r\nadmin‣abai13‣test04: 1.00\r\nThời gian ≈ 0.030431700 giây\r\nKết quả khớp đáp án!\r\nadmin‣abai13‣test05: 1.00\r\nThời gian ≈ 0.032495000 giây\r\nKết quả khớp đáp án!\r\nadmin‣abai13‣test06: 1.00\r\nThời gian ≈ 0.030278100 giây\r\nKết quả khớp đáp án!\r\nadmin‣abai13‣test07: 1.00\r\nThời gian ≈ 0.029588100 giây\r\nKết quả khớp đáp án!\r\nadmin‣abai13‣test08: 1.00\r\nThời gian ≈ 0.029702000 giây\r\nKết quả khớp đáp án!\r\nadmin‣abai13‣test09: 1.00\r\nThời gian ≈ 0.030105300 giây\r\nKết quả khớp đáp án!\r\nadmin‣abai13‣test10: 1.00\r\nThời gian ≈ 0.030698600 giây\r\nKết quả khớp đáp án!\r\n', 'CPP'),
(8, 1, 1, 'ER/IR', 0, '2025-02-14 13:37:47', '#include <bits/stdc++.h>\r\n\r\nusing namespace std;\r\nlong long n ;\r\nint main()\r\n{\r\n    freopen(\"ABAI14.inp\",\"r\",stdin);\r\n    freopen(\"ABAI14.out\",\"w\",stdout);\r\n    cin>>n;\r\n    if (n%3==0&&n%5==0)\r\n    cout <<1 ;\r\n    else\r\n        cout<<0;\r\n    return 0;\r\n}\r\n', '﻿admin‣abai: 0.00\r\nabai.cpp\r\n\"C:\\Program Files (x86)\\Themis\\gcc\\bin\\g++.exe\" -std=c++14 \"abai.cpp\" -pipe -O2 -s -static -lm -x c++ -o\"abai.exe\" -Wl,--stack,66060288|@WorkDir=C:\\ProgramData\\ThemisWorkSpace\\WaitRoom63858\\\r\nDịch thành công.\r\n\r\nadmin‣abai‣test01: 0.00\r\nChạy sinh lỗi\r\nCommand: \"C:\\ProgramData\\ThemisWorkSpace\\ContestRoom64697\\abai.exe\" terminated with exit code: 3221225794 (Hexadecimal: C0000142)\r\n', 'CPP');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `full_name` varchar(255) NOT NULL,
  `class` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `full_name`, `class`) VALUES
(1, 'admin', '$2y$10$4FubTlF1n5J45snAZq5X9OnMLHpTp8JwAfocs5Iq5swXlxoeL0PB2', 'admin', '2025-02-13 07:51:35', 'QTV', '11 Tin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contest_settings`
--
ALTER TABLE `contest_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `problems`
--
ALTER TABLE `problems`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `problem_id` (`problem_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contest_settings`
--
ALTER TABLE `contest_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `problems`
--
ALTER TABLE `problems`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`problem_id`) REFERENCES `problems` (`id`) ON DELETE CASCADE;
COMMIT;
