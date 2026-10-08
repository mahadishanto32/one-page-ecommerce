<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HomeSliderController;
use App\Http\Controllers\Admin\ProductCategoriesController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductAttributeController;
use App\Http\Controllers\Admin\ProductAttributeOptionController;
use App\Http\Controllers\Admin\ManageOrderController;
use App\Http\Controllers\Admin\ReviewsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Models\Order;

Route::prefix('admin')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Home Slider Routes
    Route::resource('homesliders', HomeSliderController::class)->names('admin.homesliders');
    Route::patch('homesliders/{homeslider}/toggle-status', [HomeSliderController::class, 'toggleStatus'])->name('admin.homesliders.toggle-status');

    // Product Categories Routes
    Route::resource('product-categories', ProductCategoriesController::class)->names('admin.product-categories');

    // Product Categories Routes
    Route::resource('product-categories', ProductCategoriesController::class)->names('admin.product-categories');

    Route::resource('products', ProductController::class)->names('admin.products');
    Route::get('products-subcategories/ajax', [ProductController::class, 'getSubcategories'])->name('admin.products.subcategories');
    Route::delete('products/{product}/gallery-image', [ProductController::class, 'removeGalleryImage'])->name('admin.products.gallery-image.destroy');
    Route::get('/get-attribute-options', [ProductController::class, 'getAttributeOptions'])->name('admin.get-attribute-options');

    // Product Attributes Routes
    Route::resource('attributes', ProductAttributeController::class)->names('admin.attributes');

    // Product Attribute Options Routes
    Route::resource('attribute-options', ProductAttributeOptionController::class)->names('admin.attribute-options');

    // Reviews Routes
    Route::resource('reviews', ReviewsController::class)->names('admin.reviews');

    // Order Management Routes 
    Route::get('/orders/incomplete', [ManageOrderController::class, 'incomplete'])->name('admin.orders.incomplete');
    Route::resource('orders', ManageOrderController::class)->names('admin.orders');
    Route::patch('orders/{order}/status', [ManageOrderController::class, 'updateStatus'])->name('admin.orders.update-status');
    
    // Settings Routes 
    Route::get('settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('settings/update-user', [SettingsController::class, 'updateUser'])->name('admin.settings.update-user');
    Route::put('settings/{id}', [SettingsController::class, 'update'])->name('admin.settings.update');
    Route::post('settings/{id}/reset', [SettingsController::class, 'reset'])->name('admin.settings.reset');
});
