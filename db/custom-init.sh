#!/bin/bash

# Wait for MySQL to be ready
until mysqladmin ping -hmysql -uroot -proot; do
  sleep 1
done

# Create MySQL user and grant privileges
mysql -hmysql -uroot -proot <<'EOF'
CREATE USER 'root'@'%';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EOF
