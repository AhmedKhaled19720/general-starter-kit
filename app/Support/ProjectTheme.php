<?php

namespace App\Support;

use App\Models\ProjectSetting;

class ProjectTheme
{
    /** @return array<string, array<string, string>> */
    public function current(): array
    {
        return array_replace_recursive(config('starter.theme'), ProjectSetting::where('key', 'theme')->value('value') ?? []);
    }
}
