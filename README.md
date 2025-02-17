# themis-onlan-judge

Hệ Thống Kỳ Thi Themis OnLAN

## Thông Tin

- Hệ thống hỗ trợ host: [UniServerZ](https://www.uniformserver.com/).
- Hệ điều hành: Windows.
- Cơ sở dữ liệu: MariaDB.

## Cài Đặt

### Tải về phiên bản mới nhất tại [Releases](https://github.com/duongnotnhan/themis-onlan-judge/releases)

Sau khi tải về, giải nén vào thư mục `www` trong thư mục cài đặt _UniServerZ_.

### Cài Đặt Cơ Sở Dữ Liệu

Tải về và Nhập cài đặt vào SQL từ file [online-judge-setup-database.sql](https://github.com/duongnotnhan/themis-onlan-judge/blob/main/online-judge-setup-database.sql)

### Cấu Hình

1. Cài đặt kết nối Cơ Sở Dữ Liệu

   Mở file `config.php` và điền các thông tin tương ứng:

    ```php
    $host = 'localhost'; //địa chỉ host SQL
    $dbname = 'online_judge'; //mặc định
    $username = 'root'; //username của SQL
    $password = 'root'; //mật khẩu SQL
    ...
    ```

2. Cấu hình thư mục
   - Bật chạy máy chủ (Start Apache).
   - Đăng nhập tài khoản `admin` với mật khẩu: `admin1234`.
   - Tại **Bảng Điều Khiển**, sửa thông tin thư mục bài nộp theo thông tin cài trong phần mềm _Themis_:
     ![image](https://github.com/user-attachments/assets/d0eb67d8-0d00-48aa-9823-1b72a12f34fb)
   - Lưu thay đổi.

----
Thế là xong bước cài đặt ban đầu!

## Sử Dụng

(*) Định nghĩa _Người Dùng_: các người dùng có tài khoản trên hệ thống.

### Bảng Điều Khiển

1. Quản Lý Kỳ Thi

   Quản Trị Viên có thể thay đổi thông tin kỳ thi bao gồm Tên kỳ thi, Thời gian diễn ra kỳ thi và Thư mục bài nộp kỳ thi:
   ![image](https://github.com/user-attachments/assets/e80ad8cf-3982-4dd1-bee7-ada575dba90d)
2. Danh Sách Đề Bài

   Phần này đang được mở rộng... tạm thời được đưa qua trang `Danh Sách Đề Bài` riêng. Tất cả đề bài nằm trong này sẽ được sử dụng trong kỳ thi.
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
    ![image](https://github.com/user-attachments/assets/24b63683-db44-4020-ae98-2bf1f61c96b7)
    **Lưu Ý:** Các thông tin như Tên Đề Bài, Tổng Điểm, TL, ML cần phải khớp với máy chấm Themis.
2. Sửa Đề Bài

    Sửa lại thông tin đề bài tương ứng.
3. Xóa Đề Bài

    Xóa đề bài khỏi hệ thống kỳ thi.

### Lịch Sử Nộp Bài

![image](https://github.com/user-attachments/assets/e59f9885-90fa-4954-8efc-bee4b2e6097f)

#### Đối Với Người Dùng Thường

Có thể xem trạng thái, danh sách bài nộp của tất cả thí sinh trong kỳ thi. Tuy nhiên, không thể thực hiện chức năng Quản Trị.

#### Đối Với Quản Trị Viên

Có thể xem trạng thái, danh sách bài nộp của tất cả thí sinh trong kỳ thi cũng như xem chi tiết bài nộp bao gồm Mã Nguồn, Chi Tiết Chấm của thí sinh. Đặc biệt có thể thực hiện xóa bài nộp trong trường hợp cần thiết.
![image](https://github.com/user-attachments/assets/aff7c9f1-0726-4c30-b030-108f97d8f7fc)

### Tham Gia Kỳ Thi

Tất cả Người Dùng đều có thể tham gia kỳ thi, đọc đề bài cũng như nộp bài lên hệ thống.
![image](https://github.com/user-attachments/assets/82f01858-31b1-48f0-a3db-5ea46e84421d)

## Vấn Đề & Bổ Sung Sắp Tới

### Vấn Đề Đã Biết

1. Người dùng cũng như Quản Trị Viên chưa thể tự thay đổi thông tin Họ và Tên, Lớp của mình
   - Giải quyết tạm thời: người chạy máy chủ web phải vào Cơ Sở Dữ Liệu để thay đổi thông tin này.

### Vấn Đề Chưa Biết

_Hiện đang trong quá trình thử nghiệm..._

### Bổ Sung Sắp Tới

1. Hệ thống E-mail
    - Các tính năng mới: hệ thống xác thực e-mail, tính năng `Tự Đặt Lại Mật Khẩu`.
2. Thông tin cá nhân
    - Các tính năng mới: tính năng `Tự Thay Đổi Thông Tin Cá Nhân`, thêm thông tin `Tên Trường`.
