# SK ProgramHub

SK ProgramHub is a Laravel 13 application for local SK councils and youth participants. It includes a public community site, council workspaces, secure account flows, program and event management, announcements, and participant registration.

## Requirements

- PHP 8.3+
- Composer
- Node.js and npm
- MySQL/MariaDB or SQLite

Make sure the PHP CLI used by Composer has the `zip` extension enabled. On Laragon, enable `extension=zip` in that PHP version's `php.ini`, then open a new terminal.

## Local setup

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
npm install
php artisan storage:link
php artisan migrate
npm run build
```

Configure `.env` before running in production. The example uses SQLite, the `Asia/Manila` timezone, and Laravel's log mailer. To send verification and password-reset emails, configure a production mail transport. To use MySQL/MariaDB, set `DB_CONNECTION=mysql` and configure `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`.

For local development, run these in separate terminals:

```powershell
php artisan serve
npm run dev
```

## Accounts and authorization

- Public visitors can browse published public programs, events, and announcements and submit program registrations.
- Participant accounts can view and cancel their own pending registrations.
- SK council signups create a workspace in `pending` state. Email verification alone does not grant council access; an authorized reviewer must separately set the account and workspace to `active`.
- Program, event, announcement, and registration management routes require a verified account, an active workspace, and ownership of the record's workspace.
- The database enforces a unique workspace per province, municipality/city, and barangay combination; barangay names alone are not treated as globally unique.

No platform-admin approval UI is provisioned in this initial app. Until an administrative workflow is configured, review and suspension are operational tasks performed by a trusted administrator through an approved process. Never activate an account based only on self-reported council details.

## Data ownership and Drupal

Laravel owns SK workspaces, operational programs, event schedules, public announcements, and participant registrations in this application. Drupal remains a separate CMS; Laravel does not connect to Drupal's database or assume Drupal logins authenticate Laravel users. No Drupal endpoint or token was supplied, so the app does not make Drupal API calls. Configure and document an authenticated, tenant-safe Drupal API contract before assigning Drupal ownership of content or enabling synchronization; keep any API credentials server-side in environment variables.

Email delivery also needs a configured mail provider. There is no billing or automatic subscription activation.

## Checks

```powershell
php artisan test
vendor/bin/pint --test
npm run build
```
