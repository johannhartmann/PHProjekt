# PHProjekt Development Guide for Claude AI

**Branch**: `claude/php-8-compatibility-011CV5jrtucDXufvzc8DwLya`
**Status**: Laminas migration complete (100% test pass rate)
**Tag**: `before-dojo-replacement` (marks completion of PHP 8 + Laminas migration)

## Project Summary

PHProjekt 6 is a collaborative project management suite with calendar, project tracking, and 12+ modules.

**Technology Stack**:
- **Backend**: PHP 8.4 + Laminas Framework (migrated from Zend Framework 1)
- **Frontend**: Dojo Toolkit 1.x (legacy, to be replaced with React)
- **Database**: MySQL 8.0 / PostgreSQL 15
- **Testing**: PHPUnit 9.6
- **Build**: Dojo build scripts (legacy)

**Current State**:
- ✅ **PHP Backend**: Fully migrated to Laminas, all tests passing (18 tests, 50 assertions)
- ✅ **Database Layer**: ActiveRecord pattern using Laminas\Db
- ⚠️ **Frontend**: Legacy Dojo code, planned migration to React SPA

---

## Directory Map

### Backend Code (`phprojekt/`)

```
phprojekt/
├── application/              # Application modules (PSR-0)
│   ├── Calendar2/           # Calendar module
│   ├── Core/                # Core MVC module
│   ├── Default/             # Default/base module
│   ├── Project/             # Project management
│   └── Timecard/            # Time tracking
│
├── library/                 # Core library code
│   ├── Phprojekt/          # Main framework classes
│   │   ├── ActiveRecord/   # ORM/Database abstraction
│   │   ├── Auth/           # Authentication
│   │   ├── Item/           # Base item/model classes
│   │   ├── Search/         # Full-text search
│   │   └── User/           # User management
│   ├── Phprojekt.php       # Application bootstrap
│   └── Cleaner.php         # Input sanitization
│
├── tests/                   # PHPUnit test suite
│   ├── UnitTests/
│   │   ├── Phprojekt/      # Core library tests
│   │   ├── Calendar2/      # Module tests
│   │   ├── Default/
│   │   └── ...
│   ├── test_fixtures.sql   # Test database fixtures
│   └── Bootstrap.php       # Test bootstrap
│
├── vendor/                  # Composer dependencies
├── configuration.php        # Application config
└── phpunit.xml             # PHPUnit configuration
```

### Frontend Code (Legacy Dojo)

```
phprojekt/htdocs/           # Public web root
├── dojo/                   # Dojo Toolkit 1.x modules
│   └── (legacy, do not modify)
├── dojo2/                  # Dojo build tools
├── phpr/                   # PHProjekt Dojo widgets
│   └── (legacy, do not modify)
├── css/                    # Stylesheets
├── img/                    # Images/assets
└── index.php              # Application entry point
```

### Static Assets (`public/`)

```
public/
└── index.php              # Minimal entry point (Docker)
```

**Note**: In development, `phprojekt/htdocs/` serves as the document root. In Docker, `public/` may be used as an alias.

---

## Commands

### Run PHP Tests

```bash
# Navigate to phprojekt directory
cd /home/user/PHProjekt/phprojekt

# Run all tests
vendor/bin/phpunit --no-coverage

# Run specific test suite
vendor/bin/phpunit --no-coverage tests/UnitTests/Phprojekt/
vendor/bin/phpunit --no-coverage tests/UnitTests/Calendar2/

# Run single test file
vendor/bin/phpunit --no-coverage tests/UnitTests/Phprojekt/ActiveRecord/AbstractTest.php

# Run specific test method
vendor/bin/phpunit --no-coverage --filter testFetchAllWithJoins tests/UnitTests/Phprojekt/ActiveRecord/AbstractTest.php
```

### Start Development Environment

**Docker Compose** (recommended):

```bash
# Start all services (app, MySQL, PostgreSQL, phpMyAdmin)
docker-compose up -d

# View logs
docker-compose logs -f app

# Stop services
docker-compose down

# Access application
# - App: http://localhost:8080
# - phpMyAdmin: http://localhost:8081
# - MySQL: localhost:3306 (user: phprojekt, pass: phprojekt)
# - PostgreSQL: localhost:5432
```

