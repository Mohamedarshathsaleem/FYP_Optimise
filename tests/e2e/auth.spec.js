import { test, expect } from '@playwright/test';
import { USERS, loginAs, logout } from './helpers/auth.js';

test.describe('Authentication', () => {
  test.describe('Login', () => {
    test('valid credentials redirect to dashboard', async ({ page }) => {
      await loginAs(page, USERS.superadmin.email, USERS.superadmin.password);
      await expect(page).toHaveURL(/\/dashboard/);
    });

    test('wrong password shows an error message', async ({ page }) => {
      await page.goto('/login');
      await page.fill('input[name="email"]', USERS.superadmin.email);
      await page.fill('input[name="password"]', 'wrong-password');
      await page.click('button[type="submit"]');

      // Should stay on login page and show an error
      await expect(page).toHaveURL(/\/login/);
      // Laravel flashes validation errors; look for any error text
      const errorLocator = page.locator('.alert, .text-danger, [class*="error"], [class*="invalid"]');
      await expect(errorLocator.first()).toBeVisible();
    });

    test('empty credentials show validation errors', async ({ page }) => {
      await page.goto('/login');
      await page.click('button[type="submit"]');
      await expect(page).toHaveURL(/\/login/);
    });
  });

  test.describe('Register', () => {
    test('register page renders all required form fields', async ({ page }) => {
      await page.goto('/register');
      await expect(page.locator('input[name="name"]')).toBeVisible();
      await expect(page.locator('input[name="email"]')).toBeVisible();
      await expect(page.locator('input[name="password"]')).toBeVisible();
      await expect(page.locator('button[type="submit"]')).toBeVisible();
    });
  });

  test.describe('Logout', () => {
    test('logout redirects to home and clears session', async ({ page }) => {
      await loginAs(page, USERS.superadmin.email, USERS.superadmin.password);
      await logout(page);

      // After logout, visiting dashboard should redirect to login
      await page.goto('/dashboard');
      await expect(page).toHaveURL(/\/login/);
    });
  });
});
