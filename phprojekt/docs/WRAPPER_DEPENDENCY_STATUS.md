# Wrapper Dependency Status

## Overview

This document tracks the status of ZF1 wrapper classes during the Laminas migration.

## Completed Refactoring (November 2024)

### Controllers - 100% Native Laminas ✓

All 24 application controllers have been refactored to native Laminas MVC:

**Default Module (6 controllers)**
- IndexController - Extends Laminas AbstractActionController
- LoginController - Extends Laminas AbstractActionController
- ErrorController - Extends Laminas AbstractActionController
- SearchController - Extends Laminas AbstractActionController
- TagController - Extends Laminas AbstractActionController
- JsController - Extends Laminas AbstractActionController

**Core Module (10 controllers)**
- IndexController - Extends Default\Controller\IndexController
- TabController, RoleController, HistoryController, etc. - All extend Core\Controller\IndexController
- All use native Laminas patterns (JsonModel, ViewModel, params(), etc.)

**Project Module (2 controllers)**
- IndexController - Extends Default\Controller\IndexController
- ProjectController - Extends Laminas AbstractRestfulController

**Timecard Module (3 controllers)**
- IndexController - Extends Default\Controller\IndexController
- TimecardController - Extends Laminas AbstractRestfulController
- CaldavController - Extends Default\Controller\IndexController

**Calendar2 Module (3 controllers)**
- IndexController - Extends Default\Controller\IndexController
- Calendar2Controller - Extends Laminas AbstractRestfulController
- CaldavController - Extends Default\Controller\IndexController

### Deleted Wrapper Classes ✓

1. **Old ZF1 Controllers** (24 files, 5,277 lines removed)
   - application/Default/Controllers/* (6 files)
   - application/Core/Controllers/* (10 files)
   - application/Project/Controllers/* (2 files)
   - application/Timecard/Controllers/* (3 files)
   - application/Calendar2/Controllers/* (3 files)

2. **Phprojekt_RestController** (1 file removed)
   - No longer used - all REST controllers now extend Laminas AbstractRestfulController

### Exceptions - Native Laminas ✓

**Created Application\Default\Exception\HttpException**
- Modern replacement for Zend_Controller_Action_Exception
- Extends RuntimeException for PSR compliance
- Maintains HTTP status code support (400, 401, 403, 404, 422, etc.)
- Compatible with Laminas MVC error handling
- Zero ZF1 dependencies

**Migrated Exception Usage (26 occurrences)**
- application/Default/Helpers/Save.php - Now throws HttpException
- application/Default/Helpers/Delete.php - Now throws HttpException
- application/Default/Helpers/Upload.php - Now throws HttpException
- application/Calendar2/Models/Calendar2.php - Now throws HttpException
- tests/UnitTests/Default/Controllers/IndexControllerTest.php - Now catches HttpException

## Remaining Wrapper Dependencies

### Zend/Controller/* - REQUIRED by Bootstrap Infrastructure

The following Zend/Controller wrapper classes **CANNOT be deleted** without major bootstrap refactoring:

**Used by library/Phprojekt.php (Application Bootstrap)**
- `Zend_Controller_Front` - Front Controller pattern, request dispatching
- `Zend_Controller_Request_Http` - Request handling for webpath detection
- `Zend_Controller_Response_Http` - Response handling for redirects/errors
- `Zend_Controller_Action_Helper_ViewRenderer` - View rendering setup
- `Zend_Controller_Action_HelperBroker` - Helper registration
- `Zend_Controller_Plugin_ErrorHandler` - Error handling plugin
- `Zend_Controller_Action_Exception` - Exception handling

**Used by Tests**
- `tests/UnitTests/FrontInit.php` - Creates Zend_Controller_Front instance
- `tests/UnitTests/Bootstrap.php` - Front Controller setup
- Various model and controller tests

**Used by Setup System**
- `htdocs/Setup/Controllers/IndexController.php` - Uses ZF1 patterns
- `htdocs/Setup/Models/Setup.php` - Creates Zend_Controller_Request_Http

## Impact Assessment

### Controllers: Migration Complete ✓
- **Status**: 100% refactored to native Laminas
- **Dependencies**: Zero Zend_Controller_* wrapper dependencies in controllers
- **Benefits**:
  - Clean separation of concerns
  - Native Laminas service manager integration
  - Proper HTTP status codes and response handling
  - Modern PSR-4 autoloading
  - Ready for PHP 8.4

### Bootstrap: Still Using Wrappers ⚠️
- **Status**: Still uses ZF1 wrapper infrastructure
- **Dependencies**: Heavy reliance on Zend_Controller_Front pattern
- **Risk**: Cannot remove Zend/Controller wrappers without breaking application
- **Future Work**: Full bootstrap refactoring required (major undertaking)

### Helpers: Migration Complete ✓
- **Status**: Helper classes now throw native HttpException
- **Dependencies**: Zero wrapper exception dependencies
- **Benefits**: Clean, PSR-compliant exception handling with proper HTTP status codes
- **Impact**: 26 wrapper exception calls eliminated from application layer

## Next Steps for Complete Wrapper Removal

To fully remove Zend/Controller wrappers, the following work is required:

1. **Refactor Application Bootstrap** (library/Phprojekt.php)
   - Replace Front Controller with Laminas MVC Application
   - Migrate to Laminas ModuleManager
   - Update request/response handling
   - Refactor error handling plugin
   - Update view renderer setup

2. **Refactor Test Infrastructure**
   - Update test bootstrap to use Laminas patterns
   - Migrate controller tests to Laminas testing framework

3. **Refactor Setup System**
   - Update htdocs/Setup to use Laminas patterns
   - Remove ZF1 dependencies from setup flow

4. **Update Models**
   - Remove controller dependencies from models
   - Use proper dependency injection

## Summary

**Achievements:**
- ✓ All 24 controllers refactored to native Laminas
- ✓ 5,277 lines of legacy controller code removed
- ✓ Unused wrapper classes deleted (Phprojekt_RestController)
- ✓ Native Laminas patterns throughout controller layer
- ✓ Exception handling migrated to native HttpException (26 occurrences)
- ✓ Helper classes (Save, Delete, Upload) now ZF1-independent
- ✓ Model exception handling migrated (Calendar2)

**Remaining Work:**
- Bootstrap infrastructure still requires Zend/Controller wrappers
- Test infrastructure uses wrapper patterns
- Setup system uses ZF1 patterns
- Estimated effort: 2-3 weeks for complete wrapper removal

**Recommendation:**
The controller refactoring is complete and provides significant benefits. The remaining wrapper dependencies are in infrastructure layers that would require a major refactoring effort. These can be addressed in a future phase when time permits.
