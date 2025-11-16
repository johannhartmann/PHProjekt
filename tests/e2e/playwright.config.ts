import { defineConfig, devices } from '@playwright/test';
import dotenv from 'dotenv';
import path from 'path';

// Load environment variables from .env.e2e
dotenv.config({ path: path.resolve(__dirname, '.env.e2e') });

/**
 * Playwright configuration for PHProjekt E2E tests
 * @see https://playwright.dev/docs/test-configuration
 */
export default defineConfig({
  testDir: './specs',

  // Maximum time one test can run for
  timeout: parseInt(process.env.TEST_TIMEOUT || '30000'),

  // Test execution configuration
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: parseInt(process.env.RETRIES || '2'),
  workers: process.env.CI ? 1 : undefined,

  // Reporter to use
  reporter: [
    ['html'],
    ['list'],
    ['json', { outputFile: 'test-results/results.json' }]
  ],

  // Shared settings for all projects
  use: {
    // Base URL for the application
    baseURL: process.env.BASE_URL || 'http://localhost:8080',

    // Browser context options
    trace: 'on-first-retry',
    screenshot: process.env.SCREENSHOT_ON_FAILURE === 'true' ? 'only-on-failure' : 'off',
    video: process.env.VIDEO_ON_FAILURE === 'true' ? 'retain-on-failure' : 'off',

    // Navigation timeout
    navigationTimeout: parseInt(process.env.NAVIGATION_TIMEOUT || '30000'),

    // Slow down execution (useful for debugging)
    launchOptions: {
      slowMo: parseInt(process.env.SLOW_MO || '0'),
    },
  },

  // Configure projects for major browsers
  projects: [
    {
      name: 'chromium',
      use: {
        ...devices['Desktop Chrome'],
        viewport: { width: 1920, height: 1080 },
      },
    },

    {
      name: 'firefox',
      use: {
        ...devices['Desktop Firefox'],
        viewport: { width: 1920, height: 1080 },
      },
    },

    {
      name: 'webkit',
      use: {
        ...devices['Desktop Safari'],
        viewport: { width: 1920, height: 1080 },
      },
    },

    // Mobile viewports (optional, uncomment if needed)
    // {
    //   name: 'Mobile Chrome',
    //   use: { ...devices['Pixel 5'] },
    // },
    // {
    //   name: 'Mobile Safari',
    //   use: { ...devices['iPhone 12'] },
    // },
  ],

  // Run your local dev server before starting the tests
  // Uncomment if you want Playwright to manage the server
  // webServer: {
  //   command: 'docker-compose up',
  //   url: 'http://localhost:8080',
  //   reuseExistingServer: !process.env.CI,
  //   timeout: 120000,
  // },
});
