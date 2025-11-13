# Full Laminas Refactoring Sprint Plan

**Goal:** Eliminate all 37+ wrapper classes and refactor PHProjekt to use native Laminas MVC.

**Duration:** 1 week (32-40 hours)

**Approach:** Systematic module-by-module refactoring

---

## Sprint Overview

### Current State
- ✅ ErrorController refactored (1/8 controllers)
- ⬜ 7 controllers remaining  
- ⬜ 18 models using ActiveRecord
- ⬜ 37+ wrapper classes (3,100+ lines)
- ⬜ ZF1-style bootstrap

### Target State
- ✅ All controllers using AbstractActionController
- ✅ All models using TableGateway pattern
- ✅ 0 wrapper classes
- ✅ Native Laminas Application bootstrap
- ✅ Service Manager configured
- ✅ Tests passing

---

## Phase Breakdown

### Phase 1: Default Module Controllers (6 hours)
**Files:** 5 controllers
**Status:** 1/6 complete (ErrorController done)

- [x] ErrorController → Application\Default\Controller\ErrorController
- [ ] IndexController → Application\Default\Controller\IndexController
- [ ] LoginController → Application\Default\Controller\LoginController  
- [ ] SearchController → Application\Default\Controller\SearchController
- [ ] TagController → Application\Default\Controller\TagController
- [ ] JsController → Application\Default\Controller\JsController

**Estimated:** 1 hour per controller

### Phase 2: Core Module Controllers (9 hours)
**Files:** 9 controllers

- [ ] IndexController
- [ ] UserController
- [ ] RoleController
- [ ] ModuleController
- [ ] SettingController
- [ ] TabController
- [ ] ModuleDesignerController
- [ ] AdministrationController
- [ ] UpgradeController
- [ ] HistoryController

**Estimated:** 1 hour per controller

### Phase 3: Project & Other Modules (4 hours)
**Files:** 4 controllers

- [ ] Project/IndexController
- [ ] Project/ProjectController
- [ ] Timecard controllers
- [ ] Template controllers

**Estimated:** 1 hour per controller

### Phase 4: Models to TableGateway (12 hours)
**Files:** 18 models

Current pattern:
```php
class User extends Phprojekt_ActiveRecord_Abstract
```

New pattern:
```php
class UserTable extends TableGateway
```

Models to refactor:
- User, Role, Module, Project, Setting
- Configuration, Tab, Tag, Search
- DatabaseManager, Groups, Item
- ModelInformation, Notification
- Rights, Tree, Upload, UserSetting

**Estimated:** 40 minutes per model

### Phase 5: Bootstrap Refactoring (4 hours)
**Files:** 
- library/Phprojekt.php → Application\Bootstrap
- htdocs/index.php → Modern application bootstrap
- Create Application\Module class
- Set up ServiceManager configuration

**Pattern:**
```php
// Old: Phprojekt::getInstance()->run()
// New: Application::init($config)->run()
```

### Phase 6: Service Manager Setup (3 hours)
**Tasks:**
- Configure factories for all services
- Set up database adapter factory
- Configure logger service
- Set up cache service
- Configure authentication service

**File:** config/services.config.php

### Phase 7: Configuration Migration (2 hours)
**Convert:**
- Zend_Config_Ini → PHP arrays
- Update all config access points
- Migrate configuration.php to config/autoload/

### Phase 8: Remove Wrappers (1 hour)
**Delete 37+ files:**
- library/Zend/Controller/*
- library/Zend/Db/Adapter/*
- library/Zend/Db/Select.php
- library/Zend/Db/Statement.php
- library/Zend/View.php
- library/Zend/Auth.php
- library/Zend/Cache.php
- library/Zend/Config/*
- library/Zend/Session.php
- library/Zend/Registry.php
- All other Zend_* wrappers

**Verify:** No references remain in codebase

### Phase 9: Test Suite Updates (4 hours)
**Tasks:**
- Update test bootstrap
- Fix test database setup
- Update controller tests
- Update model tests  
- Ensure all tests pass

### Phase 10: Documentation & Handoff (2 hours)
**Deliverables:**
- Updated README
- Architecture documentation
- API documentation
- Deployment guide

---

## Progress Tracking

### Day 1: Default Module (8 hours)
- Morning: IndexController, LoginController, SearchController
- Afternoon: TagController, JsController, testing

### Day 2: Core Module Part 1 (8 hours)
- Morning: IndexController, UserController, RoleController, ModuleController
- Afternoon: SettingController, TabController, testing

### Day 3: Core Module Part 2 + Others (8 hours)
- Morning: ModuleDesignerController, AdministrationController, UpgradeController
- Afternoon: HistoryController, Project controllers, testing

### Day 4: Models (8 hours)
- Morning: User, Role, Module, Project, Setting, Configuration
- Afternoon: Tab, Tag, Search, DatabaseManager, Groups, Item

### Day 5: Models + Bootstrap (8 hours)
- Morning: Remaining models, testing
- Afternoon: Bootstrap refactoring, ServiceManager setup

### Weekend: Testing & Cleanup
- Saturday: Configuration migration, wrapper removal, testing
- Sunday: Documentation, final testing, commit

---

## Risk Mitigation

### Risks:
1. Breaking existing functionality
2. Test failures
3. Missing dependencies
4. Authentication/session issues

### Mitigation:
1. Refactor one controller at a time, test immediately
2. Keep git history clean with atomic commits
3. Run test suite after each phase
4. Keep wrapper branch available for rollback

---

## Success Criteria

- [ ] All controllers extend AbstractActionController
- [ ] All models use TableGateway
- [ ] 0 Zend_* wrapper classes remain
- [ ] All tests passing
- [ ] Application runs without errors
- [ ] Performance improved (measured)
- [ ] Code coverage maintained
- [ ] Documentation complete

---

## Rollback Plan

If critical issues arise:
1. Git branch with wrappers remains: `claude/php-8-compatibility-wrappers`
2. Can cherry-pick working controllers
3. Hybrid approach: Some refactored, some wrapped

---

## Let's Begin! 🚀

Starting with Default module controllers...
