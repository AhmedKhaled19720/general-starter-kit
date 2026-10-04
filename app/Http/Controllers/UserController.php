<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\AvatarLibrary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $trash = $request->boolean('trash');

        $users = User::with(['roles:id,name'])
            ->when($trash, fn ($query) => $query->onlyTrashed())
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'avatar' => $user->avatar,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
                'is_super_admin' => $user->hasRole('super-admin'),
                'created_at' => $user->created_at,
                'deleted_at' => $user->deleted_at,
            ]);

        return Inertia::render('users/Index', [
            'users' => $users,
            'trash' => $trash,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('users/Create', [
            'roles' => Role::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9._-]+$/', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'avatar' => ['nullable', 'string', 'max:64', Rule::in(AvatarLibrary::options())],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'next' => ['nullable', 'in:continue'],
        ]);

        $continue = ($data['next'] ?? null) === 'continue';
        unset($data['next']);

        abort_if($data['role'] === 'super-admin' && ! $request->user()->hasRole('super-admin'), 403);

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'avatar' => $data['avatar'] ?? null,
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

        abort_if($data['role'] === 'super-admin' && ! $request->user()->hasRole('super-admin'), 403);
        $user->syncRoles($data['role']);

        if ($continue) {
            return redirect()->route('users.create')->with('success', __('User created successfully.'));
        }

        return redirect()->route('users.index')->with('success', __('User created successfully.'));
    }

    public function edit(User $user): Response
    {
        return Inertia::render('users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'avatar' => $user->avatar,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
            ],
            'roles' => Role::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9._-]+$/', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'avatar' => ['nullable', 'string', 'max:64', Rule::in(AvatarLibrary::options())],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'next' => ['nullable', 'in:continue'],
        ]);

        $continue = ($data['next'] ?? null) === 'continue';
        unset($data['next']);

        abort_if(($user->hasRole('super-admin') || $data['role'] === 'super-admin') && ! $request->user()->hasRole('super-admin'), 403);
        abort_if($user->hasRole('super-admin') && $data['role'] !== 'super-admin' && User::role('super-admin')->count() <= 1, 422);
        $user->name = $data['name'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->avatar = $data['avatar'] ?? null;

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();
        $user->syncRoles($data['role']);

        if ($continue) {
            return redirect()->route('users.edit', $user)->with('success', __('User updated successfully.'));
        }

        return redirect()->route('users.index')->with('success', __('User updated successfully.'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->hasRole('super-admin')) {
            return back()->with('error', __('The super admin cannot be deleted.'));
        }

        if ($user->id === $request->user()->id) {
            return back()->with('error', __('You cannot delete your own account.'));
        }

        $user->delete();

        return redirect()->route('users.index', ['trash' => 1])->with('success', __('User deleted.'));
    }

    public function restore(Request $request, User $user): RedirectResponse
    {
        $user->restore();

        return redirect()->route('users.index', ['trash' => 1])->with('success', __('User restored.'));
    }

    public function forceDestroy(Request $request, User $user): RedirectResponse
    {
        if (! $request->user()->hasRole('super-admin')) {
            return back()->with('error', __('Only the super admin can permanently delete users.'));
        }

        if ($user->hasRole('super-admin')) {
            return back()->with('error', __('The super admin cannot be deleted.'));
        }

        $user->forceDelete();

        return redirect()->route('users.index', ['trash' => 1])->with('success', __('User permanently deleted.'));
    }
}
