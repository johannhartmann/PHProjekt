import { test, expect } from '@playwright/test';
import { login } from '../utils/auth';
import { config } from '../utils/env';
import { randomString, waitForLoadingComplete, waitForDojoReady } from '../utils/helpers';

test.describe('Project Management', () => {
  let projectName: string;

  test.beforeEach(async ({ page }) => {
    // Login before each test
    await login(page, config.testUser.username, config.testUser.password);

    // Wait for Dojo to be ready
    await waitForDojoReady(page);

    // Generate unique project name for this test
    projectName = `Test Project ${randomString()}`;
  });

  test('should display project list/tree', async ({ page }) => {
    // Navigate to projects or wait for main page to load
    await page.waitForLoadState('networkidle');

    // Look for project tree or project module
    // PHProjekt typically has a project tree/navigation on the left or in the main area
    const projectTree = page.locator('#projectTree, .projectTree, [id*="project"], .treeContainer').first();
    await expect(projectTree).toBeVisible({ timeout: 10000 });

    // Should see at least the root project or "Projects" heading
    const projectsHeading = page.locator('text=/projects/i, text=/project/i').first();
    await expect(projectsHeading).toBeVisible({ timeout: 5000 });
  });

  test('should create a new project', async ({ page }) => {
    // Navigate to Projects module if not already there
    // Click on Projects in the menu/navigation
    const projectsLink = page.locator('text=/^projects$/i, [href*="Project"], .projectModule').first();
    await projectsLink.click().catch(() => {
      // If no link, might already be on projects page
    });

    await page.waitForLoadState('networkidle');
    await waitForDojoReady(page);

    // Click "Add" or "New Project" button
    const addButton = page.locator('button:has-text("Add"), button:has-text("New"), .addButton, [id*="addButton"]').first();
    await addButton.click();

    // Wait for form to appear
    await page.waitForSelector('input[name="title"], input[id*="title"]', { timeout: 10000 });

    // Fill in project details
    const titleField = page.locator('input[name="title"], input[id*="title"]').first();
    await titleField.fill(projectName);

    // Fill in other required fields if present
    const notesField = page.locator('textarea[name="notes"], textarea[id*="notes"]').first();
    if (await notesField.isVisible({ timeout: 1000 }).catch(() => false)) {
      await notesField.fill('Test project created by E2E test');
    }

    // Save the project
    const saveButton = page.locator('button:has-text("Save"), input[value="Save"], .saveButton').first();
    await saveButton.click();

    // Wait for save to complete
    await waitForLoadingComplete(page);
    await page.waitForLoadState('networkidle');

    // Verify project was created
    // Look for success message or the project in the list
    const successMessage = page.locator('text=/success/i, .success, .notification').first();
    const projectInList = page.locator(`text="${projectName}"`).first();

    // At least one should be true
    const hasSuccess = await successMessage.isVisible({ timeout: 5000 }).catch(() => false);
    const inList = await projectInList.isVisible({ timeout: 5000 }).catch(() => false);

    expect(hasSuccess || inList).toBeTruthy();
  });

  test('should edit an existing project', async ({ page }) => {
    // First, create a project to edit
    // Navigate to Projects
    const projectsLink = page.locator('text=/^projects$/i, [href*="Project"]').first();
    await projectsLink.click().catch(() => {});
    await waitForDojoReady(page);

    // Create project
    const addButton = page.locator('button:has-text("Add"), button:has-text("New")').first();
    await addButton.click();
    await page.waitForSelector('input[name="title"], input[id*="title"]', { timeout: 10000 });

    const titleField = page.locator('input[name="title"], input[id*="title"]').first();
    await titleField.fill(projectName);
    const saveButton = page.locator('button:has-text("Save"), input[value="Save"]').first();
    await saveButton.click();
    await waitForLoadingComplete(page);

    // Now edit it
    // Click on the project in the list
    const projectInList = page.locator(`text="${projectName}"`).first();
    await projectInList.click();
    await waitForDojoReady(page);

    // Modify the title
    const updatedTitle = `${projectName} (Updated)`;
    const editTitleField = page.locator('input[name="title"], input[id*="title"]').first();
    await editTitleField.fill(updatedTitle);

    // Save changes
    const updateSaveButton = page.locator('button:has-text("Save"), input[value="Save"]').first();
    await updateSaveButton.click();
    await waitForLoadingComplete(page);

    // Verify update
    const updatedProjectInList = page.locator(`text="${updatedTitle}"`).first();
    await expect(updatedProjectInList).toBeVisible({ timeout: 5000 });
  });

  test('should delete a project', async ({ page }) => {
    // Create a project first
    const projectsLink = page.locator('text=/^projects$/i, [href*="Project"]').first();
    await projectsLink.click().catch(() => {});
    await waitForDojoReady(page);

    const addButton = page.locator('button:has-text("Add"), button:has-text("New")').first();
    await addButton.click();
    await page.waitForSelector('input[name="title"], input[id*="title"]', { timeout: 10000 });

    const titleField = page.locator('input[name="title"], input[id*="title"]').first();
    await titleField.fill(projectName);
    const saveButton = page.locator('button:has-text("Save")').first();
    await saveButton.click();
    await waitForLoadingComplete(page);

    // Click on the project
    const projectInList = page.locator(`text="${projectName}"`).first();
    await projectInList.click();
    await waitForDojoReady(page);

    // Click delete button
    const deleteButton = page.locator('button:has-text("Delete"), .deleteButton, [id*="deleteButton"]').first();
    await deleteButton.click();

    // Confirm deletion if there's a confirmation dialog
    const confirmButton = page.locator('button:has-text("Yes"), button:has-text("OK"), button:has-text("Confirm"), .dijitDialogPaneActionBar button').first();
    if (await confirmButton.isVisible({ timeout: 2000 }).catch(() => false)) {
      await confirmButton.click();
    }

    await waitForLoadingComplete(page);

    // Verify project is no longer in the list
    const deletedProject = page.locator(`text="${projectName}"`).first();
    await expect(deletedProject).not.toBeVisible({ timeout: 5000 });
  });

  test('should filter/search projects', async ({ page }) => {
    // Create a uniquely named project
    const uniqueProjectName = `Unique ${randomString(12)}`;

    const projectsLink = page.locator('text=/^projects$/i, [href*="Project"]').first();
    await projectsLink.click().catch(() => {});
    await waitForDojoReady(page);

    const addButton = page.locator('button:has-text("Add"), button:has-text("New")').first();
    await addButton.click();
    await page.waitForSelector('input[name="title"], input[id*="title"]', { timeout: 10000 });

    const titleField = page.locator('input[name="title"], input[id*="title"]').first();
    await titleField.fill(uniqueProjectName);
    const saveButton = page.locator('button:has-text("Save")').first();
    await saveButton.click();
    await waitForLoadingComplete(page);

    // Use search/filter functionality
    const searchField = page.locator('input[type="search"], input[placeholder*="search" i], .searchField').first();
    if (await searchField.isVisible({ timeout: 3000 }).catch(() => false)) {
      await searchField.fill(uniqueProjectName);
      await page.waitForTimeout(1000); // Wait for search to filter

      // Should see our project
      await expect(page.locator(`text="${uniqueProjectName}"`).first()).toBeVisible();

      // Clear search
      await searchField.clear();
    }
  });
});
