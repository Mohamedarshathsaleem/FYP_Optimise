import { test, expect } from '@playwright/test';
import { USERS, loginAs, logout } from './helpers/auth.js';

/**
 * Access control tests.
 *
 * Route tiers in this app:
 *   - /admin/* with 'superadmin' middleware → superadmin only
 *   - /admin/* with 'permission:module,action' middleware → role-based
 *   - /dashboard and most /admin/* with just 'auth' middleware → any logged-in user
 */

test.describe('Access Control', () => {
  test.describe('Unauthenticated access', () => {
    const protectedPaths = [
      '/dashboard',
      '/admin/users',
      '/admin/permissions',
      '/sec-analysis',
      '/eip-analysis',
    ];

    for (const path of protectedPaths) {
      test(`GET ${path} redirects unauthenticated user to /login`, async ({ page }) => {
        await page.goto(path);
        await expect(page).toHaveURL(/\/login/);
      });
    }
  });

  test.describe('Superadmin-only routes', () => {
    test('superadmin can access /admin/users', async ({ page }) => {
      await loginAs(page, USERS.superadmin.email, USERS.superadmin.password);
      const response = await page.goto('/admin/users');
      // Should not be 403 or redirect to login
      expect(response?.status()).not.toBe(403);
      await expect(page).not.toHaveURL(/\/login/);
    });

    test('external-rem is blocked from /admin/users', async ({ page }) => {
      await loginAs(page, USERS.externalRem.email, USERS.externalRem.password);
      const response = await page.goto('/admin/users');
      // Expect either a 403 response or a redirect away from the admin page
      const isBlocked =
        (response?.status() === 403) ||
        (await page.url()).includes('/login') ||
        !(await page.url()).includes('/admin/users');
      expect(isBlocked).toBe(true);
    });

    test('external-rem is blocked from /admin/permissions', async ({ page }) => {
      await loginAs(page, USERS.externalRem.email, USERS.externalRem.password);
      const response = await page.goto('/admin/permissions');
      const isBlocked =
        (response?.status() === 403) ||
        (await page.url()).includes('/login') ||
        !(await page.url()).includes('/admin/permissions');
      expect(isBlocked).toBe(true);
    });
  });

  test.describe('Session expiry', () => {
    test('clearing cookies on /dashboard redirects to /login', async ({ page, context }) => {
      await loginAs(page, USERS.superadmin.email, USERS.superadmin.password);
      await expect(page).toHaveURL(/\/dashboard/);

      // Simulate session expiry by clearing cookies
      await context.clearCookies();
      await page.goto('/dashboard');
      await expect(page).toHaveURL(/\/login/);
    });
  });
});
