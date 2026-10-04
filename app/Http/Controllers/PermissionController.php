<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(): Response
    {
        $permissions = Permission::withCount('roles')
            ->orderBy('name')
            ->get();

        return Inertia::render('permissions/Index', [
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(\.[a-z0-9\-]+)+$/',
                'unique:permissions,name',
            ],
        ]);

        Permission::findOrCreate($data['name'], 'web');

        return back()->with('success', __('Permission created successfully.'));
    }
}
