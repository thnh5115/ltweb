#!/bin/bash
set -e

echo "👉 Initializing MariaDB data directory if needed..."

# Nếu chưa có thư mục hệ thống của MySQL/MariaDB thì init
if [ ! -d "/var/lib/mysql/mysql" ]; then
  echo "📦 Running mariadb-install-db..."
  mariadb-install-db --user=mysql --datadir=/var/lib/mysql > /dev/null
fi

echo "👉 Starting MariaDB (mysqld)..."
mysqld --user=mysql \
  --datadir=/var/lib/mysql \
  --socket=/run/mysqld/mysqld.sock \
  --skip-networking=0 \
  --bind-address=127.0.0.1 &

# Đợi MariaDB sẵn sàng
until mysqladmin ping -h "127.0.0.1" --silent; do
  echo "⏳ Waiting for MariaDB to be ready..."
  sleep 2
done

echo "✅ MariaDB is up. Initializing database & user..."

# Tạo DB và user (idempotent)
mysql -uroot <<EOSQL
CREATE DATABASE IF NOT EXISTS expense_manager
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON expense_manager.* TO 'user'@'localhost';
FLUSH PRIVILEGES;
EOSQL

# Nếu bảng users chưa tồn tại => import schema + seeds (chỉ lần đầu)
if ! mysql -uroot -Dexpense_manager -e "SHOW TABLES LIKE 'users';" | grep -q users; then
  echo "📥 Importing db/schema.sql..."
  mysql -uroot expense_manager < /var/www/html/db/schema.sql || echo "⚠️ schema.sql import failed"

  echo "📥 Importing db/seeds.sql..."
  mysql -uroot expense_manager < /var/www/html/db/seeds.sql || echo "⚠️ seeds.sql import failed"
else
  echo "ℹ️ Database already initialized, skipping schema & seeds."
fi

echo "🚀 Starting Apache..."
exec apache2-foreground
