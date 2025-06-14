<?php

use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\OrganizationController;


// Public routes (no auth required)
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/refresh', [AuthController::class, 'refresh'])->name('refresh');
});

// Authenticated routes (all need to be logged in)
Route::middleware('throttle:20,1')->middleware('auth:api')->group(function () {
    //Basic Routes
    Route::get('/validate', [AuthController::class, 'validateAccessToken']);
    Route::get('/dropdowns', [DropdownController::class, 'getDropdownData']);

    // Users Routes
    Route::middleware('checkPermission:view')->get('/users', [UserController::class, 'index']);
    Route::middleware('checkPermission:create')->post('/user', [UserController::class, 'store']);
    Route::middleware('checkPermission:edit')->put('/user/{id}', [UserController::class, 'update']);

    // Permissions Routes
    Route::middleware('checkPermission:view')->get('/permissions', [PermissionController::class, 'index']);
    Route::middleware('checkPermission:create')->post('/permission', [PermissionController::class, 'store']);

    // Organization Routes
    Route::middleware('checkPermission:create')->post('/organization', [OrganizationController::class, 'store']);
    Route::middleware('checkPermission:view')->get('/organizations', [OrganizationController::class, 'index']);
    Route::middleware('checkPermission:edit')->put('/organization/{id}', [OrganizationController::class, 'update']);   
});

