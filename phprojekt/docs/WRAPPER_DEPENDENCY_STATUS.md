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

**Used by Helper Classes**
- `application/Default/Helpers/Save.php` - Throws Zend_Controller_Action_Exception (18 occurrences)
- `application/Default/Helpers/Delete.php` - Throws Zend_Controller_Action_Exception (4 occurrences)
- `application/Default/Helpers/Upload.php` - Throws Zend_Controller_Action_Exception (3 occurrences)

**Used by Tests**
- `tests/UnitTests/FrontInit.php` - Creates Zend_Controller_Front instance
- `tests/UnitTests/Bootstrap.php` - Front Controller setup
- Various model and controller tests

**Used by Models (Legacy Pattern)**
- `application/Calendar2/Models/Calendar2.php` - Creates Zend_Controller_Request_Http

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

### Helpers: Using Wrapper Exceptions ⚠️
- **Status**: Helper classes throw Zend_Controller_Action_Exception
- **Dependencies**: 25+ occurrences across Save/Delete/Upload helpers
- **Impact**: Cannot remove exception wrappers without refactoring helpers
- **Future Work**: Migrate to Laminas exceptions or HTTP exceptions

## Next Steps for Complete Wrapper Removal

To fully remove Zend/Controller wrappers, the following work is required:

1. **Refactor Application Bootstrap** (library/Phprojekt.php)
   - Replace Front Controller with Laminas MVC Application
   - Migrate to Laminas ModuleManager
   - Update request/response handling
   - Refactor error handling plugin
   - Update view renderer setup

2. **Refactor Helper Classes**
   - Replace Zend_Controller_Action_Exception with Laminas\Http\Response exceptions
   - Update exception handling in controllers

3. **Refactor Test Infrastructure**
   - Update test bootstrap to use Laminas patterns
   - Migrate controller tests to Laminas testing framework

4. **Refactor Setup System**
   - Update htdocs/Setup to use Laminas patterns
   - Remove ZF1 dependencies from setup flow

5. **Update Models**
   - Remove controller dependencies from models
   - Use proper dependency injection

## Summary

**Achievements:**
- ✓ All 24 controllers refactored to native Laminas
- ✓ 5,277 lines of legacy controller code removed
- ✓ Unused wrapper classes deleted (Phprojekt_RestController)
- ✓ Native Laminas patterns throughout controller layer

**Remaining Work:**
- Bootstrap infrastructure still requires Zend/Controller wrappers
- Helper classes use wrapper exceptions
- Test infrastructure uses wrapper patterns
- Estimated effort: 2-3 weeks for complete wrapper removal

**Recommendation:**
The controller refactoring is complete and provides significant benefits. The remaining wrapper dependencies are in infrastructure layers that would require a major refactoring effort. These can be addressed in a future phase when time permits.
