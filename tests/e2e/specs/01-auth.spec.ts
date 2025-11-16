import { test, expect } from '@playwright/test';
import { login, logout } from '../utils/auth';
import { config } from '../utils/env';

test.describe('Authentication', () => {
  test.beforeEach(async ({ page }) => {
    // Ensure we start from a clean state
    await page.goto('/');
  });

  test('should successfully login with valid credentials', async ({ page }) => {
    // Perform login
    await login(page, config.testUser.username, config.testUser.password);

    // Verify we're on the main application page
    await expect(page).toHaveURL(/index\.php|\/$/);

    // Verify logout button is visible (confirms we're logged in)
    const logoutElement = page.locator('text=/logout/i, .logoutButton, [id*="logout"]').first();
    await expect(logoutElement).toBeVisible({ timeout: 10000 });
  });

  test('should fail login with invalid credentials', async ({ page }) => {
    // Navigate to login page
    await page.goto('/');

    // Fill in invalid credentials
    const usernameField = page.locator('input[name="username"], input[name="login"]').first();
    await usernameField.fill('invaliduser');

    const passwordField = page.locator('input[type="password"]').first();
    await passwordField.fill('wrongpassword');

    // Submit the form
    const loginButton = page.locator('button[type="submit"], input[type="submit"], button:has-text("Login")').first();
    await loginButton.click();

    // Wait for response
    await page.waitForLoadState('networkidle');

    // Should still be on login page or show error
    // Either we stay on login page or we see an error message
    const stillOnLoginPage = await page.locator('input[name="username"], input[name="login"]').first().isVisible();
    const errorMessage = await page.locator('.error, .errorMessage, [class*="error"]').first().isVisible().catch(() => false);

    expect(stillOnLoginPage || errorMessage).toBeTruthy();
  });

  test('should successfully logout', async ({ page }) => {
    // First, login
    await login(page, config.testUser.username, config.testUser.password);

    // Verify we're logged in
    await expect(page.locator('text=/logout/i, .logoutButton, [id*="logout"]').first()).toBeVisible();

    // Perform logout
    await logout(page);

    // Verify we're back on login page
    await expect(page.locator('input[name="username"], input[name="login"]').first()).toBeVisible();
  });

  test('should maintain session after page reload', async ({ page }) => {
    // Login
    await login(page, config.testUser.username, config.testUser.password);

    // Reload the page
    await page.reload();
    await page.waitForLoadState('networkidle');

    // Should still be logged in (logout button visible)
    await expect(page.locator('text=/logout/i, .logoutButton, [id*="logout"]').first()).toBeVisible({ timeout: 10000 });
  });

  test('should redirect to login when accessing app without authentication', async ({ page, context }) => {
    // Clear all cookies to ensure we're logged out
    await context.clearCookies();

    // Try to access the main app
    await page.goto('/');
    await page.waitForLoadState('networkidle');

    // Should be on login page
    await expect(page.locator('input[name="username"], input[name="login"]').first()).toBeVisible({ timeout: 5000 });
  });
});
