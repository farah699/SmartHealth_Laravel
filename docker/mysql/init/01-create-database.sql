CREATE DATABASE IF NOT EXISTS SmartHealth CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'smarthealth'@'%' IDENTIFIED BY 'smarthealth_password';
GRANT ALL PRIVILEGES ON SmartHealth.* TO 'smarthealth'@'%';
FLUSH PRIVILEGES;