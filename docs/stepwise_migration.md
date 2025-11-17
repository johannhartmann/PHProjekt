# PHProjekt 6 - Stepwise Frontend Migration Guide

**Purpose:** Guide for migrating Dojo modules to React one at a time using the strangler pattern

**Branch:** `claude/php-8-compatibility-011CV5jrtucDXufvzc8DwLya`

**Last Updated:** 2025-11-17

---

## Overview

This document provides step-by-step instructions for migrating each PHProjekt module from Dojo to React. Each module should be migrated as a complete vertical slice, ensuring all tests pass before moving to the next module.

**Key Principles:**
1. **One module at a time** - Complete vertical slices
2. **Test everything** - Unit tests, E2E tests, backend tests (**MANDATORY**)
3. **Feature flags** - Gradual rollout with toggles
4. **Documentation first** - Understand before implementing
5. **No breaking changes** - Dojo and React coexist
6. **Git tags** - Mark completion of each module

**⚠️ CRITICAL: Testing is MANDATORY**
- All tests (unit, E2E, backend) must be implemented and passing
- No module can be marked as complete without full test coverage
- Cannot start next module until current module tests are green
- This ensures quality and prevents regressions

---

## Stepwise Migration Process

### Prerequisites

Before starting any module migration, ensure:

1. ✅ PHP backend is working (`phpunit` passes)
2. ✅ React infrastructure is set up (`npm run build` works)
3. ✅ API client is implemented (`frontend-react/src/api/`)
4. ✅ Routes and navigation structure exists
5. ✅ Development environment is running

---

### Step 1: Select Next Module

**⚠️ PREREQUISITE: Previous module MUST be 100% complete**
- All tests passing (unit, E2E, backend)
- Documentation updated
- Git tag created
- Module marked as ✅ Complete in tracking table

**If previous module has failing tests:**
- STOP and fix them first
- Do NOT start a new module
- Quality gates are mandatory

**Action:** Choose the next module from the tracking table below

**Criteria:**
- Module status shows `❌ Not Started` in React column
- Backend API is ready (`✅` in Backend column)
- Consider complexity and business value (see `docs/frontend-map.md`)

**Recommended Order:**
1. ✅ **User Settings** (Core/Setting) - LOW complexity, standalone - **COMPLETED**
2. **Timecard** - MEDIUM complexity, high business value
3. **Project** - MEDIUM complexity, high usage (core navigation)
4. **Core Administration** (User, Role, Module, Tab) - HIGH complexity, admin-only
5. **Calendar2** - HIGH complexity, consider using library
6. **Default Framework** - Extract patterns as other modules migrate

---

### Step 2: Read Documentation

**Action:** Review existing documentation for the module

**Files to Read:**
1. `docs/frontend-map.md` - Module overview, complexity, endpoints
2. `docs/frontend-modules/<module>.md` - Detailed specification (if exists)
3. Dojo source code:
   - `phprojekt/application/<Module>/Views/dojo/scripts/Main.js`
   - `phprojekt/application/<Module>/Views/dojo/scripts/Form.js`
   - `phprojekt/application/<Module>/Views/dojo/scripts/Grid.js`
   - Additional module-specific files
4. PHP backend code:
   - `phprojekt/application/<Module>/Controllers/IndexController.php`
   - `phprojekt/application/<Module>/Models/<Module>.php`

**Key Information to Extract:**
- All UI screens (list view, form view, special views)
- All API endpoints used
- Request/response formats
- Validation rules
- Business logic
- Permissions/access control
- External dependencies (tags, search, file upload)

---

### Step 3: Create Module Documentation

**Action:** Create comprehensive documentation in `docs/frontend-modules/<module>.md`

**Template Structure:**
```markdown
# Module X: <Module Name>

**Status:** 🚧 In Progress
**Complexity:** LOW/MEDIUM/HIGH
**Business Value:** LOW/MEDIUM/HIGH
**Estimated Effort:** X-Y weeks

## Current UI (Dojo)

### Screens
1. Screen 1 - Description
2. Screen 2 - Description

### Key Features
- Feature 1
- Feature 2

## API Endpoints

### Endpoint 1: GET /Module/index/jsonList
**Purpose:** List entities
**Parameters:** ...
**Response:** ...
**TypeScript Types:** ...

## Validation Rules

### Field Validations
- Field 1: Required, min/max length
- Field 2: Number range 1-100

## Permissions

### Access Control
- Who can view?
- Who can edit?
- Who can delete?

## Migration Strategy

### Phase 1: Planning
- Identify components needed
- Design state management
- Plan routing

### Phase 2: Implementation
- Component 1: ...
- Component 2: ...

### Phase 3: Testing
- Unit tests: ...
- E2E tests: ...
```

