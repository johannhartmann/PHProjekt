# Laminas MVC Refactoring Pattern

## Overview

This document shows the proper way to refactor PHProjekt from ZF1 wrappers to native Laminas MVC.

## Example: ErrorController Refactoring

We've completed a full refactoring of the Default module's ErrorController as a reference implementation.

### What Changed

#### 1. Controller Refactoring

**Before (ZF1 wrapper approach):**
```php
class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $error = $this->_getParam('error_handler');
        $this->getResponse()->setHttpResponseCode(404);
        $this->view->error = $viewerror;
    }
}
```

**After (Native Laminas MVC):**
```php
namespace Application\Default\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;

class ErrorController extends AbstractActionController
{
    public function errorAction()
    {
        $mvcEvent = $this->getEvent();
        $exception = $mvcEvent->getParam('exception');

        $response = $this->getResponse();
        $response->setStatusCode($statusCode);

        // Return proper Laminas response
        if ($this->getRequest()->isXmlHttpRequest()) {
            return new JsonModel(['error' => $message]);
        }

        return new ViewModel(['message' => $message]);
    }
}
```

#### 2. View Templates

**Before (ZF1):**
```php
<html><body>
ERROR: <?php echo $this->error; ?>
</body></html>
```

**After (Proper Laminas views):**
```php
<?php
// Views/error/index.phtml
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Error <?php echo $statusCode; ?></title>
</head>
<body>
    <h1>An Error Occurred</h1>
    <div class="error-message">
        <?php echo $this->escapeHtml($message); ?>
    </div>
</body>
</html>
```

#### 3. Module Configuration

**module.config.php:**
```php
return [
    'controllers' => [
        'factories' => [
            Controller\ErrorController::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'display_exceptions' => true,
        'exception_template' => 'error/index',
        'not_found_template' => 'error/404',
        'template_map' => [
            'error/index' => __DIR__ . '/../Views/error/index.phtml',
            'error/404' => __DIR__ . '/../Views/error/404.phtml',
        ],
    ],
];
```

### Key Differences

| Aspect | ZF1 Wrapper | Native Laminas |
|--------|-------------|----------------|
| **Base Class** | `Zend_Controller_Action` | `AbstractActionController` |
| **Request Access** | `$this->getRequest()` through wrapper | `$this->getRequest()` native |
| **Response** | `Zend_Controller_Response_Http` wrapper | Native `Laminas\Http\Response` |
| **View Assignment** | `$this->view->var = $value` | Return `new ViewModel(['var' => $value])` |
| **JSON Response** | `$this->_helper->json($data)` | Return `new JsonModel($data)` |
| **Params** | `$this->_getParam()` | `$this->params()->fromRoute()` |
| **Error Handling** | Through wrapper plugins | Native MVC event system |

### Benefits of Native Approach

1. **No Wrapper Overhead** - Direct Laminas API calls
2. **Type Safety** - Proper PHP 8 type hints
3. **Modern Patterns** - Laminas best practices
4. **Better Testing** - Standard Laminas test tools
5. **Future Proof** - No deprecated code
6. **Performance** - Eliminates double abstraction layer

### Wrapper Classes That Can Be Removed

Once ALL controllers are refactored, these wrappers become obsolete:

- ❌ `Zend_Controller_Action`
- ❌ `Zend_Controller_Action_HelperBroker`
- ❌ `Zend_Controller_Action_Helper_*`
- ❌ `Zend_Controller_Request_Http`
- ❌ `Zend_Controller_Response_Http`
- ❌ `Zend_Controller_Plugin_*`
- ❌ `Zend_Controller_Front`
- ❌ `Zend_View` (use Laminas\View\Renderer\PhpRenderer)

### Refactoring Checklist

For each controller:

