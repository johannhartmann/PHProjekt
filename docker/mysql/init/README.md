# MySQL Initialization Scripts

This directory contains SQL scripts that are automatically executed when the MySQL container is first created.

## How it works

- Scripts in this directory are executed in alphabetical order
- Files are executed only once during the initial database creation
- Supported file extensions: `.sql`, `.sql.gz`, `.sh`

## Adding custom initialization scripts

1. Create a new SQL file with a numeric prefix (e.g., `02-my-script.sql`)
2. Place it in this directory
3. Rebuild the containers if they already exist:
   ```bash
   docker-compose down -v
   docker-compose up -d
   ```

## Example: Importing a database dump

If you have an existing PHProjekt database dump, place it here as `99-import-data.sql`:

```bash
# Place your dump file
cp /path/to/dump.sql docker/mysql/init/99-import-data.sql

# Recreate containers
docker-compose down -v
docker-compose up -d
```

## Notes

- These scripts run as the MySQL root user
- The database specified in `MYSQL_DATABASE` environment variable is created automatically
- Character set is configured for UTF-8 (utf8mb4)
