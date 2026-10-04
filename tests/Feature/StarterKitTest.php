<?php

use App\Http\Controllers\NotificationController;
use App\Models\ProjectSetting;
use App\Models\User;
use App\Notifications\SystemNotification;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutVite();
    $this->seed(RolePermissionSeeder::class);
});

it('saves account preferences independently and shares them on the next request', function () {
    $first = User::factory()->create();
    $second = User::factory()->create();
    $this->actingAs($first)->patch('/settings/preferences', ['layout' => 'sidebar', 'locale' => 'en', 'appearance' => 'dark'])->assertRedirect();
    expect($first->fresh()->preferences)->toBe(['layout' => 'sidebar', 'locale' => 'en', 'appearance' => 'dark']);
    expect($second->fresh()->preferences)->toBeNull();
    $this->get('/dashboard')->assertInertia(fn (Assert $page) => $page->where('preferences.layout', 'sidebar')->where('preferences.locale', 'en'));
    $this->actingAs($second)->get('/dashboard')->assertInertia(fn (Assert $page) => $page->where('preferences.layout', 'navbar')->where('preferences.locale', 'ar'));
});

it('rejects invalid account preferences', function () {
    $this->actingAs(User::factory()->create())->patch('/settings/preferences', ['layout' => 'other', 'locale' => 'xx', 'appearance' => 'other'])->assertSessionHasErrors(['layout', 'locale', 'appearance']);
});

it('restricts project colors and shares saved colors with all users', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->put('/project/theme', ['theme' => config('starter.theme')])->assertForbidden();
    $admin = User::factory()->create()->assignRole('super-admin');
    $theme = config('starter.theme');
    $theme['light']['primary'] = '#123456';
    $this->actingAs($admin)->put('/project/theme', ['theme' => $theme])->assertRedirect();
    expect(ProjectSetting::where('key', 'theme')->value('value')['light']['primary'])->toBe('#123456');
    $this->actingAs($user)->get('/dashboard')->assertInertia(fn (Assert $page) => $page->where('theme.light.primary', '#123456'));
    $theme['light']['primary'] = 'red; background:url(https://example.test)';
    $this->actingAs($admin)->put('/project/theme', ['theme' => $theme])->assertSessionHasErrors('theme.light.primary');
});

it('does not give the first user super admin access', function () {
    $user = User::factory()->create(['id' => 1]);
    $this->actingAs($user)->get('/users')->assertForbidden();
});

it('prevents ordinary admins from assigning or editing super admins', function () {
    $admin = User::factory()->create()->assignRole('admin');
    $super = User::factory()->create()->assignRole('super-admin');
    $data = ['name' => 'New user', 'username' => 'new.user', 'email' => 'new@example.test', 'password' => 'Password123!', 'password_confirmation' => 'Password123!', 'role' => 'super-admin'];
    $this->actingAs($admin)->post('/users', $data)->assertSessionHasNoErrors()->assertForbidden();
    expect(User::where('email', 'new@example.test')->exists())->toBeFalse();
    $data['role'] = 'user';
    $this->put('/users/'.$super->id, $data)->assertForbidden();
    $this->delete('/roles/'.$super->roles->first()->id)->assertForbidden();
});

it('keeps notifications disabled with no routes or delivery', function () {
    $user = User::factory()->create();
    expect(config('starter.notifications.enabled'))->toBeFalse();
    $user->notify(new SystemNotification('Title', 'Message'));
    expect($user->notifications()->count())->toBe(0);
    $this->actingAs($user)->get('/notifications')->assertNotFound();
});

it('stores notifications only when explicitly enabled and scopes read operations', function () {
    config(['starter.notifications.enabled' => true]);
    $first = User::factory()->create();
    $second = User::factory()->create();
    $first->notify(new SystemNotification('Title', 'Message', '/dashboard'));
    expect($first->unreadNotifications()->count())->toBe(1);
    $controller = app(NotificationController::class);
    $request = Request::create('/notifications', 'PATCH');
    $request->setUserResolver(fn () => $second);
    try {
        $controller->read($request, $first->notifications->first()->id);
        $this->fail('Another user must not mark this notification as read.');
    } catch (ModelNotFoundException) {
        expect($first->unreadNotifications()->count())->toBe(1);
    }
});
