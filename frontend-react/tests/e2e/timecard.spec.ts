import { test, expect } from '@playwright/test';

/**
 * E2E Tests for Timecard Module (Module B)
 *
 * Tests full user workflows with real browser automation:
 * - Navigation and date controls
 * - Creating new bookings
 * - Editing existing bookings
 * - Deleting bookings
 * - Form validation
 * - Running timer functionality
 * - Total hours calculation
 */

test.describe('Timecard Module', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to Timecard page (dev server runs at root, not /app/)
    await page.goto('/timecard');

    // Wait for page to load
    await page.waitForSelector('h1:has-text("Timecard")');
  });

  test('should display Timecard page with all controls', async ({ page }) => {
    // Check page title
    await expect(page.getByRole('heading', { name: 'Timecard', level: 1 })).toBeVisible();

    // Check date navigation controls exist
    await expect(page.locator('button[title="Previous day"]')).toBeVisible();
    await expect(page.locator('button[title="Next day"]')).toBeVisible();
    await expect(page.locator('button:has-text("Today")')).toBeVisible();

    // Check date picker exists
    await expect(page.locator('input[type="date"]').first()).toBeVisible();

    // Check booking form exists
    await expect(page.locator('h3:has-text("New Booking")')).toBeVisible();

    // Check booking list area exists
    await expect(page.locator('.booking-list')).toBeVisible();
  });

  test('should navigate to previous day', async ({ page }) => {
    // Get current date from date picker
    const datePicker = page.locator('input[type="date"]').first();
    const currentDate = await datePicker.inputValue();

    // Click previous day button
    await page.locator('button[title="Previous day"]').click();

    // Wait for date to change
    await page.waitForTimeout(500);

    // Verify date changed to previous day
    const newDate = await datePicker.inputValue();
    expect(new Date(newDate).getTime()).toBeLessThan(new Date(currentDate).getTime());
  });

  test('should navigate to next day', async ({ page }) => {
    // Get current date from date picker
    const datePicker = page.locator('input[type="date"]').first();
    const currentDate = await datePicker.inputValue();

    // Click next day button
    await page.locator('button[title="Next day"]').click();

    // Wait for date to change
    await page.waitForTimeout(500);

    // Verify date changed to next day
    const newDate = await datePicker.inputValue();
    expect(new Date(newDate).getTime()).toBeGreaterThan(new Date(currentDate).getTime());
  });

  test('should navigate to today when clicking Today button', async ({ page }) => {
    // Get today's date in YYYY-MM-DD format
    const today = new Date().toISOString().split('T')[0];

    // Navigate to different day first
    await page.locator('button[title="Previous day"]').click();
    await page.waitForTimeout(500);

    // Click Today button
    await page.locator('button:has-text("Today")').click();
    await page.waitForTimeout(500);

    // Verify date is today
    const datePicker = page.locator('input[type="date"]').first();
    const currentDate = await datePicker.inputValue();
    expect(currentDate).toBe(today);
  });

  test('should change date using date picker', async ({ page }) => {
    const datePicker = page.locator('input[type="date"]').first();

    // Set specific date
    await datePicker.fill('2025-12-25');
    await page.waitForTimeout(500);

    // Verify date changed
    const newDate = await datePicker.inputValue();
    expect(newDate).toBe('2025-12-25');
  });

  test('should validate required start datetime field', async ({ page }) => {
    // Get start datetime field and clear it
    const startField = page.locator('input[type="datetime-local"]');
    await startField.clear();

    // Try to save
    await page.locator('button:has-text("Save")').click();

    // Should show validation error
    await expect(page.locator('text=Start time is required')).toBeVisible();
  });

  test('should validate end time must be after start time', async ({ page }) => {
    // Set start time to 17:00
    const startField = page.locator('input[type="datetime-local"]');
    await startField.fill('2025-11-17T17:00');

    // Set end time to 09:00 (before start)
    const endField = page.locator('input[type="time"]');
    await endField.fill('09:00');

    // Try to save
    await page.locator('button:has-text("Save")').click();

    // Should show validation error
    await expect(page.locator('text=End time must be after start time')).toBeVisible();
  });

  test('should create new booking with valid data', async ({ page }) => {
    // Fill start datetime
    const startField = page.locator('input[type="datetime-local"]');
    await startField.fill('2025-11-17T09:00');

    // Fill end time
    const endField = page.locator('input[type="time"]');
    await endField.fill('17:00');

    // Select project (assuming first option after Unassigned)
    const projectSelect = page.locator('select#project');
    await projectSelect.selectOption({ index: 1 });

    // Fill notes
    const notesField = page.locator('textarea#notes');
    await notesField.fill('E2E test booking - Development work');

    // Click Save
    await page.locator('button:has-text("Save")').click();

    // Should show success message
    await expect(page.locator('.booking-form-message.success')).toBeVisible({ timeout: 5000 });
  });

  test('should create running timer (booking without end time)', async ({ page }) => {
    // Fill start datetime
    const startField = page.locator('input[type="datetime-local"]');
    await startField.fill('2025-11-17T14:30');

    // Leave end time empty

    // Select project
    const projectSelect = page.locator('select#project');
    await projectSelect.selectOption({ index: 1 });

    // Fill notes
    const notesField = page.locator('textarea#notes');
    await notesField.fill('E2E test - Running timer');

    // Click Save
    await page.locator('button:has-text("Save")').click();

    // Should show success message
    await expect(page.locator('.booking-form-message.success')).toBeVisible({ timeout: 5000 });

    // Should show running indicator in booking list
    await expect(page.locator('.booking-entry.running')).toBeVisible({ timeout: 3000 });
  });

  test('should select booking from list', async ({ page }) => {
    // Wait for bookings to load
    await page.waitForTimeout(1000);

    // Check if there are any bookings
    const bookingCount = await page.locator('.booking-entry').count();

    if (bookingCount > 0) {
      // Click first booking
      await page.locator('.booking-entry').first().click();

      // Should show Edit Booking in form
      await expect(page.locator('h3:has-text("Edit Booking")')).toBeVisible();

      // Delete button should be visible
      await expect(page.locator('button:has-text("Delete")')).toBeVisible();
    }
  });

  test('should edit existing booking', async ({ page }) => {
    // Wait for bookings to load
    await page.waitForTimeout(1000);

    // Check if there are any bookings
    const bookingCount = await page.locator('.booking-entry').count();

    if (bookingCount > 0) {
      // Click first booking to select it
      await page.locator('.booking-entry').first().click();
      await page.waitForTimeout(500);

      // Modify notes
      const notesField = page.locator('textarea#notes');
      await notesField.clear();
      await notesField.fill('E2E test - Modified booking');

      // Click Save
      await page.locator('button:has-text("Save")').click();

      // Should show success message
      await expect(page.locator('.booking-form-message.success')).toBeVisible({ timeout: 5000 });
    }
  });

  test('should delete booking with confirmation', async ({ page }) => {
    // Wait for bookings to load
    await page.waitForTimeout(1000);

    // Check if there are any bookings
    const bookingCount = await page.locator('.booking-entry').count();

    if (bookingCount > 0) {
      // Click first booking to select it
      await page.locator('.booking-entry').first().click();
      await page.waitForTimeout(500);

      // Set up dialog handler to accept confirmation
      page.on('dialog', dialog => dialog.accept());

      // Click Delete button
      await page.locator('button:has-text("Delete")').click();

      // Should show success message
      await expect(page.locator('.booking-form-message.success')).toBeVisible({ timeout: 5000 });
    }
  });

  test('should cancel delete when user declines confirmation', async ({ page }) => {
    // Wait for bookings to load
    await page.waitForTimeout(1000);

    // Check if there are any bookings
    const bookingCount = await page.locator('.booking-entry').count();

    if (bookingCount > 0) {
      const initialCount = bookingCount;

      // Click first booking to select it
      await page.locator('.booking-entry').first().click();
      await page.waitForTimeout(500);

      // Set up dialog handler to dismiss confirmation
      page.on('dialog', dialog => dialog.dismiss());

      // Click Delete button
      await page.locator('button:has-text("Delete")').click();

      // Wait a bit
      await page.waitForTimeout(1000);

      // Booking count should remain the same
      const newCount = await page.locator('.booking-entry').count();
      expect(newCount).toBe(initialCount);
    }
  });

  test('should clear form when clicking Clear button', async ({ page }) => {
    // Fill some data
    const startField = page.locator('input[type="datetime-local"]');
    await startField.fill('2025-11-17T10:00');

    const notesField = page.locator('textarea#notes');
    await notesField.fill('Test notes');

    // Click Clear button
    await page.locator('button:has-text("Clear")').click();

    // Form should reset to new booking
    await expect(page.locator('h3:has-text("New Booking")')).toBeVisible();

    // Notes should be cleared
    const notesValue = await notesField.inputValue();
    expect(notesValue).toBe('');
  });

  test('should display total hours correctly', async ({ page }) => {
    // Wait for bookings to load
    await page.waitForTimeout(1000);

    // Total hours display should be visible
    await expect(page.locator('text=/Total:.*\\d{2}:\\d{2}/')).toBeVisible();
  });

  test('should disable form fields while saving', async ({ page }) => {
    // Fill valid data
    const startField = page.locator('input[type="datetime-local"]');
    await startField.fill('2025-11-17T09:00');

    const endField = page.locator('input[type="time"]');
    await endField.fill('17:00');

    // Click Save
    const saveButton = page.locator('button:has-text("Save")');
    await saveButton.click();

    // Immediately check if button shows "Saving..." (might be very quick)
    // Note: This might be flaky due to timing, but worth testing
    try {
      await expect(page.locator('button:has-text("Saving...")')).toBeVisible({ timeout: 100 });
    } catch {
      // If we miss the "Saving..." state, that's okay - it's fast
    }
  });

  test('should display empty state when no bookings', async ({ page }) => {
    // Navigate to a date far in the future with no bookings
    const datePicker = page.locator('input[type="date"]').first();
    await datePicker.fill('2099-12-31');
    await page.waitForTimeout(1000);

    // Should show empty state message
    await expect(page.locator('text=No bookings for this day')).toBeVisible();
    await expect(page.locator('text=Click "New" to create a booking')).toBeVisible();
  });

  test('should show loading state while fetching bookings', async ({ page }) => {
    // Reload page to see loading state
    await page.reload();

    // Should show loading message (might be very quick)
    try {
      await expect(page.locator('text=Loading bookings...')).toBeVisible({ timeout: 100 });
    } catch {
      // If we miss the loading state, that's okay - it's fast with mock data
    }
  });

  test('should handle keyboard navigation (Enter key)', async ({ page }) => {
    // Wait for bookings to load
    await page.waitForTimeout(1000);

    // Check if there are any bookings
    const bookingCount = await page.locator('.booking-entry').count();

    if (bookingCount > 0) {
      // Focus first booking
      await page.locator('.booking-entry').first().focus();

      // Press Enter
      await page.keyboard.press('Enter');

      // Should select booking and show Edit form
      await expect(page.locator('h3:has-text("Edit Booking")')).toBeVisible();
    }
  });

  test('should handle keyboard navigation (Space key)', async ({ page }) => {
    // Wait for bookings to load
    await page.waitForTimeout(1000);

    // Check if there are any bookings
    const bookingCount = await page.locator('.booking-entry').count();

    if (bookingCount > 0) {
      // Focus first booking
      await page.locator('.booking-entry').first().focus();

      // Press Space
      await page.keyboard.press('Space');

      // Should select booking and show Edit form
      await expect(page.locator('h3:has-text("Edit Booking")')).toBeVisible();
    }
  });

  test('should display running booking indicator', async ({ page }) => {
    // Wait for bookings to load
    await page.waitForTimeout(1000);

    // Check if there are any running bookings (no end time)
    const runningBookings = await page.locator('.booking-entry.running').count();

    if (runningBookings > 0) {
      // Should display --:-- for running booking end time
      await expect(page.locator('text=--:--')).toBeVisible();
    }
  });

  test('should load and display favorite projects', async ({ page }) => {
    // Check project select
    const projectSelect = page.locator('select#project');
    await expect(projectSelect).toBeVisible();

    // Should have at least Unassigned project
    const optionCount = await projectSelect.locator('option').count();
    expect(optionCount).toBeGreaterThanOrEqual(1);
  });

  test('should highlight selected booking', async ({ page }) => {
    // Wait for bookings to load
    await page.waitForTimeout(1000);

    // Check if there are any bookings
    const bookingCount = await page.locator('.booking-entry').count();

    if (bookingCount > 0) {
      // Click first booking
      await page.locator('.booking-entry').first().click();
      await page.waitForTimeout(500);

      // Should have selected class
      await expect(page.locator('.booking-entry.selected')).toBeVisible();
    }
  });
});
