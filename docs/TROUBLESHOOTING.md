# Troubleshooting

[Back to README](../README.md) · [User guide](USER-GUIDE.md)

This guide covers expected issues and issues encountered while preparing the starter. It does not imply that every issue currently exists or cover every possible failure.

## Start here

Run commands from the project directory containing `artisan`. Capture the complete error before making changes:

```powershell
php --version
composer --version
node --version
npm --version
```

For Laravel errors, inspect `storage/logs/laravel.log` if the configured logging channel writes there. Do not include `.env`, passwords, or service keys in issue reports. Do not use `migrate:fresh`, database deletion, or an `APP_KEY` change as general fixes: they can destroy data or make encrypted data unreadable.

## Installation and dependencies

### Composer rejects the PHP version

The current lockfile includes Symfony and PHPUnit dependencies requiring PHP 8.4.1 or later. The root `^8.3` constraint alone is insufficient. Check `php --version` in the terminal running Composer. On Windows, PHP on PATH may differ from the version selected for the site in Herd. Use a compatible version and run `composer install`; do not bypass platform requirements to hide the error.

### `could not find driver` or a missing PHP extension

Run `php --ini` to locate the active configuration and `php -m` to list extensions. SQLite requires `pdo_sqlite`; MySQL requires `pdo_mysql`. Restart persistent PHP services after configuration changes. Run `composer check-platform-reqs` to check installed package requirements.

### `Could not resolve host` or failed Composer/npm downloads

A connection failure occurred during setup in a restricted execution environment. Check network access, DNS, proxy configuration, and execution permissions, then retry the same installation command. A network failure does not call for changing package versions or disabling TLS.

### `Permission denied` when writing to `vendor` or `node_modules`

Check that the process can write to the project directory. Another process or security tool may have locked a file. A sandboxed tool may require installation access. This error alone does not indicate a defect in the starter code.

### `Cannot open bootstrap script ... vendor/autoload.php`

Confirm that you are in the correct project directory, `composer install` completed, and the file exists and is readable. If the file already exists, inspect process permissions and execution restrictions before reinstalling dependencies.

### npm reports an unsupported Node version

The locked Vite Plus version accepts `^20.19.0`, `^22.18.0`, or `>=24.11.0`. Use a compatible version and run `npm ci`. Avoid `npm update` during initial setup so installation uses the locked versions.

### `npm ci` reports a lockfile mismatch

Check whether `package.json` changed without updating `package-lock.json`. A clean copy should use the matching files from the template. If dependencies changed intentionally, regenerate the lockfile through installation, review the differences, and test them. Do not automatically delete the lockfile.

## Database and configuration

### `No application encryption key has been specified`

For a new project, copy `.env.example` to `.env` and run `php artisan key:generate`. For an existing application, restore its correct key from the deployment configuration instead of generating another. Run `php artisan config:clear` if stale configuration is cached.

### MySQL reports `Access denied ... root` when you intended to use SQLite

This occurred during preview setup when `DB_CONNECTION=mysql` remained configured. Set both the driver and database path:

```dotenv
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Create the SQLite file if missing, run `php artisan config:clear`, then `php artisan migrate`. If MySQL is intended, configure its database name and credentials. Do not use an SQLite path as `DB_DATABASE` with the MySQL driver.

### The SQLite file is missing

Check that `database/database.sqlite` exists. For a new project, create it with `New-Item database/database.sqlite -ItemType File`. If the server runs from a different working directory, set a valid absolute path in `DB_DATABASE`. Do not replace an existing database with an empty file.

### `no such table: project_settings`, `sessions`, or `cache`

Run the starter migrations before opening the application. The default configuration uses the database for sessions and cache. Inspect `php artisan migrate:status`, then run `php artisan migrate` against the intended project database. Do not copy the Act-tracker database as a substitute for running the starter migrations.

### Changes to `.env` are not reflected

Run `php artisan config:clear`. Changing the notifications flag may also require `php artisan route:clear`, because route registration depends on that setting. Restart long-running processes when applicable. Changes to `VITE_*` require restarting Vite or rebuilding assets.

## Authentication and permissions

### The seeder requires `STARTER_ADMIN_EMAIL` and `STARTER_ADMIN_PASSWORD`

Set both values in `.env`, clear cached configuration if necessary, then rerun `php artisan db:seed`. There is no shared default account. Review `syncPermissions` before reseeding: it may overwrite role permissions changed through the interface.

### Changing the administrator password in `.env` does not change login credentials

This is expected. The seeder creates a missing account but does not reset an existing password. Use user management or password recovery. Changing the administrator email and reseeding may conflict with the existing `admin` username; reseeding is not an account-renaming workflow.

### A page returns 403 or a navigation link is missing

Check the user's role and permission for the route. For example, **Project colors** requires `project.manage`, which is not included in the initial `admin` permissions. User ID 1 does not automatically grant system privileges; those come from `super-admin`.

If permissions were modified outside the application's interface and cached values remain, run `php artisan permission:cache-reset` and reload. Granting a permission does not create a missing route.

### Editing or deleting `super-admin` returns 403, or demoting the last administrator returns 422

These are intentional protections. The protected role cannot be edited or deleted, and the application must retain an active super administrator. Ordinary administrators cannot edit a user holding that role or grant it to another user.

### An email or username remains taken after deleting an account

Normal deletion is a soft delete that retains the account and its unique values. Restore the intended account or choose different identifiers. Permanent deletion is a separate decision, not a required troubleshooting step.

### `/register` returns 404

Public registration is disabled, and `auth/Register.vue` is not included. Enabling the Fortify option alone does not provide a complete registration flow. Add the page, review account creation rules and default roles, and test the workflow.

### The security page asks for password confirmation

This is expected behavior from password confirmation middleware. Most administrative pages also require an authenticated user with a verified email address.

### `419 Page Expired`

Refresh the form to obtain a matching session and CSRF token. Use a consistent hostname instead of alternating between `localhost` and `127.0.0.1`. Check cookies, sessions, and the `sessions` table required by the default configuration. Review session domain and HTTPS settings when deploying.

### Password reset email does not arrive

The default `MAIL_MAILER=log` writes messages to the log. Configure a mail provider, credentials, and `APP_URL` before expecting delivery. The starter notification flag and Fortify authentication mail are independent.

## Interface, colors, and languages

### A blank page or `Vite manifest not found`

Run `npm ci` followed by `npm run build`, or run `npm run dev` alongside Laravel during development. Inspect the browser console and failed asset requests if the issue persists. `public/hot` points to the development server. If Vite stopped but the file remains, verify its contents before removing the stale file and using built assets.

### TypeScript cannot find `@/routes` or `@/actions`

Wayfinder generates these files. Run:

```powershell
php artisan wayfinder:generate --with-form
npm run types:check
```

The build also generates them. Do not copy generated files from a previous project with different routes.

### The old color remains after changing a default

Saved values in `project_settings` override defaults. Open **Project colors**, reset the form to its defaults, then save. Reload pages open under other accounts; windows do not synchronize in real time.

### Changing a light color does not change dark mode

Each mode has a separate palette. Edit the intended palette and check the account's appearance setting. System mode follows the operating system preference.

### A color is rejected or text is hard to read

Fields accept `#RRGGBB`, such as `#0d5236`. Named colors and short hex values are unsupported. Choose foreground colors that remain readable against their backgrounds; automatic contrast calculation is not included.

### Changing `STARTER_LAYOUT` or `STARTER_LOCALE` does not affect an existing account

Saved account preferences override defaults. Change them under **Settings > Appearance** or through the language button. A guest's saved session language can also override the default.

### Some text remains English when Arabic is selected

Add the exact English key to `resources/js/locales/ar.ts` for Vue text or `lang/ar.json` for PHP messages. Use `$t()` or `__()` instead of hardcoded text. User names, role names, entered data, and permission keys are not automatically translated.

### Cairo does not load without internet access

The font is loaded from Google Fonts. If loading fails, the browser uses a fallback. Host the font files locally when the application must work without access to that service. Inspect font requests in the browser's Network panel.

### Notifications are hidden or `/notifications` returns 404

The module is intentionally disabled by default. When needed, follow the activation steps in the user guide and rebuild caches. Enabling the module does not create notifications; your application must invoke `SystemNotification` at the appropriate event.

### Notifications do not appear immediately or arrive by email or push

The module stores database notifications and displays them when the page is requested. Email, push, WebSockets, and polling are not included. Add the required channels and connect them to project events when needed.

## Export and validation

### Export rejects the destination

The destination must be a new directory outside the starter directory. Choose a new name or extract the ZIP manually. The script does not merge files into existing projects.

### PowerShell blocks the export script

Extract the ZIP instead. On an organization-managed device, follow its script execution policy. Disabling device protection is not required to use the starter.

### Formatting checks fail

`npm run check` does not modify files. Use `npm run check:fix` for formatting and fixable lint errors, then review the differences. Use `composer lint` for PHP. Passing formatting checks does not replace tests or type analysis.

### Reporting an issue not covered here

Include the PHP and Node versions, operating system, reproduction steps, affected page or command, expected and actual behavior, and a sanitized error excerpt. State whether the issue occurs in a clean copy or after customization. Once diagnosed, add a regression test and document the verified solution.

## Validation scope and limitations

During preparation, 45 tests passed and 2 were skipped. PHPStan, TypeScript, lint, and the build passed. Checks covered account preferences, colors, selected authorization boundaries, disabled notifications, and notification ownership isolation. This was not a comprehensive security audit, load test, or compatibility certification across operating systems, database engines, and hosting providers. SSR, actual email delivery, and production deployment were not verified as part of this delivery.
