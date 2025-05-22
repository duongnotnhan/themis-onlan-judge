# themis-onlan-judge

Hệ Thống Kỳ Thi Themis OnLAN

## Thông Tin

- Hệ thống hỗ trợ host: [UniServerZ](https://www.uniformserver.com/) (phiên bản 15.x.x+).
- Hệ điều hành: Windows 10 trở lên.
- Cơ sở dữ liệu: [MariaDB](https://mariadb.org/download/?t=mariadb&o=true&p=mariadb&r=11.6.2&os=windows&cpu=x86_64&pkg=msi&mirror=archive) (phiên bản 11.6 trở lên).

## Cài Đặt

### Video Hướng Dẫn
[![Xem ngay!](https://img.youtube.com/vi/PAfpn-G5Tqc/0.jpg)](https://www.youtube.com/watch?v=PAfpn-G5Tqc)

### Tải về phiên bản mới nhất tại [Releases](https://github.com/duongnotnhan/themis-onlan-judge/releases)

Sau khi tải về, giải nén vào thư mục `www` trong thư mục cài đặt _UniServerZ_, nếu cài đặt bản AttachedUniserverZ thì không cần tải UniserverZ.

### Cài Đặt Cơ Sở Dữ Liệu

Tải về và Nhập cài đặt vào hệ CSDL MariaDB từ file [online-judge-setup-database.sql](https://github.com/duongnotnhan/themis-onlan-judge/blob/main/online-judge-setup-database.sql) có sẵn trong thư mục.

### Cấu Hình

1. Cài đặt kết nối Cơ Sở Dữ Liệu

   Mở file `.env` và điền các thông tin tương ứng:

   ```env
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=online_judge
   DB_USER=root
   DB_PASS=root
   ```

2. Cấu hình thư mục
   - Bật chạy máy chủ (Start Apache).
   - Đăng nhập tài khoản `admin` với mật khẩu: `admin1234`.
   - Tại **Bảng Điều Khiển**, sửa thông tin thư mục bài nộp theo thông tin cài trong phần mềm _Themis_:

    ![image](https://github.com/user-attachments/assets/d0eb67d8-0d00-48aa-9823-1b72a12f34fb)
   - Dựa trên thư mục lưu testcase (hay còn gọi là thư mục đề bài trên phần mềm Themis), sửa trường "Thư mục testcase (theo Themis)" trên trang web.
   - Lưu thay đổi.

3. Cài đặt hệ thống xử lý hàng đợi
   - Cài đặt PHP có sẵn trong UniserverZ vào PATH hoặc sử dụng trực tiếp bằng cách sao chép đường dẫn.
   - Khởi chạy tệp `judge_worker.php` theo mẫu như trong hình:

   ![image](https://i.postimg.cc/Hsb7817F/Screenshot-2025-04-01-201644.png)

----
Thế là xong bước cài đặt ban đầu!

(*) Bộ testcase mẫu của database mẫu có thể tải tại [đây](https://drive.google.com/drive/folders/1KsdH-ZkjJoXLcXqJvGBivBQnGWmFRwkD?usp=sharing).

## Sử Dụng

(*) Định nghĩa _Người Dùng_: các người dùng có tài khoản trên hệ thống.

### Bảng Điều Khiển

1. Quản Lý Kỳ Thi

   Quản Trị Viên có thể thay đổi thông tin kỳ thi bao gồm Tên kỳ thi, Thời gian diễn ra kỳ thi, Thư mục bài nộp kỳ thi và Thư mục lưu testcase của Themis:

   ![image](https://i.postimg.cc/L6VGFfPt/screenshot-4.png)
2. Danh Sách Đề Bài

   Quản trị viên có thể chọn các đề bài được sử dụng trong kỳ thi từ `Danh Sách Đề Bài`. Có thể tùy chọn thứ tự của đề bài, số lần nộp giới hạn, ....

   ![image](https://i.postimg.cc/7L9p48Wr/Screenshot-2025-04-03-134258.png)
3. Cài Đặt Đăng Ký

   Bật/Tắt tính năng đăng ký tài khoản của hệ thống.
4. Reset Dữ Liệu

   Cho phép Quản Trị Viên đặt lại dữ liệu của Đề Bài, Bài Nộp, Người Dùng (ngoại trừ người dùng có quyền Quản Trị).
5. Đặt Lại Mật Khẩu

   Một số Người Dùng có thể quên mật khẩu, Quản Trị Viên dùng tính năng này để tạo mật khẩu mới cho người dùng chỉ định.

### Danh Sách Đề Bài

Hiển thị danh sách đề bài nằm trong hệ thống trang web.

1. Tạo Đề Bài

   Quản Trị Viên có thể tạo đề bài mới cho kỳ thi:

   ![image](https://i.postimg.cc/v8S7CZqP/screenshot-6.png)

   **Lưu Ý:** Các thông tin như Tên Đề Bài (Themis), Giới hạn thời gian, Giới hạn bộ nhớ, ... cần phải khớp với máy chấm Themis. Có thể sử dụng tính năng chỉnh sửa cài đặt testcase tại trang danh sách đề bài để đồng bộ. Điểm trên trang web được phép khác với tổng điểm của Themis do điểm trên hệ thống được tính theo tỉ lệ điểm từ tệp cài đặt.
2. Sửa Đề Bài

   Sửa lại thông tin đề bài tương ứng.
3. Sửa Cài Đặt Testcase

   ![image](https://i.postimg.cc/dtzZvkK0/screenshot-5.png)
4. Xóa Đề Bài

   Xóa đề bài khỏi hệ thống kỳ thi.

### Lịch Sử Nộp Bài

![image](https://i.postimg.cc/VzV010cT/screenshot-3.png)

#### Đối Với Người Dùng Thường

Có thể xem trạng thái, danh sách bài nộp của tất cả thí sinh trong kỳ thi, xem bảng xếp hạng vắn tắt (trang chủ) hoặc đầy đủ (trang được tách biệt). Tuy nhiên, không thể thực hiện chức năng Quản Trị.

#### Đối Với Quản Trị Viên

Có thể xem trạng thái, danh sách bài nộp của tất cả thí sinh trong kỳ thi cũng như xem chi tiết bài nộp bao gồm Mã Nguồn, Chi Tiết Chấm của thí sinh. Đặc biệt có thể thực hiện chấm lại hoặc xóa bài nộp trong trường hợp cần thiết.

### Tham Gia Kỳ Thi

Tất cả Người Dùng đều có thể tham gia kỳ thi, đọc đề bài cũng như nộp bài lên hệ thống.

![image](https://i.postimg.cc/ydRBBdhF/Screenshot-2025-04-17-204834.png)

## Vấn Đề & Bổ Sung Sắp Tới

### Vấn Đề Đã Biết

1. Người dùng chưa thể tự cấp lại mật khẩu trong trường hợp quên (cần trợ giúp giải pháp).

### Vấn Đề Chưa Biết

- [Báo cáo tại đây.](https://github.com/duongnotnhan/themis-onlan-judge/issues)

### Bổ Sung Sắp Tới

1. Sửa lỗi giao diện.

----
**Nhóm tác giả:** DuongNhanAC, ayor.
