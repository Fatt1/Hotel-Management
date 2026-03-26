@"

# Hotel Management System

Hệ thống quản lý khách sạn được xây dựng bằng Laravel Framework.

## Yêu cầu hệ thống

- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Node.js & NPM

## Hướng dẫn cài đặt

### Bước 1 Cấu hình database

Mở file `.env` và cấu hình thông tin database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotel_db
DB_USERNAME=root
DB_PASSWORD=
```

### Bước 2: Chạy migration và seeder

```bash
# Chạy migration để tạo các bảng trong database
php artisan migrate:fresh

# Chạy seeder để thêm dữ liệu mẫu
php artisan db:seed
```

### Bước 3: Khởi chạy ứng dụng

```bash
# Chạy toàn bộ server + queue + vite
composer run dev
```

Ứng dụng sẽ chạy tại: `http://localhost:8000`

## Tính năng chính

- Quản lý phòng khách sạn
- Quản lý đặt phòng
- Quản lý khách hàng
- Quản lý nhân viên
- Dashboard admin
- Báo cáo và thống kê

## Tài khoản mặc định

Sau khi chạy seeder, bạn có thể đăng nhập với các tài khoản sau:

**Admin:**

- Email: admin@gmail.com
- Password: admin

## License
