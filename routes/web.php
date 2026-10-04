<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectThemeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'))->name('home');
Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::middleware('permission:users.manage')->group(function (): void {
        Route::resource('users', UserController::class)->except('show');
        Route::post('users/{user}/restore', [UserController::class, 'restore'])->withTrashed()->name('users.restore');
        Route::delete('users/{user}/force', [UserController::class, 'forceDestroy'])->withTrashed()->name('users.force-destroy');
    });
    Route::middleware('permission:roles.manage')->group(function (): void {
        Route::resource('roles', RoleController::class)->except('show');
        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
    });
    Route::get('activity-log', [ActivityLogController::class, 'index'])->middleware('permission:activity.view')->name('activity-log.index');
    Route::middleware('permission:project.manage')->group(function (): void {
        Route::get('project/theme', [ProjectThemeController::class, 'edit'])->name('project.theme.edit');
        Route::put('project/theme', [ProjectThemeController::class, 'update'])->name('project.theme.update');
    });
    if (config('starter.notifications.enabled')) {
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
        Route::patch('notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    }
});
require __DIR__.'/settings.php';
