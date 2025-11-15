# PHProjekt Docker Setup

This document describes how to run PHProjekt using Docker and Docker Compose.

## Prerequisites

- Docker Engine 20.10 or higher
- Docker Compose 2.0 or higher

## Quick Start

1. **Clone the repository** (if you haven't already)
   ```bash
   git clone <repository-url>
   cd PHProjekt
   ```

2. **Copy the environment file**
   ```bash
   cp .env.example .env
   ```

3. **Edit `.env` file** to customize your configuration (optional)
   ```bash
   nano .env
   ```

4. **Build and start the containers**
   ```bash
   docker-compose up -d
   ```

5. **Access the application**
   - Application: http://localhost:8080
   - phpMyAdmin: http://localhost:8081

## Services

The Docker Compose setup includes the following services:

### Application Container (`app`)
- **Image**: PHP 8.3 with Apache
- **Port**: 8080 (maps to container port 80)
- **Web Root**: `/var/www/html/phprojekt/htdocs`
- **Volumes**:
  - `./phprojekt` → `/var/www/html/phprojekt`
  - `./public` → `/var/www/html/public`
  - `phprojekt_tmp` → `/tmp/phprojekt-test`

### MySQL Database (`mysql`)
- **Image**: MySQL 8.0
- **Port**: 3306
- **Default Credentials**:
  - Database: `phprojekt`
  - Username: `phprojekt`
  - Password: `phprojekt`
  - Root Password: `root`

### MySQL Test Database (`mysql_test`)
- **Image**: MySQL 8.0
- **Port**: 3307
- **Purpose**: Dedicated database for running tests
- **Database**: `phprojekt-mvc-test`

### PostgreSQL Database (`postgres`)
- **Image**: PostgreSQL 15
- **Port**: 5432
- **Default Credentials**:
  - Database: `phprojekt`
  - Username: `phprojekt`
  - Password: `phprojekt`

### PostgreSQL Test Database (`postgres_test`)
- **Image**: PostgreSQL 15
- **Port**: 5433
- **Purpose**: Dedicated database for running tests
- **Database**: `phprojekt-mvc-testing`

### phpMyAdmin (`phpmyadmin`)
- **Image**: phpMyAdmin (latest)
- **Port**: 8081
- **Purpose**: Web interface for MySQL administration

## Common Commands

### Start all services
```bash
docker-compose up -d
```

### Stop all services
```bash
docker-compose down
```

### View logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f mysql
```

### Restart a service
```bash
docker-compose restart app
```

### Execute commands in the app container
```bash
# Run Composer
docker-compose exec app composer install

# Run PHPUnit tests
docker-compose exec app vendor/bin/phpunit -c phprojekt/tests/UnitTests/

# Access bash shell
docker-compose exec app bash
```

### Install/Update dependencies
```bash
docker-compose exec app sh -c "cd phprojekt && composer install"
```

### Database operations
```bash
# Import SQL dump
docker-compose exec -T mysql mysql -u phprojekt -pphprojekt phprojekt < dump.sql

# Export database
docker-compose exec mysql mysqldump -u phprojekt -pphprojekt phprojekt > dump.sql

# Access MySQL CLI
docker-compose exec mysql mysql -u phprojekt -pphprojekt phprojekt
```

## Running Tests

### Run all tests with MySQL
```bash
docker-compose exec app sh -c "cd phprojekt && vendor/bin/phpunit --configuration phpunit.xml"
```

### Run specific test
```bash
docker-compose exec app sh -c "cd phprojekt && vendor/bin/phpunit --filter testMethodName"
```

### Run tests with PostgreSQL
Modify `phprojekt/tests/UnitTests/configuration.php` to use PostgreSQL settings, then run:
```bash
docker-compose exec app sh -c "cd phprojekt && vendor/bin/phpunit --configuration phpunit.xml"
```

## Volumes

The setup uses several Docker volumes:

- `mysql_data`: Persistent MySQL database storage
- `mysql_test_data`: Persistent MySQL test database storage
- `postgres_data`: Persistent PostgreSQL database storage
- `postgres_test_data`: Persistent PostgreSQL test database storage
- `phprojekt_tmp`: Application temporary files

### Remove all volumes (WARNING: destroys all data)
```bash
docker-compose down -v
```

## Environment Variables

Edit `.env` file to customize:

```bash
# Database credentials
DB_DATABASE=phprojekt
DB_USERNAME=phprojekt
DB_PASSWORD=phprojekt
MYSQL_ROOT_PASSWORD=root

# PostgreSQL credentials
POSTGRES_DB=phprojekt
POSTGRES_USER=phprojekt
POSTGRES_PASSWORD=phprojekt

# PHP settings
PHP_DISPLAY_ERRORS=On
PHP_ERROR_REPORTING=E_ALL

# Application environment
APP_ENV=development
APP_DEBUG=true
```

## Troubleshooting

### Container won't start
```bash
# Check container logs
docker-compose logs app

# Check container status
docker-compose ps
```

### Permission issues
```bash
# Fix permissions in the app container
docker-compose exec app chown -R www-data:www-data /var/www/html
docker-compose exec app chmod -R 755 /var/www/html
```

### Database connection issues
```bash
# Verify database is running
docker-compose ps mysql

# Check database logs
docker-compose logs mysql

# Test connection from app container
docker-compose exec app ping mysql
```

### Reset everything
```bash
# Stop and remove containers, networks, and volumes
docker-compose down -v

# Rebuild and start fresh
docker-compose up -d --build
```

## Development Workflow

1. **Make code changes** in your local `phprojekt/` directory
2. **Changes are automatically reflected** due to volume mounting
3. **Restart Apache** if needed:
   ```bash
   docker-compose restart app
   ```

## Production Considerations

For production deployment:

1. **Update `.env` file**:
   - Set strong database passwords
   - Set `APP_ENV=production`
   - Set `APP_DEBUG=false`
   - Set `PHP_DISPLAY_ERRORS=Off`

2. **Use specific image tags** instead of `latest`

3. **Enable SSL/TLS** (add reverse proxy like Nginx or Traefik)

4. **Regular backups**:
   ```bash
   # Backup MySQL
   docker-compose exec mysql mysqldump -u root -proot --all-databases > backup.sql

   # Backup volumes
   docker run --rm -v phprojekt_mysql_data:/data -v $(pwd):/backup alpine tar czf /backup/mysql-backup.tar.gz /data
   ```

5. **Monitor logs and resources**

## Additional Information

### PHP Extensions Installed
- PDO (MySQL, PostgreSQL, SQLite)
- MySQLi
- mbstring
- exif
- pcntl
- bcmath
- gd
- zip
- intl
- opcache

### Apache Modules Enabled
- rewrite
- headers

### PHP Configuration
- Memory limit: 256M
- Upload max filesize: 64M
- Post max size: 64M
- Max execution time: 300s
- Timezone: UTC

## Support

For issues related to:
- **Docker setup**: Check this documentation and Docker logs
- **PHProjekt application**: See main README.md
- **Database issues**: Check database-specific logs
