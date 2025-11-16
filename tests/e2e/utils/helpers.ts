import { Page } from '@playwright/test';

/**
 * Wait for Dojo to be fully loaded
 * PHProjekt uses Dojo toolkit which loads asynchronously
 */
export async function waitForDojoReady(page: Page) {
  await page.waitForFunction(() => {
    // Check if dojo is loaded
    return typeof (window as any).dojo !== 'undefined' &&
           typeof (window as any).dijit !== 'undefined';
  }, { timeout: 10000 }).catch(() => {
    // If dojo check fails, just wait for network idle
    return page.waitForLoadState('networkidle');
  });
}

/**
 * Generate random string for unique test data
 */
export function randomString(length: number = 8): string {
  const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
  let result = '';
  for (let i = 0; i < length; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return result;
}

/**
 * Generate timestamp for unique test data
 */
export function timestamp(): string {
  return Date.now().toString();
}

/**
 * Wait for loading overlay to disappear
 * PHProjekt shows loading overlays during operations
 */
export async function waitForLoadingComplete(page: Page) {
  // Wait for loading overlay to appear and then disappear
  try {
    await page.locator('.loadingOverlay, .dijitDialogUnderlay, [class*="loading"]').first().waitFor({ state: 'hidden', timeout: 30000 });
  } catch {
    // Loading overlay might not appear for fast operations, that's ok
  }
}

/**
 * Wait for notification/message to appear
 */
export async function waitForNotification(page: Page, messageText?: string) {
  const selector = messageText
    ? `text=${messageText}`
    : '.notification, .message, .success, .error, [class*="toast"]';

  await page.locator(selector).first().waitFor({ timeout: 5000 });
}

/**
 * Dismiss notification/message if present
 */
export async function dismissNotification(page: Page) {
  const closeButton = page.locator('.notification .close, .message .close, [class*="toast"] .close').first();
  if (await closeButton.isVisible()) {
    await closeButton.click();
  }
}
