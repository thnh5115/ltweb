#!/bin/bash
set -e

echo "👉 Starting MySQL..."

# Start MySQL service
service mysql start

# Đợi MySQL lên
until mysqladmin ping -h "127.0.0.1" --silent; do
  echo "⏳ Waiting for MySQL..."
  sleep 2
done

echo "✅ MySQL is up. Initializing database..."

# Tạo DB nếu chưa có
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS expense_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Tạo user app nếu chưa có
mysql -uroot <<EOSQL
CREATE USER IF NOT EXISTS 'user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON expense_manager.* TO 'user'@'localhost';
FLUSH PRIVILEGES;
EOSQL

# Nếu bảng users chưa tồn tại thì chạy schema + seeds (lần đầu)
if ! mysql -uroot -Dexpense_manager -e "SHOW TABLES LIKE 'users';" | grep -q users; then
  echo "📦 Importing schema.sql..."
  mysql -uroot expense_manager < /var/www/html/db/schema.sql

  echo "📦 Importing seeds.sql..."
  mysql -uroot expense_manager < /var/www/html/db/seeds.sql
else
  echo "ℹ️ Database already initialized, skipping schema/seeds."
fi

echo "🚀 Starting Apache..."
# Chạy Apache ở foreground để container không tắt
apache2-foreground