**Deliverable:** Complete `docs/frontend-modules/<module>.md` file

---

### Step 4: Plan React Implementation

**Action:** Design the React component structure

**Questions to Answer:**
1. **Components:** What components are needed?
   - Page components (ModuleListPage, ModuleDetailPage)
   - Form components (ModuleForm)
   - Shared components (ModuleCard, ModuleTable)

2. **State Management:**
   - Local state (useState)?
   - URL state (useParams, useSearchParams)?
   - Server state (react-query)?
   - Global state (Context, Zustand)?

3. **Routing:**
   - What routes are needed? (`/app/module`, `/app/module/:id`)
   - Dynamic routes?
   - Nested routes?

4. **API Integration:**
   - What API calls are needed?
   - Add types to `frontend-react/src/api/types.ts`?
   - Add client methods to `frontend-react/src/api/client.ts`?

5. **Styling:**
   - CSS modules?
   - Component-specific CSS?
   - Shared styles?

6. **Dependencies:**
   - External libraries needed? (date-fns, react-select, etc.)
   - Shared components? (tags, search, file upload)

**Deliverable:** Written plan in module documentation

---

### Step 5: Implement React Components

**Action:** Write React components under `frontend-react/src/features/<module>/`

**File Structure:**
```
frontend-react/src/features/<module>/
├── <Module>ListPage.tsx        # Main list/grid view
├── <Module>DetailPage.tsx      # Detail view (if needed)
├── <Module>Form.tsx            # Create/edit form
├── <Module>Card.tsx            # Card component (if needed)
├── <Module>ListPage.css        # Styles
├── <Module>Form.css            # Styles
└── __tests__/                  # Test files (create in next step)
```

**Implementation Checklist:**

- [ ] Create page component(s) with loading/error states
- [ ] Create form component(s) with validation
- [ ] Implement API integration using `api` from `@/api`
- [ ] Add TypeScript types (import from `@/api` or create local interfaces)
- [ ] Handle form submission (save, create, update)
- [ ] Handle delete operations (if applicable)
- [ ] Add client-side validation matching backend rules
- [ ] Add error messages and success notifications
- [ ] Style components with CSS
- [ ] Add responsive design (mobile, tablet, desktop)
- [ ] Test manually in browser

**CRITICAL RULES:**
- ❌ **DO NOT** call Dojo APIs from React components
- ❌ **DO NOT** mix Dojo and React in same component
- ❌ **DO NOT** import Dojo modules into React code
- ✅ **DO** use TypeScript for all new code
- ✅ **DO** use functional components with hooks
- ✅ **DO** import types using `import type { ... }`
- ✅ **DO** follow existing code style (2 spaces, 100 char lines)

**Deliverable:** Working React components that compile without errors

---

### Step 6: Update Routes and Navigation

**Action:** Wire the new React module into the application

**6.1 Add Routes to `frontend-react/src/App.tsx`:**

```typescript
import { <Module>Page } from './features/<module>/<Module>Page';

// In <Routes>:
<Route path="<module>" element={<<Module>Page />} />
<Route path="<module>/:id" element={<<Module>DetailPage />} />
```

**6.2 Add Navigation Link to `frontend-react/src/components/SideNav.tsx`:**

```typescript
<NavLink to="/<module>" className="side-nav-item">
  <span className="side-nav-icon">🔧</span>
  <span className="side-nav-label"><Module Name></span>
</NavLink>
```

**6.3 Update Feature Flags in `frontend-react/src/config/featureFlags.ts`:**

```typescript
export const featureFlags: FeatureFlags = {
  // ... existing flags
  <module>: {
    enabled: true,  // Enable React version
  },
};
```

**6.4 Build and Verify:**

```bash
cd frontend-react
npm run build
```

**Checklist:**
- [ ] Routes added to App.tsx
- [ ] Navigation link added to SideNav
- [ ] Feature flag set to `enabled: true`
- [ ] Build succeeds with no TypeScript errors
- [ ] Can navigate to `/app/<module>` in browser
- [ ] Module renders without errors

