<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): Response
    {
        $roles = Role::withCount('users')
            ->with('permissions:id,name')
            ->orderBy('name')
            ->get();

        return Inertia::render('roles/Index', [
            'roles' => $roles,
            'permissionCount' => Permission::count(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('roles/Create', [
            'permissions' => $this->groupedPermissions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
            'next' => ['nullable', 'in:continue'],
        ]);

        $continue = ($data['next'] ?? null) === 'continue';
        unset($data['next']);

        abort_if($data['name'] === 'super-admin', 422);
        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($data['permissions'] ?? []);

        if ($continue) {
            return redirect()->route('roles.create')->with('success', __('Role created successfully.'));
        }

        return redirect()->route('roles.index')->with('success', __('Role created successfully.'));
    }

    public function edit(Role $role): Response
    {
        return Inertia::render('roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name'),
            ],
            'permissions' => $this->groupedPermissions(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_if($role->name === 'super-admin', 403);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
            'next' => ['nullable', 'in:continue'],
        ]);

        $continue = ($data['next'] ?? null) === 'continue';
        unset($data['next']);

        abort_if($data['name'] === 'super-admin', 422);
        $role->name = $data['name'];
        $role->save();
        $role->syncPermissions($data['permissions'] ?? []);

        if ($continue) {
            return redirect()->route('roles.edit', $role)->with('success', __('Role updated successfully.'));
        }

        return redirect()->route('roles.index')->with('success', __('Role updated successfully.'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_if($role->name === 'super-admin', 403);
        $role->users()->detach();
        $role->delete();

        return redirect()->route('roles.index')->with('success', __('Role deleted.'));
    }

    /**
     * Permissions grouped by their prefix (users, roles, ...) for the UI.
     *
     * @return array<string, list<string>>
     */
    private function groupedPermissions(): array
    {
        $grouped = [];

        foreach (Permission::orderBy('name')->get(['name']) as $permission) {
            $grouped[explode('.', $permission->name)[0]][] = $permission->name;
        }

        return $grouped;
    }
}
