<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\EmployeeController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Employee API Routes
    Route::get('/employees/create-data', [EmployeeController::class, 'createData']);
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit']); // Keep edit to fetch data for edit form
    Route::apiResource('employees', EmployeeController::class);
});

Route::get('/hello', function () {
    return response()->json(['message' => 'Hello from Laravel API!']);
});