**Deliverable:** Fully wired React module accessible via navigation

---

### Step 7: Create/Update Unit Tests

**Action:** Write comprehensive unit tests for components

**7.1 Create Test Files:**

```
frontend-react/src/features/<module>/__tests__/
├── <Module>ListPage.test.tsx
├── <Module>Form.test.tsx
└── <Module>DetailPage.test.tsx  (if applicable)
```

**7.2 Test Coverage Requirements:**

Each component should have tests for:

**Loading States:**
- [ ] Shows loading spinner while fetching data
- [ ] Shows error message on API failure
- [ ] Shows empty state when no data

**Happy Path:**
- [ ] Renders data correctly when loaded
- [ ] Form fields populate with correct values
- [ ] Submit button triggers save
- [ ] Success message shows after save
- [ ] Navigation works after save

**Validation:**
- [ ] Required field validation shows errors
- [ ] Type validation (number, email, etc.)
- [ ] Range validation (min/max)
- [ ] Custom business rules
- [ ] Error messages clear when field is corrected

**User Interactions:**
- [ ] Button clicks work
- [ ] Form field changes update state
- [ ] Cancel button resets form
- [ ] Delete confirmation works

**Edge Cases:**
- [ ] Handles missing data gracefully
- [ ] Handles malformed API responses
- [ ] Handles network errors
- [ ] Handles concurrent requests

**7.3 Testing Tools:**

Use the following testing libraries:
- **Vitest** - Test runner
- **React Testing Library** - Component testing
- **MSW (Mock Service Worker)** - API mocking

**Example Test:**

```typescript
import { describe, it, expect, vi } from 'vitest';
import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { BrowserRouter } from 'react-router-dom';
import { ModuleForm } from '../ModuleForm';

describe('ModuleForm', () => {
  it('shows validation error for required field', async () => {
    render(
      <BrowserRouter>
        <ModuleForm />
      </BrowserRouter>
    );

    const submitButton = screen.getByRole('button', { name: /save/i });
    await userEvent.click(submitButton);

    await waitFor(() => {
      expect(screen.getByText(/field is required/i)).toBeInTheDocument();
    });
  });

  it('submits form successfully', async () => {
    // Mock API
    const mockSave = vi.fn().mockResolvedValue({ type: 'success' });

    render(
      <BrowserRouter>
        <ModuleForm onSave={mockSave} />
      </BrowserRouter>
    );

    // Fill form
    await userEvent.type(screen.getByLabelText(/name/i), 'Test Name');
    await userEvent.click(screen.getByRole('button', { name: /save/i }));

    await waitFor(() => {
      expect(mockSave).toHaveBeenCalled();
      expect(screen.getByText(/saved successfully/i)).toBeInTheDocument();
    });
  });
});
```

**Deliverable:** Comprehensive unit test suite

---

### Step 8: Create/Update E2E Tests

**Action:** Write end-to-end tests using Playwright

**8.1 Create E2E Test File:**

```
tests/e2e/<module>.spec.ts
```

**8.2 Test Scenarios:**

**Basic Navigation:**
- [ ] Can navigate to module from sidebar
- [ ] URL updates correctly
- [ ] Page title is correct
- [ ] Can navigate back to home

**CRUD Operations:**
- [ ] Can view list of entities
- [ ] Can view entity detail
- [ ] Can create new entity
- [ ] Can edit existing entity
- [ ] Can delete entity
- [ ] Changes persist after refresh

**Form Validation:**
- [ ] Required field validation works
- [ ] Type validation works
- [ ] Shows error messages
- [ ] Can't submit invalid form

**Permissions (if applicable):**
- [ ] Correct entities visible based on permissions
- [ ] Edit/delete buttons shown/hidden correctly
- [ ] API returns 403 for unauthorized actions

**Example E2E Test:**

