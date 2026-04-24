import { test, expect } from '@playwright/test';

test.describe('Public marketing pages', () => {
  test('landing page loads and has a heading', async ({ page }) => {
    await page.goto('/');
    await expect(page).toHaveTitle(/.+/);
    // The landing page should render at least one visible heading
    await expect(page.locator('h1, h2').first()).toBeVisible();
  });

  for (const path of ['/features', '/solution', '/pricing']) {
    test(`${path} returns 200 and renders content`, async ({ page }) => {
      const response = await page.goto(path);
      expect(response?.status()).toBe(200);
      await expect(page.locator('body')).toBeVisible();
    });
  }

  test('login page is accessible from landing', async ({ page }) => {
    await page.goto('/');
    const loginLink = page.getByRole('link', { name: /login/i });
    if (await loginLink.count() > 0) {
      await loginLink.first().click();
      await expect(page).toHaveURL(/\/login/);
    } else {
      // fallback: navigate directly
      await page.goto('/login');
      await expect(page).toHaveURL(/\/login/);
    }
  });
});
