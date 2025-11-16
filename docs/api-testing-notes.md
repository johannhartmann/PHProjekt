# API Testing Notes

**Date:** 2025-11-16
**Status:** Unit Tests Passing, Integration Tests Require Auth Setup

---

## Unit Test Results ✅

**Frontend Tests (Vitest):**
- **Total**: 38 tests
- **Passed**: 38 ✅
- **Failed**: 0
- **Duration**: 2.63s

**Test Coverage:**
- HTTP client core (GET, POST)
- Query parameter building
- Error handling (ApiError, NetworkError)
- Timeout handling
- CSRF token support
- All API modules (timecard, project, tag, search, system)
- TypeScript type guards

**Test File**:
`frontend-react/src/api/__tests__/client.test.ts` (468 lines)
`frontend-react/src/api/__tests__/types.test.ts` (89 lines)

---

## Backend Tests (PHPUnit) ⚠️

**Status**: Cannot run - MySQL not configured in test environment

**Note**: No new PHP controllers were added as part of this task. We're using existing JSON endpoints that are already tested. Once MySQL is configured, existing PHPUnit tests should continue to pass.

---

## Endpoint Integration Testing ⚠️

**Status**: Requires authentication setup

**Current Issue**: JSON endpoints return 404 or HTML when accessed without proper session/authentication.

**Example**:
```bash
curl "http://localhost:8080/index.php/Project/index/jsonTree"
# Returns: 404 HTML page
```

**Root Cause**:
- Endpoints require authenticated session
- Laminas routing may need configuration for legacy `/index.php/Module/Controller/action` URLs

**Verified via Source Code**:
The following endpoints exist and return correct JSON structure (verified by reading PHP source):

### ✅ Timecard Endpoints

**jsonDayList** - `Timecard/IndexController.php:52`
```php
public function jsonDayListAction()
{
    $date = \Cleaner::sanitize('date', $this->params()->fromQuery('date', date("Y-m-d")));
    $records = $this->getModelObject()->getDayRecords($date);

    \Phprojekt_Converter_Json::echoConvert($records, \Phprojekt_ModelInformation_Default::ORDERING_FORM);
    return new JsonModel([]);
}
```

**Response Format** - `Timecard/Models/Timecard.php:384`
```php
return array('data' => $datas); // Where $datas is array of bookings
```

**TypeScript Type** ✅ **MATCHES**:
```typescript
interface TimecardDayListResponse {
  data: TimecardBooking[];
}
```

### ✅ Project Endpoints

**jsonTree** - `Default/IndexController.php:174`
```php
public function jsonTreeAction()
{
    $model = new \Project_Models_Project();
    $tree = new \Phprojekt_Tree_Node_Database($model, 1);

    \Phprojekt_Converter_Json::echoConvert($tree->setup());

    return new JsonModel([]);
}
```

**TypeScript Type** ✅ **MATCHES**:
```typescript
interface ProjectTreeNode {
  id: number;
  title: string;
  parent: number | null;
  path: string;
  children?: ProjectTreeNode[];
}
```

### ✅ Project List

**jsonList** - `Default/IndexController.php:189`
```php
public function jsonListAction()
{
    // ... query logic ...
    $records = $this->getModelObject()->fetchAll($where, null, $count, $offset);

    \Phprojekt_Converter_Json::echoConvert($records, \Phprojekt_ModelInformation_Default::ORDERING_LIST);
}
```

**TypeScript Type** ✅ **MATCHES**:
```typescript
interface ProjectListResponse {
  metadata?: ProjectMetadata[];
  data: Project[];
  numRows?: number;
}
```

---

## Type Accuracy Verification

All TypeScript types were created by:
1. **Reading PHP source code** - Controllers and Models
2. **Analyzing response structure** - Converter classes
3. **Checking existing Dojo code** - Frontend expectations

**Confidence Level**: **HIGH** ✅

The types accurately reflect what the PHP backend returns based on source code analysis.

---

## Next Steps for Full Integration Testing

1. **Set up MySQL** for PHPUnit tests
2. **Configure authentication** for endpoint testing
3. **Create test user/session** for curl testing
4. **Add Playwright E2E tests** that:
   - Log in as test user
   - Call API endpoints
   - Verify JSON responses match TypeScript types

---

## Recommendations

**For Development:**
- Use the **API Test Page** (`/app/api-test`) to test endpoints interactively
- Mock responses in unit tests (already done ✅)
- Use TypeScript compiler to catch type mismatches

**For Production:**
- Set up proper error monitoring for API calls
- Add request/response logging
- Implement retry logic for transient failures

---

## Test Commands

```bash
# Run frontend unit tests
cd frontend-react
npm test

# Run frontend tests with UI
npm run test:ui

# Run frontend tests with coverage
npm run test:coverage

# Run backend tests (requires MySQL)
cd phprojekt
vendor/bin/phpunit --no-coverage

# Build React app
cd frontend-react
npm run build

# Start dev server
npm run dev
```

---

**Document Version:** 1.0
**Last Updated:** 2025-11-16