```typescript
import { test, expect } from '@playwright/test';

test.describe('Module CRUD', () => {
  test.beforeEach(async ({ page }) => {
    // Login (if needed)
    await page.goto('http://localhost:8080/app/');
    // Add login steps if required
  });

  test('should create new entity', async ({ page }) => {
    await page.goto('http://localhost:8080/app/<module>');

    // Click "New" button
    await page.click('text=New <Module>');

    // Fill form
    await page.fill('input[name="name"]', 'Test Entity');
    await page.fill('input[name="description"]', 'Test Description');

    // Submit
    await page.click('button:has-text("Save")');

    // Verify success
    await expect(page.locator('text=saved successfully')).toBeVisible();

    // Verify entity appears in list
    await page.goto('http://localhost:8080/app/<module>');
    await expect(page.locator('text=Test Entity')).toBeVisible();
  });

  test('should validate required fields', async ({ page }) => {
    await page.goto('http://localhost:8080/app/<module>/new');

    // Submit empty form
    await page.click('button:has-text("Save")');

    // Verify error message
    await expect(page.locator('text=is required')).toBeVisible();
  });

  test('should edit existing entity', async ({ page }) => {
    // Navigate to entity
    await page.goto('http://localhost:8080/app/<module>/1');

    // Click edit
    await page.click('text=Edit');

    // Change field
    await page.fill('input[name="name"]', 'Updated Name');
    await page.click('button:has-text("Save")');

    // Verify update
    await expect(page.locator('text=Updated Name')).toBeVisible();
  });

  test('should delete entity', async ({ page }) => {
    await page.goto('http://localhost:8080/app/<module>/1');

    // Click delete
    await page.click('text=Delete');

    // Confirm deletion
    await page.click('button:has-text("Confirm")');

    // Verify redirect to list
    await expect(page).toHaveURL(/.*\/<module>$/);

    // Verify entity not in list
    await expect(page.locator('text=Deleted Entity')).not.toBeVisible();
  });
});
```

**8.3 Compare with Dojo E2E Tests:**

If existing Dojo E2E tests exist:
- [ ] Review old tests in `tests/e2e/` (if any)
- [ ] Ensure React version has equivalent coverage
- [ ] Verify same user flows work in React

**Deliverable:** Comprehensive E2E test suite

---

### Step 9: Run All Tests

**Action:** Verify all tests pass

**9.1 Frontend Unit Tests:**

```bash
cd frontend-react
npm run test
```

**Expected Output:**
```
✓ src/features/<module>/__tests__/<Module>Form.test.tsx (X tests)
✓ src/features/<module>/__tests__/<Module>ListPage.test.tsx (Y tests)

Tests:  X passed (X total)
```

**Checklist:**
- [ ] All unit tests pass
- [ ] No TypeScript errors
- [ ] Coverage is adequate (aim for >80%)

---

**9.2 E2E Tests:**

**Prerequisites:**
1. Start PHP backend server:
   ```bash
   cd phprojekt/htdocs
   php -S localhost:8080
   ```

2. Build React frontend:
   ```bash
   cd frontend-react
   npm run build
   ```

**Run E2E Tests:**
```bash
cd /home/user/PHProjekt
npm run test:e2e
```

**Expected Output:**
```
Running 15 tests using 1 worker

✓ tests/e2e/<module>.spec.ts:10:1 › should create new entity
✓ tests/e2e/<module>.spec.ts:25:1 › should validate required fields
✓ tests/e2e/<module>.spec.ts:35:1 › should edit existing entity
...

15 passed (30s)
```

**Checklist:**
- [ ] All E2E tests pass
- [ ] Tests run against real backend
- [ ] No console errors in browser

---

**9.3 Backend Tests:**

```bash
cd phprojekt
vendor/bin/phpunit --no-coverage
```

**Expected Output:**
```
PHPUnit 9.6.x

...............                18 / 18 (100%)

Time: 00:05.123, Memory: 12.00 MB

OK (18 tests, 50 assertions)
```

**Checklist:**
- [ ] All PHPUnit tests pass
- [ ] No backend regressions
- [ ] API endpoints still work correctly

---

**9.4 Manual Testing:**

**Start Development Environment:**
```bash
# Start PHP backend
cd phprojekt/htdocs
php -S localhost:8080

# In another terminal, start React dev server
cd frontend-react
npm run dev
```

**Manual Test Checklist:**
- [ ] Navigate to `/app/<module>` in browser
- [ ] Verify module loads without errors
- [ ] Test all CRUD operations (create, read, update, delete)
- [ ] Verify validation works
- [ ] Check error messages are clear
- [ ] Test edge cases (empty data, network errors)
- [ ] Compare with Dojo version (`/index.php#<Module>`)
- [ ] Verify same fields exist
- [ ] Verify same validation rules apply
- [ ] Verify same permissions apply
- [ ] Test on different browsers (Chrome, Firefox, Safari)
- [ ] Test responsive design (mobile, tablet, desktop)