**Local Development** (requires MySQL/PostgreSQL):

```bash
# Start MySQL (if not running)
sudo service mysql start
# Or: /usr/bin/mysqld_safe --user=mysql &

# Configure database
cp phprojekt/configuration.php-dist phprojekt/configuration.php
# Edit configuration.php with your database credentials

# Run built-in PHP server
cd phprojekt/htdocs
php -S localhost:8080
```

### Run JavaScript/Dojo Build (Legacy)

```bash
# From project root
./compilejs.sh

# This executes:
# phprojekt/htdocs/dojo2/util/buildscripts/build.sh --profile timecard
```

**⚠️ DO NOT run this unless working with legacy Dojo code. New development should use React.**

---

## Coding Rules

### PHP Style

Follow **Zend Framework Coding Standard** (legacy standard, still in use):
- http://framework.zend.com/manual/en/coding-standard.html

**Key Points**:
- **Indentation**: 4 spaces (no tabs)
- **Line length**: 120 characters max
- **Naming**:
  - Classes: `PascalCase` with underscores for namespaces (e.g., `Phprojekt_ActiveRecord_Abstract`)
  - Methods: `camelCase`
  - Private/protected: prefix with `_` (e.g., `_data`, `_fetchWithJoin`)
  - Constants: `UPPER_SNAKE_CASE`
- **Braces**: Opening brace on same line for methods, next line for classes/functions
- **SQL**: Use Laminas\Db\Sql builders, not raw queries
- **Type hints**: Use PHP 8 type hints where possible (gradual adoption)

**Example**:
```php
class Phprojekt_MyClass extends Phprojekt_ActiveRecord_Abstract
{
    protected $_privateProperty;

    public function myMethod(string $param): ?array
    {
        $sql = new \Laminas\Db\Sql\Sql($this->_db);
        $select = $sql->select()->from('my_table');
        // ...
    }
}
```

### TypeScript Style (for React migration)

**We will adopt TypeScript** for all new React code:

- **Indentation**: 2 spaces
- **Line length**: 100 characters max
- **Naming**:
  - Components: `PascalCase`
  - Functions/variables: `camelCase`
  - Constants: `UPPER_SNAKE_CASE`
  - Interfaces: `PascalCase` with `I` prefix (e.g., `IProjectData`)
- **Imports**: Named imports preferred over default
- **Props**: Define interfaces for all component props
- **Hooks**: Prefix custom hooks with `use` (e.g., `useProjectData`)

**Example**:
```typescript
interface IProjectListProps {
  projectId: number;
  onSelect: (project: IProject) => void;
}

export const ProjectList: React.FC<IProjectListProps> = ({ projectId, onSelect }) => {
  const [projects, setProjects] = useState<IProject[]>([]);
  // ...
};
```

### Frontend Migration Rules

**CRITICAL RULES**:

1. ✅ **DO**: Write new React components in TypeScript
2. ✅ **DO**: Use functional components with hooks
3. ✅ **DO**: Place React code in `/app` directory (SPA)
4. ❌ **DO NOT**: Write new Dojo code
5. ❌ **DO NOT**: Modify existing Dojo code unless fixing critical bugs
6. ❌ **DO NOT**: Call Dojo widgets from React components
7. ❌ **DO NOT**: Call React components from Dojo code

**Exception**: During transition, React and Dojo will coexist. Use `<iframe>` or full-page navigation between old (Dojo) and new (React) modules.

---

## Frontend Migration Strategy

### Strangler Pattern

We're using the **strangler fig pattern** to gradually replace Dojo with React:

1. **Coexistence**: Old Dojo app continues to run
2. **New routes**: New features built in React under `/app` route
3. **Module-by-module**: Migrate one module at a time (e.g., Timecard → Projects → Calendar)
4. **Proxy routing**: Backend routes traffic to Dojo or React based on URL
5. **Data API**: Shared REST API serves both frontends
6. **Final cutover**: When all modules migrated, remove Dojo

### Architecture

