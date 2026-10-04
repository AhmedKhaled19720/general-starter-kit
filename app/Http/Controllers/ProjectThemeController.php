<?php

namespace App\Http\Controllers;

use App\Models\ProjectSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectThemeController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('project/Theme', ['defaults' => config('starter.theme')]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = ['theme' => ['required', 'array:light,dark']];
        foreach (['light', 'dark'] as $mode) {
            $rules["theme.$mode"] = ['required', 'array:primary,primary_foreground,background,foreground,card'];
            foreach (array_keys(config("starter.theme.$mode")) as $key) {
                $rules["theme.$mode.$key"] = ['required', 'regex:/^#[0-9a-fA-F]{6}$/'];
            }
        }
        $data = $request->validate($rules);
        ProjectSetting::updateOrCreate(['key' => 'theme'], ['value' => $data['theme']]);

        return back()->with('success', __('Project colors saved.'));
    }
}