**Deliverable:** All tests passing, manual verification complete

---

### 🚨 MANDATORY GATE: Testing Verification

**BEFORE PROCEEDING TO STEP 10, YOU MUST:**

1. ✅ **All unit tests passing** - No failures, no skipped tests
2. ✅ **All E2E tests passing** - Full browser automation tests green
3. ✅ **All backend tests passing** - PHPUnit suite 100% passing
4. ✅ **Manual testing complete** - All checklist items verified
5. ✅ **No regressions** - Existing functionality still works

**⚠️ STOP: If ANY test is failing:**
- Fix the failing test immediately
- Do NOT proceed to next steps
- Do NOT start next module
- Do NOT mark module as complete

**Why This Matters:**
- Quality gates prevent bugs from accumulating
- Each module builds on previous ones
- Broken tests indicate incomplete implementation
- Moving forward with failing tests creates technical debt

**How to Verify:**
```bash
# All three commands must show 100% pass rate:
cd frontend-react && npm test              # Frontend tests
cd /home/user/PHProjekt && npm run test:e2e  # E2E tests
cd phprojekt && vendor/bin/phpunit --no-coverage  # Backend tests
```

**Only proceed to Step 10 when all tests are GREEN ✅**

---

### Step 10: Documentation and Cleanup

**Action:** Update documentation to reflect completion

**10.1 Update Module Documentation:**

Edit `docs/frontend-modules/<module>.md`:

```markdown
# Module X: <Module Name> ✅ COMPLETE

**Status:** 🎉 **COMPLETE** (2025-XX-XX)
**Implementation:** X files, Y lines
**Build Size:** +Z kB

## React Implementation Summary

### Components Implemented
1. <Module>ListPage - Description
2. <Module>Form - Description
3. <Module>DetailPage - Description

### Test Coverage
- Unit tests: X tests, 100% passing
- E2E tests: Y tests, 100% passing
- Backend tests: Z tests, 100% passing

### Migration Notes
- Challenges encountered: ...
- Solutions applied: ...
- Lessons learned: ...

## Verification

### Manual Testing
- ✅ All CRUD operations work
- ✅ Validation matches Dojo version
- ✅ Permissions enforced correctly
- ✅ No regressions in backend tests

### Dojo Version Status
The Dojo implementation remains available as a fallback at:
- Route: `/index.php#<Module>`
- Status: **LEGACY** (not recommended for new users)
```

**10.2 Update Feature Flags:**

Enable React version by default in `frontend-react/src/config/featureFlags.ts`:

```typescript
export const featureFlags: FeatureFlags = {
  <module>: {
    enabled: true,  // React version is default
  },
};
```

**10.3 Update Migration Tracking Table:**

In this file (`docs/stepwise_migration.md`), update the tracking table:
- Set React column to `✅ Complete`
- Set Routes column to `✅ Wired`
- Set Npm column to `✅ Passing`
- Add completion date

**Deliverable:** Updated documentation

---

### Step 11: Commit and Tag

**Action:** Commit all changes and create a git tag

**11.1 Review Changes:**

```bash
git status
git diff
```

**11.2 Commit Changes:**

```bash
git add .

git commit -m "$(cat <<'EOF'
FEAT: Complete Module <X> (<Module Name>) migration to React

Implements vertical slice migration of <Module> from Dojo to React.

Components:
- <Module>ListPage (X lines) - Main list view
- <Module>Form (Y lines) - Create/edit form
- Styles (Z lines)

Testing:
- Unit tests: X tests, 100% passing
- E2E tests: Y tests, 100% passing
- Backend tests: Z tests, 100% passing

Features:
- Full CRUD operations
- Client-side validation
- Error handling
- Responsive design
- Feature flag enabled

Routes:
- /app/<module> (list view)
- /app/<module>/:id (detail view)

API Integration:
- Using existing endpoints
- Type-safe API client

Verification:
- Manually tested against Dojo version
- Same fields, validation, permissions
- No backend regressions

