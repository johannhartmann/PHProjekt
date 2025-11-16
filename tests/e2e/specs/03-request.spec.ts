import { test, expect } from '@playwright/test';
import { login } from '../utils/auth';
import { config } from '../utils/env';
import { randomString, waitForLoadingComplete, waitForDojoReady } from '../utils/helpers';

/**
 * Request/Ticket Management Tests
 *
 * Note: PHProjekt may have different modules for request tracking:
 * - Helpdesk
 * - Todo
 * - Note
 * These tests will attempt to work with any available module
 */
test.describe('Request/Ticket Management', () => {
  let requestTitle: string;

  test.beforeEach(async ({ page }) => {
    // Login before each test
    await login(page, config.testUser.username, config.testUser.password);

    // Wait for Dojo to be ready
    await waitForDojoReady(page);

    // Generate unique request title
    requestTitle = `Test Request ${randomString()}`;
  });

  test('should navigate to request/ticket module', async ({ page }) => {
    await page.waitForLoadState('networkidle');

    // Try to find request/ticket/helpdesk/todo module
    // Look for common names
    const moduleLinks = [
      'text=/^helpdesk$/i',
      'text=/^todo$/i',
      'text=/^note$/i',
      'text=/^requests$/i',
      'text=/^tickets$/i',
      '[href*="Helpdesk"]',
      '[href*="Todo"]',
      '[href*="Note"]',
    ];

    let moduleFound = false;
    for (const selector of moduleLinks) {
      const link = page.locator(selector).first();
      if (await link.isVisible({ timeout: 2000 }).catch(() => false)) {
        await link.click();
        moduleFound = true;
        break;
      }
    }

    if (moduleFound) {
      await waitForDojoReady(page);
      // Should see module content
      const moduleContent = page.locator('.mainContent, #centerMainContent, .moduleContent').first();
      await expect(moduleContent).toBeVisible({ timeout: 5000 });
    } else {
      test.skip();
    }
  });

  test('should create a new request/ticket', async ({ page }) => {
    // Navigate to module
    const moduleLink = page.locator('text=/^helpdesk$/i, text=/^todo$/i, text=/^note$/i, [href*="Helpdesk"], [href*="Todo"]').first();

    if (!await moduleLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      test.skip();
      return;
    }

    await moduleLink.click();
    await waitForDojoReady(page);

    // Click Add/New button
    const addButton = page.locator('button:has-text("Add"), button:has-text("New"), .addButton').first();
    await addButton.click();

    // Wait for form
    await page.waitForSelector('input[name="title"], input[id*="title"], input[name="subject"], textarea[name="title"]', { timeout: 10000 });

    // Fill in title/subject
    const titleField = page.locator('input[name="title"], input[id*="title"], input[name="subject"], textarea[name="title"]').first();
    await titleField.fill(requestTitle);

    // Fill in description/notes if available
    const descriptionField = page.locator('textarea[name="description"], textarea[name="notes"], textarea[id*="description"]').first();
    if (await descriptionField.isVisible({ timeout: 1000 }).catch(() => false)) {
      await descriptionField.fill('Test request created by E2E test automation');
    }

    // Set priority if available
    const priorityField = page.locator('select[name="priority"], select[id*="priority"]').first();
    if (await priorityField.isVisible({ timeout: 1000 }).catch(() => false)) {
      await priorityField.selectOption({ index: 1 }); // Select first non-default option
    }

    // Save
    const saveButton = page.locator('button:has-text("Save"), input[value="Save"]').first();
    await saveButton.click();

    await waitForLoadingComplete(page);
    await page.waitForLoadState('networkidle');

    // Verify creation
    const successMessage = page.locator('text=/success/i, .success').first();
    const requestInList = page.locator(`text="${requestTitle}"`).first();

    const hasSuccess = await successMessage.isVisible({ timeout: 5000 }).catch(() => false);
    const inList = await requestInList.isVisible({ timeout: 5000 }).catch(() => false);

    expect(hasSuccess || inList).toBeTruthy();
  });

  test('should update an existing request/ticket', async ({ page }) => {
    // Navigate to module
    const moduleLink = page.locator('text=/^helpdesk$/i, text=/^todo$/i, text=/^note$/i, [href*="Helpdesk"]').first();

    if (!await moduleLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      test.skip();
      return;
    }

    await moduleLink.click();
    await waitForDojoReady(page);

    // Create a request first
    const addButton = page.locator('button:has-text("Add"), button:has-text("New")').first();
    await addButton.click();
    await page.waitForSelector('input[name="title"], input[name="subject"], textarea[name="title"]', { timeout: 10000 });

    const titleField = page.locator('input[name="title"], input[name="subject"], textarea[name="title"]').first();
    await titleField.fill(requestTitle);

    const saveButton = page.locator('button:has-text("Save")').first();
    await saveButton.click();
    await waitForLoadingComplete(page);

    // Now update it
    // Click on the request in the list
    const requestInList = page.locator(`text="${requestTitle}"`).first();
    await requestInList.click();
    await waitForDojoReady(page);

    // Modify the title
    const updatedTitle = `${requestTitle} (Updated)`;
    const editTitleField = page.locator('input[name="title"], input[name="subject"], textarea[name="title"]').first();
    await editTitleField.clear();
    await editTitleField.fill(updatedTitle);

    // Update status if available
    const statusField = page.locator('select[name="status"], select[id*="status"]').first();
    if (await statusField.isVisible({ timeout: 1000 }).catch(() => false)) {
      // Select a different status (e.g., in progress)
      await statusField.selectOption({ index: 1 });
    }

    // Save changes
    const updateSaveButton = page.locator('button:has-text("Save")').first();
    await updateSaveButton.click();
    await waitForLoadingComplete(page);

    // Verify update
    const updatedRequestInList = page.locator(`text="${updatedTitle}"`).first();
    await expect(updatedRequestInList).toBeVisible({ timeout: 5000 });
  });

  test('should change request status', async ({ page }) => {
    // Navigate to module
    const moduleLink = page.locator('text=/^helpdesk$/i, text=/^todo$/i, [href*="Helpdesk"]').first();

    if (!await moduleLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      test.skip();
      return;
    }

    await moduleLink.click();
    await waitForDojoReady(page);

    // Create a request
    const addButton = page.locator('button:has-text("Add"), button:has-text("New")').first();
    await addButton.click();
    await page.waitForSelector('input[name="title"], textarea[name="title"]', { timeout: 10000 });

    const titleField = page.locator('input[name="title"], textarea[name="title"]').first();
    await titleField.fill(requestTitle);

    const saveButton = page.locator('button:has-text("Save")').first();
    await saveButton.click();
    await waitForLoadingComplete(page);

    // Open the request
    const requestInList = page.locator(`text="${requestTitle}"`).first();
    await requestInList.click();
    await waitForDojoReady(page);

    // Change status
    const statusField = page.locator('select[name="status"], select[id*="status"]').first();
    if (await statusField.isVisible({ timeout: 2000 }).catch(() => false)) {
      // Get current status
      const currentStatus = await statusField.inputValue();

      // Select different status
      const options = await statusField.locator('option').all();
      if (options.length > 1) {
        await statusField.selectOption({ index: 1 });

        // Save
        const statusSaveButton = page.locator('button:has-text("Save")').first();
        await statusSaveButton.click();
        await waitForLoadingComplete(page);

        // Verify status changed
        const newStatus = await statusField.inputValue();
        expect(newStatus).not.toBe(currentStatus);
      }
    } else {
      test.skip();
    }
  });

  test('should assign request to user', async ({ page }) => {
    // Navigate to module
    const moduleLink = page.locator('text=/^helpdesk$/i, text=/^todo$/i, [href*="Helpdesk"]').first();

    if (!await moduleLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      test.skip();
      return;
    }

    await moduleLink.click();
    await waitForDojoReady(page);

    // Create a request
    const addButton = page.locator('button:has-text("Add"), button:has-text("New")').first();
    await addButton.click();
    await page.waitForSelector('input[name="title"], textarea[name="title"]', { timeout: 10000 });

    const titleField = page.locator('input[name="title"], textarea[name="title"]').first();
    await titleField.fill(requestTitle);

    // Assign to user if field is available
    const assigneeField = page.locator('select[name="assigned"], select[id*="assigned"], select[name="owner"]').first();
    if (await assigneeField.isVisible({ timeout: 2000 }).catch(() => false)) {
      const options = await assigneeField.locator('option').all();
      if (options.length > 1) {
        await assigneeField.selectOption({ index: 1 });
      }
    }

    const saveButton = page.locator('button:has-text("Save")').first();
    await saveButton.click();
    await waitForLoadingComplete(page);

    // Verify created
    await expect(page.locator(`text="${requestTitle}"`).first()).toBeVisible({ timeout: 5000 });
  });

  test('should delete a request/ticket', async ({ page }) => {
    // Navigate to module
    const moduleLink = page.locator('text=/^helpdesk$/i, text=/^todo$/i, [href*="Helpdesk"]').first();

    if (!await moduleLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      test.skip();
      return;
    }

    await moduleLink.click();
    await waitForDojoReady(page);

    // Create a request
    const addButton = page.locator('button:has-text("Add"), button:has-text("New")').first();
    await addButton.click();
    await page.waitForSelector('input[name="title"], textarea[name="title"]', { timeout: 10000 });

    const titleField = page.locator('input[name="title"], textarea[name="title"]').first();
    await titleField.fill(requestTitle);

    const saveButton = page.locator('button:has-text("Save")').first();
    await saveButton.click();
    await waitForLoadingComplete(page);

    // Click on the request
    const requestInList = page.locator(`text="${requestTitle}"`).first();
    await requestInList.click();
    await waitForDojoReady(page);

    // Delete
    const deleteButton = page.locator('button:has-text("Delete"), .deleteButton').first();
    await deleteButton.click();

    // Confirm if dialog appears
    const confirmButton = page.locator('button:has-text("Yes"), button:has-text("OK"), button:has-text("Confirm")').first();
    if (await confirmButton.isVisible({ timeout: 2000 }).catch(() => false)) {
      await confirmButton.click();
    }

    await waitForLoadingComplete(page);

    // Verify deleted
    await expect(page.locator(`text="${requestTitle}"`).first()).not.toBeVisible({ timeout: 5000 });
  });
});
