<?php

namespace App\Http\Middleware;

use App\Support\UserPreferences;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class HandleUserPreferences
{
    public function handle(Request $request, Closure $next): Response
    {
        $preferences = app(UserPreferences::class)->for($request->user());
        app()->setLocale($preferences['locale']);
        View::share('appearance', $preferences['appearance']);

        return $next($request);
    }
}
