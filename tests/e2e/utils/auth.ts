import { Page, expect } from '@playwright/test';

/**
 * Login helper function
 * Performs login with the given credentials
 */
export async function login(page: Page, username: string, password: string) {
  // Navigate to login page (assuming root redirects to login if not authenticated)
  await page.goto('/');

  // Wait for login form to be visible
  await page.waitForSelector('input[name="username"], input[name="login"]', { timeout: 10000 });

  // Fill in credentials (PHProjekt uses 'username' or 'login' field name)
  const usernameField = await page.locator('input[name="username"], input[name="login"]').first();
  await usernameField.fill(username);

  const passwordField = await page.locator('input[type="password"]').first();
  await passwordField.fill(password);

  // Submit the form
  const loginButton = await page.locator('button[type="submit"], input[type="submit"], button:has-text("Login"), input[value*="Login"]').first();
  await loginButton.click();

  // Wait for navigation to complete and verify we're logged in
  // PHProjekt typically shows the main application UI after login
  await page.waitForLoadState('networkidle');

  // Verify login was successful (look for common post-login elements)
  // This could be the logout button, main menu, or project tree
  await expect(page.locator('text=/logout/i, .logoutButton, [id*="logout"]').first()).toBeVisible({ timeout: 10000 })
    .catch(() => {
      // Alternative check: look for main application UI
      return expect(page.locator('.phprContainer, #centerMainContent, .mainContent').first()).toBeVisible({ timeout: 5000 });
    });
}

/**
 * Logout helper function
 * Performs logout from the application
 */
export async function logout(page: Page) {
  // Look for logout button/link
  const logoutButton = await page.locator('text=/logout/i, .logoutButton, [id*="logout"]').first();

  // Click logout
  await logoutButton.click();

  // Wait for redirect to login page
  await page.waitForLoadState('networkidle');

  // Verify we're on the login page
  await expect(page.locator('input[name="username"], input[name="login"]').first()).toBeVisible({ timeout: 5000 });
}

/**
 * Check if user is logged in
 */
export async function isLoggedIn(page: Page): Promise<boolean> {
  try {
    // Check for logout button or main UI
    await page.locator('text=/logout/i, .logoutButton, [id*="logout"], .phprContainer').first().waitFor({ timeout: 3000 });
    return true;
  } catch {
    return false;
  }
}
