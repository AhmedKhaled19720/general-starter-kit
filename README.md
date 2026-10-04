# General Laravel + Vue Starter Kit

An independent starter extracted from Act-tracker, preserving the green palette and **Cairo** font, without cases, customers, teams, or reports.

## Documentation

- [User guide](docs/USER-GUIDE.md): project setup, accounts, permissions, colors, navigation, languages, and notifications.
- [Troubleshooting](docs/TROUBLESHOOTING.md): installation, runtime, authentication, and build issues, plus current limitations.

This is a standalone starter application. It is not currently a published library installed with `composer require`. Updates to this template do not automatically update projects created from it.

## Requirements

These requirements reflect the included lockfiles, not just the Laravel version:

- PHP **8.4.1 or later within compatible PHP 8 releases**. Although `composer.json` specifies `^8.3`, some locked dependencies and testing tools require 8.4.1.
- Composer and the required PHP extensions. Run `composer check-platform-reqs` after installation.
- Node.js compatible with the included Vite and Vite Plus: `^20.19.0`, `^22.18.0`, or `>=24.11.0`, plus npm.
- SQLite and `pdo_sqlite` for the default configuration and tests. Projects can configure MySQL with `pdo_mysql`.
- PowerShell for the export script, or extract the ZIP manually on a supported system.

Run commands from the starter or new project directory containing `artisan` and `composer.json`.

## Features

- Laravel 13, Vue 3, TypeScript, Inertia, and Tailwind 4.
- Login with email or username, password recovery, email verification, and two-factor authentication. Public registration is disabled by default.
- Users, avatars, soft deletion and restoration, roles, permissions, and an activity log.
- Navbar or sidebar selection under **Settings > Appearance**, saved per user in the database.
- Arabic and English, a language switch, and RTL/LTR support. Authenticated preferences are stored per account; guest language is stored in the session.
- Global project colors protected by `project.manage`, with separate light and dark palettes, a preview, and a reset option.
- An in-app notification module that is **fully disabled by default**, with no automatic sending, polling, or broadcasting.

## Start a new project

Copy the starter into a new project directory without `.env`, `vendor`, `node_modules`, `public/build`, or the database. Use the export script below for a clean copy.

```powershell
composer install
npm ci
Copy-Item .env.example .env
php artisan key:generate
```

Set `APP_NAME`, the database settings, `STARTER_ADMIN_EMAIL`, and `STARTER_ADMIN_PASSWORD` in `.env`. The starter has no default password. Keep `STARTER_NOTIFICATIONS_ENABLED=false`.

For SQLite:

```powershell
New-Item database/database.sqlite -ItemType File
php artisan migrate --seed
npm run build
php artisan serve
```

For frontend development, run `npm run dev` in a second terminal. `php artisan serve` runs the application, while Vite reloads Vue and CSS changes.

Use these manual setup steps for the first installation. The inherited `composer setup` command does not seed the administrator or replace configuring its credentials. Do not use it to repair an existing application: it generates a new application key.

Log in with the configured administrator email or the username `admin`. Privileges come from the `super-admin` role, regardless of user ID. This protected role cannot be edited or deleted. Ordinary administrators cannot grant it or edit an account that holds it.

## Customize the starter

- `config/starter.php`: default colors, locale, layout, and navigation links with their permissions. Both navigation layouts use the same list.
- `resources/css/app.css`: secondary colors, states, borders, and typography. Saved project colors override `primary`, `primary_foreground`, `background`, `foreground`, and `card` in both modes; focus and muted primary backgrounds follow these values.
- `resources/js/locales/ar.ts`: frontend translations. English text is the key; use `$t('Your label')` in Vue and add its translation.
- `lang/ar.json`: Laravel message translations; use `__('Your message')`.
- `routes/web.php`: routes for new project features. Keep domain-specific functionality separate from reusable components.
- `database/seeders/RolePermissionSeeder.php`: project roles and permissions. Keep `super-admin` protected.

Account preferences are separate from project colors. Colors apply to everyone; language, navigation layout, and light/dark mode belong to each account.

## Notifications

The scaffold includes a notifications table, a general notification class, a list page, mark-as-read and mark-all-as-read actions, and a button shown only when enabled. Read operations are scoped to the notification owner.

When needed, set `STARTER_NOTIFICATIONS_ENABLED=true`, then run `php artisan optimize:clear`. Rebuild route and configuration caches if your deployment uses them. No events send notifications automatically; your project decides when to send one:

```php
$user->notify(new \App\Notifications\SystemNotification(
    'Notification title',
    'Notification details',
    '/dashboard',
));
```

This module provides database-backed in-app notifications. Email, push, and WebSockets require separate integration.

## Validation

```powershell
php artisan test
npm run types:check
npm run check
npm run build
composer types:check
```

Tests cover account preference isolation, project color protection, privilege escalation prevention, and disabled and user-scoped notifications. Tests use an in-memory SQLite database. The key in `phpunit.xml` is for testing only.

## Export a clean copy

```powershell
./scripts/export.ps1 -Destination D:/Projects/my-new-app
```

The script creates a new directory and excludes secrets, accounts, databases, and installed dependencies. This is currently a project template; reusable Composer or npm packages can be extracted later once their interfaces have been proven across multiple projects.