```
┌─────────────────────────────────────┐
│  User Browser                       │
├─────────────────────────────────────┤
│                                     │
│  /legacy/*  →  Dojo SPA (htdocs/)  │
│  /app/*     →  React SPA (new)     │
│                                     │
└──────────────┬──────────────────────┘
               │
               ↓
┌─────────────────────────────────────┐
│  Backend (Laminas MVC)              │
│  - REST API (/api/*)                │
│  - Routing (/index.php)             │
│  - ActiveRecord Models              │
└─────────────────────────────────────┘
```

### Migration Phases

**Phase 1: Infrastructure** (Current)
- ✅ PHP 8 + Laminas migration
- ✅ Test infrastructure
- ⏸️ Set up React build pipeline
- ⏸️ Create `/app` directory structure
- ⏸️ Configure routing for `/app/*`

**Phase 2: First Module** (Next)
- Pick simplest module (e.g., Timecard or User settings)
- Build React UI consuming existing REST API
- Test with real data
- Deploy behind feature flag

**Phase 3: Incremental Migration**
- One module at a time
- Reuse components across modules
- Build shared UI library
- Gradual rollout per module

**Phase 4: Completion**
- All modules in React
- Remove Dojo dependencies
- Clean up legacy code
- Performance optimization

### Module Migration Priority

**Suggested order** (easiest → hardest):

1. **User Settings** - Simple CRUD, low complexity
2. **Timecard** - Time tracking, moderate complexity
3. **Projects** - Core module, complex relationships
4. **Calendar** - Date handling, recurring events
5. **Search** - Full-text search integration
6. **Admin/Roles** - Permissions, ACL

**Rationale**: Start with simple modules to establish patterns, then tackle complex ones.

---

## Database Notes

**Test Database**:
- Database: `phprojekt_test`
- User: `test`
- Password: `test`
- Fixtures: `phprojekt/tests/test_fixtures.sql`

**Fixture Loading**: Automatic on test run (see `tests/UnitTests/DatabaseTest.php`)

**Production Database**:
- Configured in `phprojekt/configuration.php`
- Supports MySQL 8.0+ and PostgreSQL 15+

---

## Migration Status

**Completed**:
- ✅ Zend Framework 1 → Laminas Framework
- ✅ PHP 8.4 compatibility
- ✅ ActiveRecord database layer
- ✅ All 18 core tests passing (50 assertions)
- ✅ Test fixtures converted to SQL

**In Progress**:
- ⏸️ Frontend Dojo → React migration (not started)

**Known Issues**:
- Dojo build scripts are legacy, do not rely on them for new development
- Some Zend_ class references may exist in view templates (not critical)

---

## Important Files

| File | Purpose |
|------|---------|
| `phprojekt/configuration.php` | App config (DB, cache, etc.) |
| `phprojekt/library/Phprojekt.php` | Bootstrap/initialization |
| `phprojekt/library/Phprojekt/ActiveRecord/Abstract.php` | Base model class (ORM) |
| `phprojekt/tests/test_fixtures.sql` | Test data |
| `phprojekt/composer.json` | PHP dependencies |
| `docker-compose.yml` | Docker services |
| `ZEND-MIGRATION-STATUS.md` | Laminas migration report |

---

## Quick Start for Claude

When starting work on this project:

1. **Check current branch**: Should be on `claude/php-8-compatibility-011CV5jrtucDXufvzc8DwLya`
2. **Start MySQL**: `sudo service mysql start` or use Docker
3. **Run tests**: `cd phprojekt && vendor/bin/phpunit --no-coverage`
4. **Check migration status**: Read `ZEND-MIGRATION-STATUS.md` for context
5. **For frontend work**: Remember - NO new Dojo code, React only

---

## Contact & Resources

- **Project**: http://www.phprojekt.com
- **License**: LGPL v3
- **Zend Coding Standard**: http://framework.zend.com/manual/en/coding-standard.html
- **Laminas Docs**: https://docs.laminas.dev/
- **PHPUnit Docs**: https://phpunit.de/documentation.html

---

**Last Updated**: 2025-11-16
**By**: Claude Code (Anthropic)
