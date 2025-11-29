# Hướng dẫn Deploy lên Render.com

## Bước 1: Tạo MySQL Private Service

1. Trên Render Dashboard, tạo **New** > **Private Service**
2. Chọn **Docker** làm runtime
3. **Image**: `mysql:5.7`
4. **Service Name**: `ltweb-mysql` (hoặc tên tùy chọn, nhớ ghi lại để dùng cho DB_HOST)
5. **Environment**:
   - `MYSQL_ROOT_PASSWORD`: `root`
   - `MYSQL_DATABASE`: `expense_manager`
   - `MYSQL_USER`: `user`
   - `MYSQL_PASSWORD`: `password`
6. **Persistent Disk**:
   - Mount Path: `/var/lib/mysql`
   - Size: 10 GB (hoặc đủ cho DB)
7. **Deploy** và chờ service chạy.

## Bước 2: Tạo Web Service

1. Tạo **New** > **Web Service**
2. **Source**: Kết nối GitHub repo của bạn
3. **Runtime**: **Docker**
4. **Dockerfile Path**: `./Dockerfile` (ở root)
5. **Service Name**: `ltweb-web` (hoặc tùy chọn)
6. **Environment**:
   - `DB_HOST`: `<tên MySQL service>` (ví dụ: `ltweb-mysql`)
   - `DB_NAME`: `expense_manager`
   - `DB_USER`: `user`
   - `DB_PASS`: `password`
   - `DB_PORT`: `3306`
7. **Deploy** và chờ build xong.

## Bước 3: Test

Mở URL: `https://<web-service>.onrender.com/public/user/login.php`

Site sẽ chạy đầy đủ (login, CRUD, admin, dashboard).

Nếu lỗi DB, check logs của Web Service để debug.