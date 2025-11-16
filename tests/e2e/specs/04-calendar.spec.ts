import { test, expect } from '@playwright/test';
import { login } from '../utils/auth';
import { config } from '../utils/env';
import { randomString, waitForLoadingComplete, waitForDojoReady } from '../utils/helpers';

/**
 * Calendar Event Management Tests
 * Tests for PHProjekt Calendar2 module
 */
test.describe('Calendar Event Management', () => {
  let eventTitle: string;

  test.beforeEach(async ({ page }) => {
    // Login before each test
    await login(page, config.testUser.username, config.testUser.password);

    // Wait for Dojo to be ready
    await waitForDojoReady(page);

    // Generate unique event title
    eventTitle = `Test Event ${randomString()}`;
  });

  test('should navigate to calendar module', async ({ page }) => {
    await page.waitForLoadState('networkidle');

    // Click on Calendar module
    const calendarLink = page.locator('text=/^calendar$/i, [href*="Calendar"]').first();
    await calendarLink.click();

    await waitForDojoReady(page);

    // Should see calendar view
    const calendarView = page.locator('.calendarView, .calendar, [id*="calendar"]').first();
    await expect(calendarView).toBeVisible({ timeout: 10000 });
  });

  test('should create a new calendar event', async ({ page }) => {
    // Navigate to Calendar
    const calendarLink = page.locator('text=/^calendar$/i, [href*="Calendar"]').first();
    await calendarLink.click();
    await waitForDojoReady(page);

    // Click Add/New Event button
    const addButton = page.locator('button:has-text("Add"), button:has-text("New"), .addButton, button:has-text("Event")').first();
    await addButton.click();

    // Wait for event form to appear
    await page.waitForSelector('input[name="title"], input[id*="title"], input[name="summary"]', { timeout: 10000 });

    // Fill in event title/summary
    const titleField = page.locator('input[name="title"], input[id*="title"], input[name="summary"]').first();
    await titleField.fill(eventTitle);

    // Fill in start date/time (if separate fields)
    const startDateField = page.locator('input[name="startDate"], input[id*="startDate"], input[name="start"]').first();
    if (await startDateField.isVisible({ timeout: 2000 }).catch(() => false)) {
      // Set to today or tomorrow
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      const dateStr = tomorrow.toISOString().split('T')[0]; // YYYY-MM-DD
      await startDateField.fill(dateStr);
    }

    // Fill in start time
    const startTimeField = page.locator('input[name="startTime"], input[id*="startTime"]').first();
    if (await startTimeField.isVisible({ timeout: 2000 }).catch(() => false)) {
      await startTimeField.fill('10:00');
    }

    // Fill in end time
    const endTimeField = page.locator('input[name="endTime"], input[id*="endTime"]').first();
    if (await endTimeField.isVisible({ timeout: 2000 }).catch(() => false)) {
      await endTimeField.fill('11:00');
    }

    // Fill in notes/description if available
    const notesField = page.locator('textarea[name="notes"], textarea[name="description"], textarea[id*="notes"]').first();
    if (await notesField.isVisible({ timeout: 1000 }).catch(() => false)) {
      await notesField.fill('Test event created by E2E automation');
    }

    // Save the event
    const saveButton = page.locator('button:has-text("Save"), input[value="Save"]').first();
    await saveButton.click();

    await waitForLoadingComplete(page);
    await page.waitForLoadState('networkidle');

    // Verify event was created
    const successMessage = page.locator('text=/success/i, .success').first();
    const eventInCalendar = page.locator(`text="${eventTitle}"`).first();

    const hasSuccess = await successMessage.isVisible({ timeout: 5000 }).catch(() => false);
    const inCalendar = await eventInCalendar.isVisible({ timeout: 5000 }).catch(() => false);

    expect(hasSuccess || inCalendar).toBeTruthy();
  });

  test('should edit an existing calendar event', async ({ page }) => {
    // Navigate to Calendar
    const calendarLink = page.locator('text=/^calendar$/i, [href*="Calendar"]').first();
    await calendarLink.click();
    await waitForDojoReady(page);

    // Create an event first
    const addButton = page.locator('button:has-text("Add"), button:has-text("New")').first();
    await addButton.click();
    await page.waitForSelector('input[name="title"], input[name="summary"]', { timeout: 10000 });

    const titleField = page.locator('input[name="title"], input[name="summary"]').first();
    await titleField.fill(eventTitle);

    const saveButton = page.locator('button:has-text("Save")').first();
    await saveButton.click();
    await waitForLoadingComplete(page);

    // Click on the event in calendar
    const eventInCalendar = page.locator(`text="${eventTitle}"`).first();
    await eventInCalendar.click();
    await waitForDojoReady(page);

    // Modify the title
    const updatedTitle = `${eventTitle} (Updated)`;
    const editTitleField = page.locator('input[name="title"], input[name="summary"]').first();
    await editTitleField.clear();
    await editTitleField.fill(updatedTitle);

    // Update notes if available
    const notesField = page.locator('textarea[name="notes"], textarea[name="description"]').first();
    if (await notesField.isVisible({ timeout: 1000 }).catch(() => false)) {
      await notesField.fill('Updated event description');
    }

    // Save changes
    const updateSaveButton = page.locator('button:has-text("Save")').first();
    await updateSaveButton.click();
    await waitForLoadingComplete(page);

    // Verify update
    const updatedEventInCalendar = page.locator(`text="${updatedTitle}"`).first();
    await expect(updatedEventInCalendar).toBeVisible({ timeout: 5000 });
  });

  test('should delete a calendar event', async ({ page }) => {
    // Navigate to Calendar
    const calendarLink = page.locator('text=/^calendar$/i, [href*="Calendar"]').first();
    await calendarLink.click();
    await waitForDojoReady(page);

    // Create an event
    const addButton = page.locator('button:has-text("Add"), button:has-text("New")').first();
    await addButton.click();
    await page.waitForSelector('input[name="title"], input[name="summary"]', { timeout: 10000 });

    const titleField = page.locator('input[name="title"], input[name="summary"]').first();
    await titleField.fill(eventTitle);

    const saveButton = page.locator('button:has-text("Save")').first();
    await saveButton.click();
    await waitForLoadingComplete(page);

    // Click on the event
    const eventInCalendar = page.locator(`text="${eventTitle}"`).first();
    await eventInCalendar.click();
    await waitForDojoReady(page);

    // Delete the event
    const deleteButton = page.locator('button:has-text("Delete"), .deleteButton').first();
    await deleteButton.click();

    // Confirm deletion if dialog appears
    const confirmButton = page.locator('button:has-text("Yes"), button:has-text("OK"), button:has-text("Confirm")').first();
    if (await confirmButton.isVisible({ timeout: 2000 }).catch(() => false)) {
      await confirmButton.click();
    }

    await waitForLoadingComplete(page);

    // Verify event is deleted
    await expect(page.locator(`text="${eventTitle}"`).first()).not.toBeVisible({ timeout: 5000 });
  });

  test('should create a recurring event', async ({ page }) => {
    // Navigate to Calendar
    const calendarLink = page.locator('text=/^calendar$/i, [href*="Calendar"]').first();
    await calendarLink.click();
    await waitForDojoReady(page);

    // Create event
    const addButton = page.locator('button:has-text("Add"), button:has-text("New")').first();
    await addButton.click();
    await page.waitForSelector('input[name="title"], input[name="summary"]', { timeout: 10000 });

    const titleField = page.locator('input[name="title"], input[name="summary"]').first();
    await titleField.fill(`${eventTitle} (Recurring)`);

    // Look for recurrence/repeat option
    const recurrenceCheckbox = page.locator('input[type="checkbox"][name*="recur"], input[type="checkbox"][id*="recur"], input[type="checkbox"][name*="repeat"]').first();
    if (await recurrenceCheckbox.isVisible({ timeout: 2000 }).catch(() => false)) {
      await recurrenceCheckbox.check();

      // Wait for recurrence options to appear
      await page.waitForTimeout(500);

      // Select recurrence pattern (e.g., daily, weekly)
      const recurrencePattern = page.locator('select[name*="recurrence"], select[id*="recurrence"], select[name*="rrule"]').first();
      if (await recurrencePattern.isVisible({ timeout: 1000 }).catch(() => false)) {
        await recurrencePattern.selectOption({ index: 1 }); // Select first option (e.g., daily)
      }
    }

    // Save
    const saveButton = page.locator('button:has-text("Save")').first();
    await saveButton.click();
    await waitForLoadingComplete(page);

    // Verify created
    const eventInCalendar = page.locator(`text="${eventTitle} (Recurring)"`).first();
    await expect(eventInCalendar).toBeVisible({ timeout: 5000 });
  });

  test('should switch between calendar views', async ({ page }) => {
    // Navigate to Calendar
    const calendarLink = page.locator('text=/^calendar$/i, [href*="Calendar"]').first();
    await calendarLink.click();
    await waitForDojoReady(page);

    // Try to switch to different views
    const viewButtons = [
      { name: 'day', selector: 'button:has-text("Day"), .dayView, [data-view="day"]' },
      { name: 'week', selector: 'button:has-text("Week"), .weekView, [data-view="week"]' },
      { name: 'month', selector: 'button:has-text("Month"), .monthView, [data-view="month"]' },
    ];

    for (const view of viewButtons) {
      const viewButton = page.locator(view.selector).first();
      if (await viewButton.isVisible({ timeout: 2000 }).catch(() => false)) {
        await viewButton.click();
        await page.waitForTimeout(500);
        // View should change
        await waitForDojoReady(page);
      }
    }

    // At least one view should be visible
    const calendarView = page.locator('.calendarView, .calendar, [id*="calendar"]').first();
    await expect(calendarView).toBeVisible();
  });

  test('should navigate between dates', async ({ page }) => {
    // Navigate to Calendar
    const calendarLink = page.locator('text=/^calendar$/i, [href*="Calendar"]').first();
    await calendarLink.click();
    await waitForDojoReady(page);

    // Look for next/previous navigation buttons
    const nextButton = page.locator('button:has-text("Next"), .nextButton, [class*="next"]').first();
    const prevButton = page.locator('button:has-text("Previous"), button:has-text("Prev"), .prevButton, [class*="prev"]').first();

    // Click next if available
    if (await nextButton.isVisible({ timeout: 2000 }).catch(() => false)) {
      await nextButton.click();
      await page.waitForTimeout(500);
      await waitForDojoReady(page);

      // Calendar should still be visible
      const calendarView = page.locator('.calendarView, .calendar').first();
      await expect(calendarView).toBeVisible();
    }

    // Click previous if available
    if (await prevButton.isVisible({ timeout: 2000 }).catch(() => false)) {
      await prevButton.click();
      await page.waitForTimeout(500);
      await waitForDojoReady(page);

      // Calendar should still be visible
      const calendarView = page.locator('.calendarView, .calendar').first();
      await expect(calendarView).toBeVisible();
    }
  });

  test('should create an all-day event', async ({ page }) => {
    // Navigate to Calendar
    const calendarLink = page.locator('text=/^calendar$/i, [href*="Calendar"]').first();
    await calendarLink.click();
    await waitForDojoReady(page);

    // Create event
    const addButton = page.locator('button:has-text("Add"), button:has-text("New")').first();
    await addButton.click();
    await page.waitForSelector('input[name="title"], input[name="summary"]', { timeout: 10000 });

    const titleField = page.locator('input[name="title"], input[name="summary"]').first();
    await titleField.fill(`${eventTitle} (All Day)`);

    // Look for all-day checkbox
    const allDayCheckbox = page.locator('input[type="checkbox"][name*="allDay"], input[type="checkbox"][id*="allDay"], input[type="checkbox"][name*="all-day"]').first();
    if (await allDayCheckbox.isVisible({ timeout: 2000 }).catch(() => false)) {
      await allDayCheckbox.check();
    }

    // Save
    const saveButton = page.locator('button:has-text("Save")').first();
    await saveButton.click();
    await waitForLoadingComplete(page);

    // Verify created
    const eventInCalendar = page.locator(`text="${eventTitle} (All Day)"`).first();
    await expect(eventInCalendar).toBeVisible({ timeout: 5000 });
  });
});
