/**
 * Shared authentication helpers for Playwright E2E tests.
 * Credentials fall back to the seeded defaults if env vars are not set.
 */

export const USERS = {
  superadmin: {
    email: process.env.E2E_SUPERADMIN_EMAIL ?? 'superadmin@optimise.test',
    password: process.env.E2E_SUPERADMIN_PASSWORD ?? 'password',
  },
  emt: {
    email: process.env.E2E_EMT_EMAIL ?? 'emt@optimise.test',
    password: process.env.E2E_EMT_PASSWORD ?? 'password',
  },
  externalRem: {
    email: process.env.E2E_EXTERNAL_REM_EMAIL ?? 'external.rem@optimise.test',
    password: process.env.E2E_EXTERNAL_REM_PASSWORD ?? 'password',
  },
};

/**
 * Navigate to /login, fill credentials, submit, and wait for redirect to /dashboard.
 *
 * @param {import('@playwright/test').Page} page
 * @param {string} email
 * @param {string} password
 */
export async function loginAs(page, email, password) {
  await page.goto('/login');
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', password);
  await page.click('button[type="submit"]');
  await page.waitForURL('**/dashboard');
}

/**
 * Log out by navigating to the GET /logout route.
 *
 * @param {import('@playwright/test').Page} page
 */
export async function logout(page) {
  await page.goto('/logout');
}
