import { test, expect } from '@playwright/test';

test('debug - check what loads', async ({ page }) => {
  // Navigate to timecard page
  await page.goto('/timecard');

  // Wait a bit for page to load
  await page.waitForTimeout(2000);

  // Log the page title
  const title = await page.title();
  console.log('Page title:', title);

  // Log the page HTML
  const html = await page.content();
  console.log('Page HTML length:', html.length);
  console.log('HTML preview:', html.substring(0, 500));

  // Check if root div exists
  const root = await page.locator('#root').count();
  console.log('Root div count:', root);

  // Get root div content
  if (root > 0) {
    const rootContent = await page.locator('#root').innerHTML();
    console.log('Root innerHTML:', rootContent.substring(0, 300));
  }

  // Log any console errors
  page.on('console', msg => console.log('Browser console:', msg.type(), msg.text()));
  page.on('pageerror', error => console.log('Page error:', error.message));

  // Take a screenshot
  await page.screenshot({ path: '/tmp/debug-screenshot.png' });

  console.log('Screenshot saved to /tmp/debug-screenshot.png');
});
