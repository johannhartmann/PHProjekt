# PHProjekt E2E Tests

End-to-end tests for PHProjekt 6 using [Playwright](https://playwright.dev/).

## Overview

This test suite validates key user flows in the PHProjekt Dojo frontend:

- **Authentication** - Login, logout, session management
- **Project Management** - Create, edit, delete, filter projects
- **Request/Ticket Management** - Create, update, assign, delete requests
- **Calendar Events** - Create, edit, delete events, recurring events, view switching

## Prerequisites

- Node.js 18+ and npm
- PHProjekt application running and accessible
- Valid test user credentials in the database

## Setup

### 1. Install Dependencies

From the repository root:

```bash
npm run test:e2e:install
```

Or from this directory (`tests/e2e/`):

```bash
npm install
```

### 2. Configure Environment

Copy the example environment file:

```bash
cp .env.e2e.example .env.e2e
```

Edit `.env.e2e` with your configuration:

```env
BASE_URL=http://localhost:8080
TEST_USER_USERNAME=test
TEST_USER_PASSWORD=test
TEST_ADMIN_USERNAME=admin
TEST_ADMIN_PASSWORD=admin
```

**Important:** Ensure test users exist in your PHProjekt database.

### 3. Install Playwright Browsers

```bash
npx playwright install
```

## Running Tests

### Run All Tests (Headless)

From repository root:

```bash
npm run test:e2e
```

From this directory:

```bash
npm test
```

### Run with UI Mode (Interactive)

```bash
npm run test:e2e:ui
```

This opens Playwright's UI mode for debugging and watching tests.

### Run in Headed Mode (See Browser)

```bash
npm run test:e2e:headed
```

### Run Specific Test File

```bash
npx playwright test specs/01-auth.spec.ts
```

### Run Specific Test

```bash
npx playwright test -g "should successfully login"
```

### Run in Debug Mode

```bash
npm run test:debug
```

Or:

```bash
npx playwright test --debug
```

### Run on Specific Browser

```bash
npx playwright test --project=chromium
npx playwright test --project=firefox
npx playwright test --project=webkit
```

## Test Structure

```
tests/e2e/
├── specs/                  # Test specifications
│   ├── 01-auth.spec.ts    # Authentication tests
│   ├── 02-project.spec.ts # Project management tests
│   ├── 03-request.spec.ts # Request/ticket tests
│   └── 04-calendar.spec.ts# Calendar event tests
├── utils/                  # Helper utilities
│   ├── auth.ts            # Login/logout helpers
│   ├── env.ts             # Environment config
│   └── helpers.ts         # Common helpers
├── .env.e2e               # Environment variables (git-ignored)
├── .env.e2e.example       # Example environment file
├── playwright.config.ts   # Playwright configuration
├── package.json           # Dependencies and scripts
└── README.md              # This file
```

## Writing New Tests

### Basic Test Template

```typescript
import { test, expect } from '@playwright/test';
import { login } from '../utils/auth';
import { config } from '../utils/env';
import { waitForDojoReady, waitForLoadingComplete } from '../utils/helpers';

test.describe('Feature Name', () => {
  test.beforeEach(async ({ page }) => {
    await login(page, config.testUser.username, config.testUser.password);
    await waitForDojoReady(page);
  });

  test('should do something', async ({ page }) => {
    // Your test code here
    await expect(page.locator('selector')).toBeVisible();
  });
});
```

### Using Helpers

**Login/Logout:**

```typescript
import { login, logout, isLoggedIn } from '../utils/auth';

await login(page, username, password);
await logout(page);
const loggedIn = await isLoggedIn(page);
```

**Dojo Helpers:**

```typescript
import { waitForDojoReady, waitForLoadingComplete, randomString } from '../utils/helpers';

await waitForDojoReady(page);           // Wait for Dojo framework to load
await waitForLoadingComplete(page);     // Wait for loading overlay
const unique = randomString(8);         // Generate random string
```

## Test Reports

After running tests, view the HTML report:

```bash
npx playwright show-report
```

Reports are saved to `playwright-report/`.

## CI/CD Integration

### GitHub Actions Example

```yaml
- name: Install E2E dependencies
  run: npm run test:e2e:install

- name: Install Playwright browsers
  run: cd tests/e2e && npx playwright install --with-deps

- name: Run E2E tests
  run: npm run test:e2e
  env:
    BASE_URL: http://localhost:8080
    TEST_USER_USERNAME: test
    TEST_USER_PASSWORD: test
```

### GitLab CI Example

```yaml
e2e-tests:
  stage: test
  script:
    - npm run test:e2e:install
    - cd tests/e2e && npx playwright install --with-deps
    - npm run test:e2e
  artifacts:
    when: always
    paths:
      - tests/e2e/playwright-report/
      - tests/e2e/test-results/
```

## Troubleshooting

### Tests Failing Due to Timeouts

Increase timeouts in `.env.e2e`:

```env
TEST_TIMEOUT=60000
NAVIGATION_TIMEOUT=60000
```

### Application Not Loading

1. Verify `BASE_URL` in `.env.e2e`
2. Ensure PHProjekt Docker containers are running:
   ```bash
   docker-compose up -d
   ```
3. Check application is accessible:
   ```bash
   curl http://localhost:8080
   ```

### Authentication Failures

1. Verify test user exists in database
2. Check credentials in `.env.e2e`
3. Manually test login at `BASE_URL`

### Selector Not Found

PHProjekt uses Dojo which loads dynamically. Use:

```typescript
await waitForDojoReady(page);
await page.waitForSelector('selector', { timeout: 10000 });
```

### Running in Docker

If running tests in Docker alongside PHProjekt:

```dockerfile
FROM mcr.microsoft.com/playwright:v1.48.0-jammy

WORKDIR /app
COPY tests/e2e/package*.json ./
RUN npm ci

COPY tests/e2e/ ./

CMD ["npm", "test"]
```

## Best Practices

1. **Use unique test data** - Generate unique names with `randomString()`
2. **Clean up after tests** - Delete created data in test teardown
3. **Wait for Dojo** - Always call `waitForDojoReady()` after navigation
4. **Use semantic selectors** - Prefer text content over CSS classes
5. **Handle loading states** - Use `waitForLoadingComplete()`
6. **Retry flaky selectors** - Use Playwright's auto-waiting features
7. **Don't hardcode URLs** - Use `baseURL` from config
8. **Test isolation** - Each test should be independent

## Configuration

### Browsers

Tests run on Chromium, Firefox, and WebKit by default. Modify `playwright.config.ts` to change:

```typescript
projects: [
  { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
  // { name: 'firefox', use: { ...devices['Desktop Firefox'] } },  // Disable
  // { name: 'webkit', use: { ...devices['Desktop Safari'] } },     // Disable
],
```

### Parallel Execution

Adjust workers in `playwright.config.ts`:

```typescript
workers: process.env.CI ? 1 : 4,  // 4 parallel workers locally
```

### Screenshots and Videos

Configure in `.env.e2e`:

```env
SCREENSHOT_ON_FAILURE=true
VIDEO_ON_FAILURE=true
```

## Maintenance

### Updating Playwright

```bash
cd tests/e2e
npm update @playwright/test
npx playwright install
```

### Updating Selectors

If PHProjekt UI changes, update selectors in:

- `utils/auth.ts` - Login/logout selectors
- Individual test specs - Feature-specific selectors

## Resources

- [Playwright Documentation](https://playwright.dev/docs/intro)
- [Playwright Best Practices](https://playwright.dev/docs/best-practices)
- [Playwright Codegen](https://playwright.dev/docs/codegen) - Generate tests by recording
- [PHProjekt Documentation](https://phprojekt.com/docs)

## Support

For issues with:
- **Tests**: Open issue in this repository
- **Playwright**: See [Playwright GitHub](https://github.com/microsoft/playwright)
- **PHProjekt**: See main documentation

## License

LGPL-3.0 (same as PHProjekt)
