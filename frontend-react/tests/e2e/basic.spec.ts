import { test, expect } from '@playwright/test';

test('basic - can page load HTML', async ({ page }) => {
  console.log('Navigating to timecard (baseURL includes /app/)...');

  // Use relative path without leading slash for proper baseURL resolution
  // Use 'load' instead of 'networkidle' to avoid timeout issues
  await page.goto('timecard', { waitUntil: 'load' });

  console.log('Page loaded, getting content...');

  // Get the full HTML
  const html = await page.content();
  console.log('HTML length:', html.length);
  console.log('HTML preview:', html.substring(0, 1000));

  // Check title
  const title = await page.title();
  console.log('Page title:', title);
  expect(title).toBe('PHProjekt');

  // Check for root div
  const rootExists = await page.locator('#root').count();
  console.log('Root div count:', rootExists);
  expect(rootExists).toBeGreaterThan(0);

  // Wait a moment for React to render
  await page.waitForTimeout(5000);

  // Get root content
  const rootHTML = await page.locator('#root').innerHTML();
  console.log('Root innerHTML length:', rootHTML.length);
  console.log('Root innerHTML preview:', rootHTML.substring(0, 500));

  // Take screenshot
  await page.screenshot({ path: '/tmp/basic-test.png', fullPage: true });
  console.log('Screenshot saved to /tmp/basic-test.png');

  // Try to find any h1
  const h1Count = await page.locator('h1').count();
  console.log('H1 count:', h1Count);

  if (h1Count > 0) {
    const h1Text = await page.locator('h1').first().textContent();
    console.log('First H1 text:', h1Text);
  }
});
