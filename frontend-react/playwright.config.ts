import { defineConfig, devices } from '@playwright/test';

/**
 * Playwright E2E Test Configuration for PHProjekt React Frontend
 * @see https://playwright.dev/docs/test-configuration
 */
export default defineConfig({
  testDir: './tests/e2e',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: 'html',

  // Global timeout for each test (default 30s)
  timeout: 60000, // Increased to 60s for slower React hydration

  use: {
    // Include /app/ in baseURL since Vite serves at that path
    // IMPORTANT: Must end with / so relative URLs work correctly
    baseURL: 'http://localhost:3000/app/',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',

    // Wait for network to be idle before considering navigation complete
    actionTimeout: 15000, // Increased action timeout
  },

  projects: [
    {
      name: 'chromium',
      use: {
        ...devices['Desktop Chrome'],
        // headless: false, // Can't use in environments without display
      },
    },
  ],

  webServer: {
    command: 'npm run dev',
    // Check the actual URL where Vite is ready (includes /app/)
    url: 'http://localhost:3000/app/',
    reuseExistingServer: !process.env.CI,
    timeout: 120000,
    // Log webServer output for debugging
    stdout: 'pipe',
    stderr: 'pipe',
  },
});
