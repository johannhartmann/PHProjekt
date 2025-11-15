#!/bin/bash
# PHProjekt Docker Quick Start Script

set -e

echo "=================================="
echo "PHProjekt Docker Quick Start"
echo "=================================="
echo ""

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "Error: Docker is not installed."
    echo "Please install Docker from https://docs.docker.com/get-docker/"
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "Error: Docker Compose is not installed."
    echo "Please install Docker Compose from https://docs.docker.com/compose/install/"
    exit 1
fi

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
    echo "✓ .env file created"
    echo ""
    echo "You can edit .env to customize your configuration."
    echo ""
else
    echo "✓ .env file already exists"
fi

# Stop any running containers
echo ""
echo "Stopping any running containers..."
docker-compose down 2>/dev/null || true

# Build images
echo ""
echo "Building Docker images..."
docker-compose build

# Start containers
echo ""
echo "Starting containers..."
docker-compose up -d

# Wait for MySQL to be ready
echo ""
echo "Waiting for MySQL to be ready..."
for i in {1..30}; do
    if docker-compose exec -T mysql mysqladmin ping -h localhost -u root -proot &> /dev/null; then
        echo "✓ MySQL is ready"
        break
    fi
    echo -n "."
    sleep 2
done

# Install Composer dependencies
echo ""
echo "Installing Composer dependencies..."
docker-compose exec -T app sh -c "cd phprojekt && composer install --no-interaction"

# Show status
echo ""
echo "=================================="
echo "PHProjekt is now running!"
echo "=================================="
echo ""
echo "Access points:"
echo "  - Application:  http://localhost:8080"
echo "  - phpMyAdmin:   http://localhost:8081"
echo ""
echo "Database credentials:"
echo "  - MySQL Host:   localhost:3306 (or 'mysql' from within containers)"
echo "  - Database:     phprojekt"
echo "  - Username:     phprojekt"
echo "  - Password:     phprojekt"
echo ""
echo "Useful commands:"
echo "  - View logs:         docker-compose logs -f"
echo "  - Stop containers:   docker-compose down"
echo "  - Restart:           docker-compose restart"
echo "  - Run tests:         make test"
echo "  - Access shell:      make shell"
echo ""
echo "For more commands, run: make help"
echo ""
