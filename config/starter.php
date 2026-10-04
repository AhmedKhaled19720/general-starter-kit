<?php

return [
    'locale' => env('STARTER_LOCALE', 'ar'),
    'layout' => env('STARTER_LAYOUT', 'navbar'),
    'theme' => [
        'light' => ['primary' => '#0d5236', 'primary_foreground' => '#ffffff', 'background' => '#f8fafc', 'foreground' => '#111827', 'card' => '#ffffff'],
        'dark' => ['primary' => '#10b981', 'primary_foreground' => '#04241a', 'background' => '#0a0a0a', 'foreground' => '#fafafa', 'card' => '#121212'],
    ],
    'navigation' => [
        ['label' => 'Dashboard', 'href' => '/dashboard', 'icon' => 'dashboard'],
        ['label' => 'Users', 'href' => '/users', 'icon' => 'users', 'permission' => 'users.manage'],
        ['label' => 'Roles', 'href' => '/roles', 'icon' => 'roles', 'permission' => 'roles.manage'],
        ['label' => 'Permissions', 'href' => '/permissions', 'icon' => 'permissions', 'permission' => 'roles.manage'],
        ['label' => 'Activity log', 'href' => '/activity-log', 'icon' => 'activity', 'permission' => 'activity.view'],
        ['label' => 'Project colors', 'href' => '/project/theme', 'icon' => 'theme', 'permission' => 'project.manage'],
    ],
    'notifications' => ['enabled' => (bool) env('STARTER_NOTIFICATIONS_ENABLED', false)],
    'admin' => ['email' => env('STARTER_ADMIN_EMAIL'), 'password' => env('STARTER_ADMIN_PASSWORD')],
];
