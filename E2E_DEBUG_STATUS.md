# E2E Test Debugging Status

**Date**: 2025-11-23
**Branch**: `claude/dojo-to-react-migration-01DWi93PzxoCRJ6HRh6vwSWG`

## Summary

E2E tests for the Timecard module are failing. All 23 tests timeout waiting for the h1 element with text "Timecard" to appear. Extensive debugging has been performed to isolate the issue.

## What Was Fixed

### 1. PHP Backend Routing ✅
- **Problem**: PHP built-in server returned 404 for Laminas MVC routes like `/index.php/Timecard/Index/jsonDayList`
- **Solution**: Created `phprojekt/htdocs/router.php` to route all requests through `index.php`
- **Status**: FIXED - Backend now correctly handles MVC routes
- **Commit**: `2d6f389` - "FIX: Add router.php for PHP built-in server + configuration"

### 2. Composer Dependencies ✅
- **Problem**: Application couldn't load Composer autoloader
- **Solution**: Ran `composer install` in `phprojekt/` directory
- **Status**: FIXED - All Laminas packages installed

### 3. Vite Base Path Configuration ✅
- **Problem**: Vite configured with `base: '/app/'` for production, but dev server needed root path
- **Solution**: Made base path conditional based on mode
  - Development: `base: '/'`
  - Production: `base: '/app/'`
- **Changes**:
  - `vite.config.ts`: Conditional base path
  - `App.tsx`: Dynamic React Router basename
  - `timecard.spec.ts`: Navigate to `/timecard` instead of `/app/timecard`
- **Status**: FIXED
- **Commit**: `c280caf` - "FIX: Configure Vite base path for dev vs production"

### 4. Unit Tests ✅
- **Problem**: 2/92 unit tests failing due to URL pattern changes
- **Solution**: Updated test expectations from `index` to `Index` (capitalized)
- **Status**: FIXED - 92/92 unit tests passing
- **Commit**: `db298c4` - "FIX: Update API client tests for Laminas MVC routing changes"

## Current Issue: E2E Tests Still Failing ❌

### Symptoms
- **All 23 E2E tests fail** with same error pattern
- **Timeout**: 30 seconds
- **Error**: `page.waitForSelector: Test timeout of 30000ms exceeded`
- **Missing element**: `h1:has-text("Timecard")`

### What We Know

#### ✅ Working Components
1. **Vite Dev Server**: Starts successfully on port 3000
2. **HTML Serving**: `curl http://localhost:3000/timecard` returns correct HTML
3. **TypeScript**: No compilation errors (`npm run type-check` passes)
4. **React Component**: `TimecardDayPage.tsx` has h1 element on line 94 (unconditional render)
5. **PHP Backend**: Working with router.php

#### ❓ Unknown/Unverified
1. **JavaScript Loading**: Unclear if React app actually mounts in browser
2. **React Router**: Unknown if routing to TimecardDayPage works
3. **Browser Errors**: No visibility into browser console during test execution
4. **Component Rendering**: Unknown if ShellLayout, TopNav, or SideNav block rendering

### Test Configuration

**Playwright Config** (`playwright.config.ts`):
```typescript
use: {
  baseURL: 'http://localhost:3000',
},
webServer: {
  command: 'npm run dev',
  url: 'http://localhost:3000',
  reuseExistingServer: !process.env.CI,
  timeout: 120000,
},
```

**Test Code** (`tests/e2e/timecard.spec.ts:17-22`):
```typescript
test.beforeEach(async ({ page }) => {
  await page.goto('/timecard');
  await page.waitForSelector('h1:has-text("Timecard")');
});
```

## Investigation Steps Taken

1. ✅ Verified Vite dev server starts and serves HTML
2. ✅ Checked TypeScript compilation - no errors
3. ✅ Reviewed TimecardDayPage component - h1 renders unconditionally
4. ✅ Verified router.php works for backend API calls
5. ✅ Updated Vite base path configuration
6. ✅ Updated test to navigate to `/timecard` (not `/app/timecard`)
7. ⏸️ Created debug test to capture screenshots and HTML - incomplete
8. ⏸️ Attempted to view Playwright HTML report - inconclusive

## Next Steps to Debug

### Option 1: Browser Console Inspection
Run Playwright with headed mode and check browser console for errors:
```bash
cd frontend-react
npx playwright test tests/e2e/timecard.spec.ts --headed --debug
```
Look for:
- JavaScript errors
- Failed module imports
- React mounting errors
- Router initialization issues

### Option 2: Simplified Test
Create a minimal test without ShellLayout:
1. Add a route that doesn't use ShellLayout wrapper
2. Test if basic React rendering works
3. Isolate whether issue is with Router, Shell, or React itself

### Option 3: Manual Testing
1. Start Vite dev server: `cd frontend-react && npm run dev`
2. Open browser to `http://localhost:3000/timecard`
3. Check if page renders correctly
4. Inspect browser console for errors
5. Use React DevTools to verify component tree

### Option 4: Trace Analysis
Run test with Playwright trace:
```bash
npx playwright test --trace on
npx playwright show-trace trace.zip
```

## Files Modified

| File | Change | Status |
|------|--------|--------|
| `phprojekt/htdocs/router.php` | Created new file | Committed |
| `phprojekt/configuration.php` | Copied from test config | Not committed (gitignored) |
| `frontend-react/vite.config.ts` | Conditional base path | Committed |
| `frontend-react/src/App.tsx` | Dynamic basename | Committed |
| `frontend-react/tests/e2e/timecard.spec.ts` | Updated navigation URL | Committed |
| `frontend-react/src/api/__tests__/client.test.ts` | Fixed URL patterns | Committed |

## Test Results Summary

- **Backend PHPUnit Tests**: Not run recently (were passing before)
- **Frontend Unit Tests**: 92/92 passing ✅
- **E2E Tests**: 0/23 passing ❌ (all timeout)

## Commands to Reproduce

### Start PHP Backend
```bash
cd /home/user/PHProjekt/phprojekt/htdocs
php -S localhost:8080 router.php
```

### Run E2E Tests
```bash
cd /home/user/PHProjekt/frontend-react
npm run test:e2e
```

### Run Unit Tests
```bash
cd /home/user/PHProjekt/frontend-react
npm test -- --run
```

## Hypothesis

The most likely cause is that React is not mounting in the browser during E2E tests. Possible reasons:
1. JavaScript module loading error (imports failing)
2. React initialization error
3. React Router configuration issue
4. Conflict between Playwright-started Vite server and existing processes
5. Timing issue - app takes longer than 30s to render (unlikely)

## Recommended Next Action

**Manual browser testing** is the quickest way to isolate the issue:
1. Manually open `http://localhost:3000/timecard` in a browser
2. If it works: E2E test environment issue (Playwright config)
3. If it doesn't work: Application issue (React, Router, or build)

This will immediately narrow down whether the problem is with the app itself or the E2E test setup.
