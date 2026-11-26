# CLAUDE.md

Rules for AI coding assistants working in this repository.

## Project

Energy Management System (EnMS) for an ISO 50001 energy consultancy. It covers the PLAN phase:
energy data management, energy review (SEC / EIP analysis), Significant Energy Use (SEU)
flagging, and regression-based EnPI baselines.

- **Stack:** Laravel 10, PHP 8.3, Blade + Vite, Playwright for E2E
- **Databases:** PostgreSQL in production (Render/Railway), MySQL 8 in CI, either locally
- **Layout:** controllers in `app/Http/Controllers/Admin/`, analytics in `app/Services/`,
  views in `resources/views/admin/<module>/`, routes in `routes/web.php`

## Commands

```bash
php artisan migrate --seed       # schema + reference data + one demo user per role
php artisan serve
vendor/bin/phpunit               # unit / feature tests
npm run test:e2e                 # Playwright (needs the app running on :8000)
```

## Hard rules

1. **SQL must run on both PostgreSQL and MySQL.** No `DROP INDEX IF EXISTS`, no
   `true`/`1` boolean mixing, no engine-specific functions in migrations or raw queries.
   Prefer the schema builder and query builder. CI runs on MySQL; production runs on PostgreSQL.
   Both kinds of bug have reached CI or a deploy before.
2. **Every new route sits behind authorisation.** Use `permission:<module>,<action>` middleware
   (see `CheckPermission`) or the `superadmin` group. Never rely on hiding a button in Blade.
3. **Validate numeric input; never coerce it.** Do not use `floatval()` / `(int)` on request
   data. That silently turns bad input into `0`. Use Laravel validation (`numeric`, `min:0`)
   and show errors through the `$errors` bag.
4. **No secrets or real credentials in code.** Demo account passwords come from
   `SEED_USER_PASSWORD` (see `database/seeders/Support/SeedPassword.php`). Seeders must not
   create known-password accounts in production.
5. **Calculation changes need a check against known figures.** SEC, EIP and regression output
   (`BaselineModelController`, `EipFilterService`) must be verified against a worked example
   before merge.
6. **Keep changes small and scoped.** One concern per PR. Don't reformat untouched code or add
   libraries without asking. The regression and statistics code is plain PHP on purpose.

## Workflow

- Branch from `main`. Open a PR. CI (Playwright E2E on MySQL) must be green before merge.
- Explain any change you can't justify line by line instead of committing it.
- Commit messages: `<type>: <description>` (feat, fix, refactor, docs, test, chore, ci).
