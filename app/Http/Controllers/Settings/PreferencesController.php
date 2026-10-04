<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PreferencesController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate(['layout' => ['sometimes', 'required', 'in:navbar,sidebar'], 'locale' => ['sometimes', 'required', 'in:ar,en'], 'appearance' => ['sometimes', 'required', 'in:light,dark,system']]);
        $user = $request->user();
        $user->forceFill(['preferences' => array_replace($user->preferences ?? [], $data)])->save();

        return back();
    }
}
