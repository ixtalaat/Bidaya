# Bidaya — Kids Education Center LMS

Web-based LMS and center-management system for Bidaya Center (Mit Mohsen, Mit Ghamr, Dakahlia, Egypt).
Laravel 13 · PHP 8.3+ · MySQL/MariaDB · Blade + Tailwind v4 · PHPUnit.

One connected workflow: register students → enroll into groups → schedule sessions →
record attendance → assess → collect monthly fees → track progress — for staff,
teachers, parents, and accountants, in English and Arabic (RTL).

## Requirements

| Tool | Version | Notes |
|---|---|---|
| PHP | 8.3+ | Extensions: `gd`, `zip`, `pdo_mysql`, `mbstring`, `openssl` |
| Composer | 2.x | |
| Node + npm | 20+ | Frontend build |
| MySQL / MariaDB | 8 / 10.4+ | Two databases: app + testing |
| mysqldump | — | Ships with XAMPP/MySQL; set `MYSQLDUMP_PATH` if not on PATH |

On XAMPP (Windows): enable `extension=gd` and `extension=zip` in `php.ini`,
then start MySQL (tick **Svc** in the control panel so it survives reboots).

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Create the databases, then configure `.env`:

```sql
CREATE DATABASE bidaya CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE bidaya_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```ini
DB_DATABASE=bidaya
ADMIN_EMAIL=admin@bidaya.test
ADMIN_PASSWORD=password
CONTACT_PHONE=+201121451378
CONTACT_EMAIL=elsaftymariam555@gmail.com
CONTACT_WHATSAPP=201121451378
```

```bash
php artisan migrate --seed   # demo center: users, collection, groups, enrollment
npm run dev                  # or: npm run build
php artisan serve
```

Demo logins (password: `password`): `admin@bidaya.test`, `teacher@bidaya.test`,
`parent@bidaya.test`, `manager@bidaya.test`, `accountant@bidaya.test`.

## Testing

Tests run against MySQL (`bidaya_testing` via `.env.testing` — same queries as production):

```bash
php artisan test --compact          # full suite
php artisan test --filter=PaymentsTest
vendor/bin/pint --dirty --format agent   # format before committing
```

## Daily operations

```bash
php artisan schedule:run        # cron every minute (session reminders 07:00, DB backup 02:00)
php artisan app:backup-database # manual DB backup → storage/app/backups (keeps 7)
php artisan app:send-session-reminders --date=2026-10-05
```

## Deployment (Railway)

Repo prep is done: `railway/` scripts, DB sessions/cache/queue, public-disk uploads.

1. Push `main` to GitHub, create a Railway project from the repo.
2. Services: **app** (`npm run build`, pre-deploy `sh ./railway/init-app.sh`),
   **MySQL plugin**, **cron** (`sh ./railway/run-cron.sh`), **worker**
   (`sh ./railway/run-worker.sh`), volume on `/app/storage/app`.
3. Set variables: `APP_KEY` (fresh `php artisan key:generate --show`),
   `DB_*` from `${{MySQL.*}}`, `QUEUE_CONNECTION=database`,
   `SESSION_DRIVER=database`, `SESSION_SECURE_COOKIE=true`,
   `LOG_CHANNEL=stderr`, Gmail SMTP + `WHATSAPP_*` as needed.
4. Create the first admin via Railway shell (production starts empty).

## Conventions

- Conventional Commits with scopes (`feat(enrollment): …`). Template: `.gitmessage`.
- Thin controllers → Form Requests → Actions/Services → Models. Business rules
  live in actions, enforced transactionally, covered by feature tests.
- Controllers coordinate HTTP only (input, authorization, response) — no domain
  decisions, calculations, or state-transition logic inside them.
- `app/Enums` for all statuses; bilingual `lang/en` + `lang/ar` (RTL layout).
- Run the full suite + Pint before every commit.
