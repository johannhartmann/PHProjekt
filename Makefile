.PHONY: help build up down restart logs shell test composer clean

# Default target
help:
	@echo "PHProjekt Docker Commands"
	@echo "========================="
	@echo "make build          - Build Docker images"
	@echo "make up             - Start all containers"
	@echo "make down           - Stop all containers"
	@echo "make restart        - Restart all containers"
	@echo "make logs           - View logs (all services)"
	@echo "make logs-app       - View application logs"
	@echo "make logs-mysql     - View MySQL logs"
	@echo "make shell          - Access application shell"
	@echo "make shell-mysql    - Access MySQL CLI"
	@echo "make test           - Run PHPUnit tests"
	@echo "make composer       - Run composer install"
	@echo "make composer-update - Run composer update"
	@echo "make clean          - Stop containers and remove volumes"
	@echo "make reset          - Complete reset (clean + build + up)"
	@echo "make db-backup      - Backup MySQL database"
	@echo "make db-restore     - Restore MySQL database from backup.sql"
	@echo "make permissions    - Fix file permissions"

# Build Docker images
build:
	docker-compose build

# Start containers
up:
	docker-compose up -d

# Stop containers
down:
	docker-compose down

# Restart containers
restart:
	docker-compose restart

# View logs
logs:
	docker-compose logs -f

logs-app:
	docker-compose logs -f app

logs-mysql:
	docker-compose logs -f mysql

# Access shells
shell:
	docker-compose exec app bash

shell-mysql:
	docker-compose exec mysql mysql -u phprojekt -pphprojekt phprojekt

# Run tests
test:
	docker-compose exec app sh -c "cd phprojekt && vendor/bin/phpunit --configuration phpunit.xml"

test-filter:
	@read -p "Enter test filter: " filter; \
	docker-compose exec app sh -c "cd phprojekt && vendor/bin/phpunit --configuration phpunit.xml --filter $$filter"

# Composer commands
composer:
	docker-compose exec app sh -c "cd phprojekt && composer install"

composer-update:
	docker-compose exec app sh -c "cd phprojekt && composer update"

composer-dump:
	docker-compose exec app sh -c "cd phprojekt && composer dump-autoload"

# Database operations
db-backup:
	docker-compose exec mysql mysqldump -u phprojekt -pphprojekt phprojekt > backup.sql
	@echo "Database backed up to backup.sql"

db-restore:
	docker-compose exec -T mysql mysql -u phprojekt -pphprojekt phprojekt < backup.sql
	@echo "Database restored from backup.sql"

db-shell:
	docker-compose exec mysql mysql -u root -proot

# Clean up
clean:
	docker-compose down -v

# Complete reset
reset: clean build up
	@echo "System reset complete!"

# Fix permissions
permissions:
	docker-compose exec app chown -R www-data:www-data /var/www/html
	docker-compose exec app chmod -R 755 /var/www/html

# Check status
status:
	docker-compose ps

# Show container resource usage
stats:
	docker stats --no-stream
