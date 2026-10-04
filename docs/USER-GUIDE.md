# User Guide

[Back to README](../README.md) · [Troubleshooting](TROUBLESHOOTING.md)

## 1. What you get

An independent Laravel and Vue application for starting new projects. It includes authentication, users, roles and permissions, appearance preferences, Arabic and English, an activity log, and an in-app notification scaffold disabled by default.

Each project has its own database and environment settings. Updating the template does not automatically update existing projects. Keep reusable changes in the template, then review their differences and tests before applying them to an existing application.

## 2. Create a new copy

From the starter directory, export to a directory that does not exist:

```powershell
./scripts/export.ps1 -Destination D:/Projects/my-new-app
```

Alternatively, extract the ZIP into a new directory. A clean copy excludes `.env`, the database, installed dependencies, and preview accounts. Follow the [installation steps](../README.md#start-a-new-project).

Do not export inside the starter directory. The script rejects existing destinations to avoid overwriting another project. `.env.example` is a configuration template; create an actual `.env` file from it.

## 3. Project configuration

| Variable in `.env` | Purpose |
| --- | --- |
| `APP_NAME` | Application name displayed in the interface |
| `APP_URL` | Actual application URL, also used for email links |
| `APP_KEY` | Application key generated once for a new application |
| `DB_CONNECTION` | `sqlite` by default, or your configured database engine |
| `DB_DATABASE` | SQLite file path or database name, depending on the engine |
| `STARTER_ADMIN_EMAIL` | Administrator email used during initial seeding |
| `STARTER_ADMIN_PASSWORD` | Administrator password used when first creating the account |
| `STARTER_LOCALE` | Default language: `ar` or `en` |
| `STARTER_LAYOUT` | Default layout: `navbar` or `sidebar` |
| `STARTER_NOTIFICATIONS_ENABLED` | Defaults to `false`; keep disabled until needed |

Run `php artisan config:clear` after changing Laravel configuration. Restart Vite or run `npm run build` after changing the application name or `VITE_*` settings.

The default `MAIL_MAILER=log` writes password reset and verification messages to application logs instead of delivering real email. Configure a mail provider and its credentials when delivery is needed.

## 4. Users and roles

After configuring the administrator and running `php artisan migrate --seed`, log in with the chosen email or the username `admin`.

| Role | Initial behavior |
| --- | --- |
| `super-admin` | Bypasses permission and policy checks; privileges do not depend on user ID |
| `admin` | Manages users and roles and reads the activity log |
| `user` | Regular user without default administrative permissions |

The users page supports creating and editing accounts, selecting roles and avatars, soft deletion, and restoration. Permanent deletion is available to the super administrator. Soft-deleted accounts retain their unique email addresses and usernames so they can be restored without conflicts.

The `super-admin` role cannot be edited or deleted. Ordinary administrators cannot create or edit super administrator accounts. The last active super administrator cannot lose that role. Users cannot delete their own accounts through the user management action.

Public registration is disabled. Administrators create accounts through user management, which marks the new email address as verified. If your project requires invitations or email ownership verification before access, implement and test that workflow.

The seeder uses `firstOrCreate`, so rerunning it does not reset an existing administrator password. Changing the password in `.env` does not update an existing account. Use user management or password recovery after configuring mail delivery.

## 5. Colors and typography

Open **Project colors** with `project.manage` permission or the `super-admin` role.

Each light and dark palette has five values:

- Primary button and link color.
- Text color on the primary background.
- Page background.
- Foreground text color.
- Card background.

Enter a `#RRGGBB` value or use the color picker, then save. Saved colors apply to all users on the next page load or page data response. Open windows do not receive live WebSocket updates.

The reset button loads the defaults from `config/starter.php` into the form. Save the form to persist them. The inline preview displays the light palette; check the dark palette by selecting dark mode in account settings after saving.

Saved values in `project_settings` override defaults in `config/starter.php`. Editing a default will therefore not replace an existing saved customization.

The **Cairo** font is configured in CSS and loaded from Google Fonts in `resources/views/app.blade.php`. For offline use, host appropriately licensed Cairo files locally and replace the external font link with `@font-face` rules.

The color picker does not calculate contrast automatically. Check text and button readability in both modes. Error, warning, and border colors can be changed in `resources/css/app.css`; not every CSS color has a field on the settings page.

## 6. Navigation and appearance

Under **Settings > Appearance**, each user can select:

- Navbar or sidebar.
- Light, dark, or system appearance.
- Arabic or English.

Preferences are stored in `users.preferences` and follow the account across devices. Changing a default does not overwrite a saved preference. Both navigation layouts use the same permission-filtered links from `config/starter.php`.

Small screens display a navigation menu button instead of a full sidebar.

## 7. Languages and new text

The language button switches between Arabic and English and updates the page direction. Guest choices are stored in the session; authenticated choices are stored on the account. User names and project data are not translated automatically.

For frontend text:

```vue
<span>{{ $t('Orders') }}</span>
```

Add the `Orders` key and its Arabic translation to `resources/js/locales/ar.ts`.

For PHP messages, use `__('Order saved.')` and add the translation to `lang/ar.json`. Arabic validation messages are in `lang/ar/validation.php`; extend them for new fields as needed.

English text is the fallback when a translation is missing. Frontend and server messages use separate catalogs. Update the appropriate file, or both if the same text appears in both contexts.

## 8. Add a page or module

1. Add the model, migration, and project-specific logic as needed.
2. Add a Vue page under `resources/js/pages` and render it from Laravel through Inertia.
3. Add a route with the appropriate server-side authorization in `routes/web.php`.
4. Add permissions to `RolePermissionSeeder.php` and assign them to the intended roles. Rerunning `syncPermissions` replaces the role's permissions with the specified list.
5. Add a `navigation` entry in `config/starter.php` with `label`, `href`, `icon`, and `permission`, then translate the label.
6. Available icons are mapped in `StarterNavigation.vue`. Add a Lucide icon to that map when needed. Unknown keys fall back to the dashboard icon.
7. After route changes, rebuild the frontend or generate Wayfinder files before checking TypeScript:

```powershell
php artisan wayfinder:generate --with-form
npm run types:check
npm run build
```

Hiding a Vue link or button does not authorize the corresponding Laravel route. Test both permitted and forbidden access.

## 9. Notifications

The scaffold includes a database table, general notification class, list page, and read actions. While `STARTER_NOTIFICATIONS_ENABLED=false`, the button is hidden, routes are not registered, and `SystemNotification` does not send to the database channel.

When needed, set the flag to `true`, run `php artisan optimize:clear`, and rebuild caches if used. Connect sending to a project event using the [README example](../README.md#notifications).

This module has no automatic sending events, email, push, WebSockets, or polling. Authentication mail, including password resets, is independent and follows the mail configuration.

## 10. Validation and maintenance

Run the validation commands in the README after changing application logic or the frontend. During preparation on October 4, 2026, **45 tests passed, 2 were skipped, and 176 assertions completed**. PHPStan, TypeScript, lint, and the production build also passed. These results describe that version, not future modifications.

Manual desktop and mobile checks covered color persistence, language switching, and navigation layouts. Production deployment, load testing, and exhaustive hosting compatibility checks were not performed.

For deployment, configure the `public` web root, database, write access to `storage` and `bootstrap/cache`, environment and mail settings, asset build, and appropriate migrations. Set `APP_DEBUG=false` in production. Never use the test key from `phpunit.xml` as the application key or regenerate an existing application's key as a general repair step.