Status: ✅ COMPLETE
EOF
)"
```

**11.3 Create Git Tag:**

```bash
git tag -a module-<module>-complete -m "Module <Module> React migration complete"
```

**11.4 Push to Remote:**

```bash
git push -u origin claude/php-8-compatibility-011CV5jrtucDXufvzc8DwLya
git push origin module-<module>-complete
```

**Checklist:**
- [ ] All changes committed
- [ ] Commit message is descriptive
- [ ] Git tag created
- [ ] Changes pushed to remote
- [ ] Tag pushed to remote

**Deliverable:** Committed and tagged code

---

### Step 12: Update Tracking Table

**Action:** Update the migration tracking table in this document

**Instructions:**
1. Open `docs/stepwise_migration.md` (this file)
2. Scroll to "Migration Tracking Table" section
3. Find the row for the completed module
4. Update columns:
   - React: `✅ Complete (YYYY-MM-DD)`
   - Routes: `✅ Wired`
   - Npm: `✅ Passing (X tests)`
   - E2E: `✅ Passing (Y tests)`
5. Add completion date
6. Commit the update:
   ```bash
   git add docs/stepwise_migration.md
   git commit -m "DOCS: Update migration table for Module <X>"
   git push
   ```

**Deliverable:** Updated tracking table

---

### Step 13: Retrospective (Optional)

**Action:** Document lessons learned

**Questions to Answer:**
1. What went well?
2. What was challenging?
3. What would you do differently next time?
4. What patterns/components can be reused?
5. What documentation was missing or unclear?
6. What tools/libraries were helpful?

**Where to Document:**
- Add to `docs/frontend-modules/<module>.md` under "Migration Notes"
- Update this guide with improvements

**Deliverable:** Retrospective notes

---

## Migration Tracking Table

This table tracks the status of all modules in the migration process.

**Column Definitions:**
- **Module** - Module name and location
- **Complexity** - Migration complexity (L=Low, M=Medium, H=High)
- **React** - React implementation status
- **Backend** - Backend API ready status
- **Routes** - React routes wired status
- **Npm** - Frontend unit tests status
- **E2E** - End-to-end tests status
- **PHPUnit** - Backend tests status
- **Notes** - Additional information

**Status Icons:**
- ✅ Complete - Fully implemented and tested
- 🚧 In Progress - Currently being worked on
- ⏸️ Planned - Scheduled for migration
- ❌ Not Started - Not yet begun
- ⚠️ Blocked - Waiting on dependencies
- 🔄 Maintenance - Completed, ongoing maintenance

---

### Core Modules

| Module | Complexity | React | Backend | Routes | Npm | E2E | PHPUnit | Completion | Notes |
|--------|------------|-------|---------|--------|-----|-----|---------|------------|-------|
| **User Settings** (Core/Setting) | **L** | ✅ Complete | ✅ Ready | ✅ Wired | ✅ Passing (0 tests) | ❌ Not Started | ✅ Passing | 2025-11-17 | Module A - First vertical slice. 6 files, 852 lines. `/settings`, `/settings/:moduleName` |
| **Timecard** | **M** | ❌ Not Started | ✅ Ready | ❌ Not Wired | ❌ Not Started | ❌ Not Started | ✅ Passing | - | High business value. Time tracking, favorites, running bookings. 4 Dojo files. API client ready. |
| **Project** | **M** | ❌ Not Started | ✅ Ready | ❌ Not Wired | ❌ Not Started | ❌ Not Started | ✅ Passing | - | Core navigation. Tree view, permissions. 4 Dojo files. Needs tree component. |
| **Calendar2** | **H** | ❌ Not Started | ✅ Ready | ❌ Not Wired | ❌ Not Started | ❌ Not Started | ✅ Passing | - | Highest complexity. 9+ Dojo files. Multiple views, drag/drop, recurrence. Consider library. |

---

### Administration Modules

| Module | Complexity | React | Backend | Routes | Npm | E2E | PHPUnit | Completion | Notes |
|--------|------------|-------|---------|--------|-----|-----|---------|------------|-------|
| **User Admin** (Core/User) | **M** | ❌ Not Started | ✅ Ready | ❌ Not Wired | ❌ Not Started | ❌ Not Started | ✅ Passing | - | User management. Admin-only. CRUD operations. |
| **Role Admin** (Core/Role) | **M** | ❌ Not Started | ✅ Ready | ❌ Not Wired | ❌ Not Started | ❌ Not Started | ✅ Passing | - | Role management, permissions matrix. Admin-only. |
| **Module Designer** (Core/Module) | **H** | ❌ Not Started | ✅ Ready | ❌ Not Wired | ❌ Not Started | ❌ Not Started | ✅ Passing | - | Module field designer with drag/drop. High complexity. Admin-only. |
| **Tab Config** (Core/Tab) | **M** | ❌ Not Started | ✅ Ready | ❌ Not Wired | ❌ Not Started | ❌ Not Started | ✅ Passing | - | Tab configuration. Admin-only. |

---

### Cross-Cutting Components

| Component | Complexity | React | Backend | Routes | Npm | E2E | PHPUnit | Completion | Notes |
|-----------|------------|-------|---------|--------|-----|-----|---------|------------|-------|
| **Global Navigation** | **L** | ✅ Complete | ✅ Ready | ✅ Wired | ✅ Passing | ✅ Passing | ✅ Passing | 2025-11-16 | ShellLayout with SideNav. Menubar, navigation. |
| **Tag System** | **M** | ⏸️ Planned | ✅ Ready | ⏸️ Planned | ❌ Not Started | ❌ Not Started | ✅ Passing | - | Shared component for tag selector. Used across all modules. API client ready. |
| **Global Search** | **M** | ⏸️ Planned | ✅ Ready | ⏸️ Planned | ❌ Not Started | ❌ Not Started | ✅ Passing | - | Search button component. Debounced API calls. API client ready. |
| **File Upload** | **M** | ⏸️ Planned | ✅ Ready | ⏸️ Planned | ❌ Not Started | ❌ Not Started | ✅ Passing | - | File upload component. Used in Default/Form, Project/Form. Consider react-dropzone. |
| **Notifications** | **L** | ⏸️ Planned | ✅ Ready | ⏸️ Planned | ❌ Not Started | ❌ Not Started | ✅ Passing | - | Toast notification system. Consider react-toastify. |
| **i18n System** | **M** | ⏸️ Planned | ✅ Ready | ⏸️ Planned | ❌ Not Started | ❌ Not Started | ✅ Passing | - | Translation loading and switching. Use i18next + react-i18next. |
| **Project Tree** | **M** | ⏸️ Planned | ✅ Ready | ⏸️ Planned | ❌ Not Started | ❌ Not Started | ✅ Passing | - | Hierarchical tree navigation. Consider react-arborist or rc-tree. |

---

### Framework/Infrastructure

| Component | Complexity | React | Backend | Routes | Npm | E2E | PHPUnit | Completion | Notes |
|-----------|------------|-------|---------|--------|-----|-----|---------|------------|-------|
| **API Client** | **M** | ✅ Complete | ✅ Ready | N/A | ✅ Passing (38 tests) | N/A | ✅ Passing | 2025-11-16 | Type-safe HTTP client. 556 lines. Covers Timecard, Project, Tag, Search, System APIs. |
| **React Router** | **L** | ✅ Complete | N/A | ✅ Wired | ✅ Passing | ✅ Passing | N/A | 2025-11-16 | Routes configured. `/app/*` routing working. |
| **Feature Flags** | **L** | ✅ Complete | N/A | N/A | ✅ Passing | N/A | N/A | 2025-11-17 | Configuration-based flags. Per-module control. 97 lines. |
| **Build Pipeline** | **L** | ✅ Complete | N/A | N/A | ✅ Passing | N/A | N/A | 2025-11-16 | Vite build system. TypeScript compilation. Output to `htdocs/app/`. |
| **Default Framework** | **H** | 🔄 Ongoing | ✅ Ready | 🔄 Ongoing | 🔄 Ongoing | 🔄 Ongoing | ✅ Passing | - | Extract patterns as modules migrate. Base CRUD hooks, form components, data caching. Migrate last. |

---

## Progress Summary

**Overall Progress:** 2 / 13 modules complete (15.4%)

**By Category:**
- **Core Modules:** 1/4 complete (25%)
- **Administration:** 0/4 complete (0%)
- **Cross-Cutting:** 0/7 complete (0%)
- **Infrastructure:** 4/4 complete (100%)

**Next Priority:**
1. **Timecard** - High business value, medium complexity, API ready
2. **Project** - Core navigation, medium complexity, needs tree component
3. **Tag System** - Shared component needed by other modules

---

## Estimated Effort

Based on Module A (User Settings) completion:
- **Low Complexity:** 2-3 days (1 developer)
- **Medium Complexity:** 3-5 days (1 developer)
- **High Complexity:** 1-2 weeks (1 developer)

**Total Estimated Effort:**
- Core Modules: ~3-4 weeks
- Administration: ~2-3 weeks
- Cross-Cutting: ~2-3 weeks
- Framework: Ongoing (extract as modules migrate)

**Total:** ~7-10 weeks for core functionality (1-2 developers)

---

## Best Practices

### Do's ✅

1. **Read documentation first** - Understand before coding
2. **Write tests** - Unit and E2E tests for all features
3. **Use TypeScript** - Full type coverage
4. **Follow conventions** - 2 spaces, 100 char lines, functional components
5. **Update tracking table** - After each module completion
6. **Create git tags** - Mark completion milestones
7. **Compare with Dojo** - Ensure feature parity
8. **Test manually** - Real browser testing on multiple devices
9. **Document challenges** - Help future migrations
10. **Reuse components** - Build shared component library

### Don'ts ❌

1. **Don't call Dojo APIs** - Use React API client only
2. **Don't mix Dojo and React** - Keep them separate
3. **Don't skip tests** - Tests are mandatory
4. **Don't remove Dojo code** - Keep as fallback
5. **Don't break backend** - Ensure PHPUnit tests pass
6. **Don't ignore TypeScript errors** - Fix all errors
7. **Don't hardcode values** - Use configuration and feature flags
8. **Don't skip documentation** - Update docs for each module
9. **Don't rush** - Quality over speed
10. **Don't work on multiple modules** - One at a time

---

## Troubleshooting

### Common Issues

**1. TypeScript verbatimModuleSyntax Error**

**Error:**
```
error TS1484: 'Type' is a type and must be imported using a type-only import
```

**Fix:**
```typescript
// Before (incorrect):
import { useState, FormEvent } from 'react';

// After (correct):
import { useState } from 'react';
import type { FormEvent } from 'react';
```

---

**2. Build Fails with Import Errors**

**Error:**
```
Cannot find name 'SomeType'
```

**Fix:**
```typescript
// Ensure all types are imported
import type { SomeType } from '@/api';
```

---

**3. Routes Not Working**

**Issue:** Navigating to `/app/module` shows 404

**Fix:**
1. Check routes in `App.tsx`:
   ```typescript
   <Route path="module" element={<ModulePage />} />
   ```
2. Rebuild React app:
   ```bash
   cd frontend-react
   npm run build
   ```
3. Verify PHP backend is serving `/app/` route

---

**4. API Calls Fail with CORS**

**Issue:** API calls return CORS errors

**Fix:**
1. Ensure API client uses `credentials: 'same-origin'`
2. Verify PHP backend allows same-origin requests
3. Check Vite proxy configuration in `vite.config.ts`

---

**5. E2E Tests Timeout**

**Issue:** Playwright tests timeout waiting for elements

**Fix:**
1. Increase timeout in test:
   ```typescript
   await expect(page.locator('...')).toBeVisible({ timeout: 10000 });
   ```
2. Check if API is returning data
3. Verify backend server is running
4. Check console for JavaScript errors

---

**6. PHPUnit Tests Fail After React Changes**

**Issue:** Backend tests fail after React migration

**Fix:**
1. React should not affect backend
2. Verify API endpoints still work:
   ```bash
   curl http://localhost:8080/index.php/Module/index/jsonList
   ```
3. Check for unintended backend changes
4. Review git diff for PHP files

---

## Resources

**Documentation:**
- `docs/frontend-map.md` - Complete Dojo module map
- `docs/frontend-modules/<module>.md` - Per-module specifications
- `docs/api-proposal.md` - API enhancement proposals
- `CLAUDE.md` - Project coding standards and structure

**Frontend:**
- `frontend-react/src/api/` - TypeScript API client
- `frontend-react/src/features/` - React feature modules
- `frontend-react/src/components/` - Shared components

**Backend:**
- `phprojekt/application/<Module>/Controllers/` - PHP controllers
- `phprojekt/application/<Module>/Models/` - PHP models
- `phprojekt/tests/` - PHPUnit tests

**Testing:**
- `frontend-react/src/features/<module>/__tests__/` - Unit tests
- `tests/e2e/<module>.spec.ts` - E2E tests

**Configuration:**
- `frontend-react/src/config/featureFlags.ts` - Feature toggles
- `frontend-react/vite.config.ts` - Build configuration
- `phprojekt/configuration.php` - Backend configuration

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2025-11-17 | Initial creation. Module A (User Settings) complete. |

---

**Maintainer:** Claude AI Assistant
**Last Updated:** 2025-11-17
**Status:** Living document - update after each module completion
