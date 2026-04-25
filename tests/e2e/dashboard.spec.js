import { test, expect } from '@playwright/test';
import { USERS, loginAs } from './helpers/auth.js';

test.describe('Dashboard', () => {
  test('unauthenticated GET /dashboard redirects to /login', async ({ page }) => {
    await page.goto('/dashboard');
    await expect(page).toHaveURL(/\/login/);
  });

  test('authenticated superadmin can access dashboard', async ({ page }) => {
    await loginAs(page, USERS.superadmin.email, USERS.superadmin.password);
    await expect(page).toHaveURL(/\/dashboard/);
    // The dashboard should render a main content area
    await expect(page.locator('main, #content, .content, [class*="dashboard"]').first()).toBeVisible();
  });

  test('authenticated emt user can access dashboard', async ({ page }) => {
    await loginAs(page, USERS.emt.email, USERS.emt.password);
    await expect(page).toHaveURL(/\/dashboard/);
  });

  test('dashboard shows navigation menu', async ({ page }) => {
    await loginAs(page, USERS.superadmin.email, USERS.superadmin.password);
    // Sidebar or top nav should be rendered
    await expect(page.locator('nav, aside, [class*="sidebar"], [class*="navbar"]').first()).toBeVisible();
  });
});