- [ ] Create new file in `Controller/` directory (not `Controllers/`)
- [ ] Add proper namespace: `Application\{Module}\Controller`
- [ ] Extend `AbstractActionController`
- [ ] Replace `$this->view->var` with `return new ViewModel(['var' => $value])`
- [ ] Replace `$this->_helper->json()` with `return new JsonModel()`
- [ ] Update all view templates to use proper Laminas helpers
- [ ] Add controller to module.config.php factories
- [ ] Test thoroughly
- [ ] Delete old ZF1 controller file

### Models Refactoring Pattern

**Before (ZF1/Phprojekt pattern):**
```php
class User extends Phprojekt_ActiveRecord_Abstract
{
    public function find($id)
    {
        // Uses Zend_Db_Table_Abstract
    }
}
```

**After (Laminas TableGateway):**
```php
namespace Application\Models;

use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\Sql\Select;

class UserTable extends TableGateway
{
    protected $table = 'user';

    public function find($id)
    {
        return $this->select(['id' => $id])->current();
    }

    public function findAll()
    {
        return $this->select();
    }
}
```

### Database Layer Refactoring

**Before (using wrappers):**
```php
$db = Zend_Db::factory($config);  // Returns wrapper
$select = $db->select()->from('table');
$result = $db->query($select);
$rows = $result->fetchAll();
```

**After (native Laminas):**
```php
$adapter = new \Laminas\Db\Adapter\Adapter($config);
$sql = new \Laminas\Db\Sql\Sql($adapter);
$select = $sql->select('table');
$statement = $sql->prepareStatementForSqlObject($select);
$result = $statement->execute();
$rows = iterator_to_array($result);
```

Or even better, use TableGateway:
```php
$tableGateway = new TableGateway('table', $adapter);
$rows = $tableGateway->select();
```

## Migration Strategy

### Phase 1: Module-by-Module (RECOMMENDED)
1. Start with simple modules (Default, Error handlers)
2. Refactor all controllers in one module
3. Refactor all models in that module
4. Test thoroughly
5. Move to next module
6. Remove wrappers only when ALL modules done

### Phase 2: Layer-by-Layer (ALTERNATIVE)
1. Refactor all controllers first (keep model wrappers)
2. Then refactor all models
3. Then refactor configuration
4. Remove wrappers last

### Phase 3: Incremental (SLOWEST BUT SAFEST)
1. Refactor one controller at a time
2. Keep wrappers for everything else
3. Gradually reduce wrapper usage
4. Remove wrappers after 100% coverage

## Current Status

### ✅ Completed
- **Default/Controller/ErrorController** - Fully refactored to Laminas MVC
- **Error view templates** - Native Laminas templates created
- **Module configuration** - Proper Laminas config in place

### 🔄 To Do
- [ ] Refactor remaining Default module controllers (Login, Index, Search, Tag, Js)
- [ ] Refactor Core module controllers
- [ ] Refactor Project module controllers
- [ ] Refactor Timecard module controllers
- [ ] Refactor Calendar2 module controllers
- [ ] Convert models to TableGateway pattern
- [ ] Remove all 37+ wrapper classes
- [ ] Update tests to use Laminas test harness

## Estimated Effort

- **Per Controller**: 30-60 minutes
- **Per Model**: 45-90 minutes
- **Total Controllers**: 8 × 45min = 6 hours
- **Total Models**: 18 × 60min = 18 hours
- **Configuration & Testing**: 8 hours
- **Total Estimate**: 32-40 hours

## Testing

Test each refactored component:

```bash
# Run specific test
vendor/bin/phpunit --filter ErrorControllerTest

# Run module tests
vendor/bin/phpunit tests/UnitTests/Default/

# Run all tests
vendor/bin/phpunit
```

## Conclusion

The ErrorController refactoring demonstrates that proper Laminas MVC usage **eliminates the need for wrapper classes** entirely. Each controller refactored reduces technical debt and moves the application toward a modern, maintainable architecture.

**Next steps**: Apply this pattern to remaining controllers, starting with the simplest ones first.
