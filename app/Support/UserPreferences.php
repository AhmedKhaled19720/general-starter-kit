<?php

namespace App\Support;

use App\Models\User;

class UserPreferences
{
    /** @return array{layout: string, locale: string, appearance: string} */
    public function for(?User $user): array
    {
        $stored = $user === null ? [] : ($user->preferences ?? []);

        return array_replace(['layout' => config('starter.layout', 'navbar'), 'locale' => session('locale', config('starter.locale', 'ar')), 'appearance' => 'system'], $stored);
    }
}
