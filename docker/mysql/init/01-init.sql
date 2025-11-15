-- PHProjekt Database Initialization Script
-- This script runs automatically when the MySQL container is first created

-- Set character set and collation
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Use the phprojekt database
USE phprojekt;

-- Grant all privileges to the phprojekt user
GRANT ALL PRIVILEGES ON phprojekt.* TO 'phprojekt'@'%';
GRANT ALL PRIVILEGES ON `phprojekt-mvc-test`.* TO 'phprojekt'@'%';

-- Flush privileges to ensure they take effect
FLUSH PRIVILEGES;

-- Note: Actual table creation should be done by the PHProjekt application
-- or imported from a separate SQL dump file placed in this directory
