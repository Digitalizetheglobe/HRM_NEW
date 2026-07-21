<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('dashboard', function () {
        if (auth()->user()->isEmployee()) {
            return view('dashboard.employeedashboard');
        }
        return view('dashboard.companydashboard');
    })->name('dashboard');

    // Employee Management & Settings (Company Only)
    Route::middleware('company')->group(function () {
        Route::resource('employees', EmployeeController::class);
        
        // System Settings Hub & Sub-modules
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::resource('branches', BranchController::class)->except(['show']);
        Route::resource('departments', DepartmentController::class)->except(['show']);
        Route::resource('designations', DesignationController::class)->except(['show']);
    });
});
