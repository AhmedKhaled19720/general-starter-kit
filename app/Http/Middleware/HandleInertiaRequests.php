<?php

namespace App\Http\Middleware;

use App\Support\ProjectTheme;
use App\Support\UserPreferences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'preferences' => app(UserPreferences::class)->for($user),
            'theme' => app(ProjectTheme::class)->current(),
            'notifications' => ['enabled' => config('starter.notifications.enabled')],
            'navigation' => collect(Config::array('starter.navigation'))->filter(fn ($item) => ! isset($item['permission']) || $user?->can($item['permission']))->values(),
            'auth' => [
                'user' => $user,
                'permissions' => $user ? $user->getAllPermissions()->pluck('name')->values() : [],
                'isSuperAdmin' => $user?->hasRole('super-admin') ?? false,
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
