<?php

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

// Order routes
Route::post('/order', [OrderController::class, 'store'])->name('frontend.order.store');
Route::get('/thank-you/{order}', [OrderController::class, 'thankyou'])->name('frontend.order.thankyou');
Route::post('/order/incomplete', [OrderController::class, 'saveIncomplete'])->name('frontend.order.incomplete');

// Authentication Routes
Route::get('/login', [UserController::class, 'showLoginForm'])->name('admin.login.form');
Route::post('/login', [UserController::class, 'login'])->name('admin.login');
Route::get('/register', [UserController::class, 'showRegisterForm'])->name('admin.register');
Route::post('/register', [UserController::class, 'register'])->name('admin.register.post');
Route::post('/logout', [UserController::class, 'logout'])->name('admin.logout');

// Admin Routes (protected)
require __DIR__ . '/admin.php';
