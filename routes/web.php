<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShipperController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Portal Routes
Route::get('/', [LoginController::class, 'showLoginForm'])->name('home');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/login/quick/{userId}', [LoginController::class, 'quickLogin'])->name('login.quick');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Common Notifications Route
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications/mark-all-read', function () {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markAllRead');
});

// Admin Module Routes (Prefix: admin, Middleware: role 1)
Route::middleware(['auth', 'role:1'])->prefix('admin')->group(function () {
    // Dashboard & Overview
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Orders CRUD + Export CSV
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::post('/orders', [AdminController::class, 'storeOrder'])->name('admin.orders.store');
    Route::put('/orders/{id}', [AdminController::class, 'updateOrder'])->name('admin.orders.update');
    Route::delete('/orders/{id}', [AdminController::class, 'destroyOrder'])->name('admin.orders.destroy');
    Route::get('/orders/export-csv', [AdminController::class, 'exportOrdersCsv'])->name('admin.orders.export');
    Route::get('/orders/{id}/print', [AdminController::class, 'printOrder'])->name('admin.orders.print');

    // Shippers CRUD & Status Toggle
    Route::get('/shippers', [AdminController::class, 'shippers'])->name('admin.shippers');
    Route::post('/shippers', [AdminController::class, 'storeShipper'])->name('admin.shippers.store');
    Route::put('/shippers/{id}', [AdminController::class, 'updateShipper'])->name('admin.shippers.update');
    Route::delete('/shippers/{id}', [AdminController::class, 'destroyShipper'])->name('admin.shippers.destroy');
    Route::post('/shippers/{id}/toggle', [AdminController::class, 'toggleShipperStatus'])->name('admin.shippers.toggle');

    // Customers CRUD
    Route::get('/customers', [AdminController::class, 'customers'])->name('admin.customers');
    Route::post('/customers', [AdminController::class, 'storeCustomer'])->name('admin.customers.store');
    Route::put('/customers/{id}', [AdminController::class, 'updateCustomer'])->name('admin.customers.update');
    Route::delete('/customers/{id}', [AdminController::class, 'destroyCustomer'])->name('admin.customers.destroy');

    // Hubs CRUD
    Route::get('/hubs', [AdminController::class, 'hubs'])->name('admin.hubs');
    Route::post('/hubs', [AdminController::class, 'storeHub'])->name('admin.hubs.store');
    Route::put('/hubs/{id}', [AdminController::class, 'updateHub'])->name('admin.hubs.update');
    Route::delete('/hubs/{id}', [AdminController::class, 'destroyHub'])->name('admin.hubs.destroy');

    // Districts Shipping Rates CRUD
    Route::get('/rates', [AdminController::class, 'rates'])->name('admin.rates');
    Route::post('/rates', [AdminController::class, 'storeRate'])->name('admin.rates.store');
    Route::put('/rates/{id}', [AdminController::class, 'updateRate'])->name('admin.rates.update');
    Route::delete('/rates/{id}', [AdminController::class, 'destroyRate'])->name('admin.rates.destroy');
    Route::post('/settings/update', [AdminController::class, 'updateSettings'])->name('admin.settings.update');

    // Vouchers CRUD
    Route::get('/vouchers', [AdminController::class, 'vouchers'])->name('admin.vouchers');
    Route::post('/vouchers', [AdminController::class, 'storeVoucher'])->name('admin.vouchers.store');
    Route::put('/vouchers/{id}', [AdminController::class, 'updateVoucher'])->name('admin.vouchers.update');
    Route::delete('/vouchers/{id}', [AdminController::class, 'destroyVoucher'])->name('admin.vouchers.destroy');

    // Routes & Optimization
    Route::get('/routes', [AdminController::class, 'routes'])->name('admin.routes');
    Route::post('/routes', [AdminController::class, 'storeRoute'])->name('admin.routes.store');
    Route::put('/routes/{id}', [AdminController::class, 'updateRoute'])->name('admin.routes.update');
    Route::post('/routes/{id}/assign-shipper', [AdminController::class, 'assignShipperToRoute'])->name('admin.routes.assign');
    Route::delete('/routes/{id}', [AdminController::class, 'destroyRoute'])->name('admin.routes.destroy');


    // === HỆ THỐNG ĐIỀU PHỐI THÔNG MINH ===
    Route::get('/dispatch', [DispatchController::class, 'index'])->name('admin.dispatch');
    Route::post('/dispatch/batch', [DispatchController::class, 'batchOrders'])->name('admin.dispatch.batch');
    Route::post('/dispatch/optimize/{routeId}', [DispatchController::class, 'optimizeRoute'])->name('admin.dispatch.optimize');
    Route::post('/dispatch/auto-assign', [DispatchController::class, 'autoAssign'])->name('admin.dispatch.auto-assign');
    Route::get('/dispatch/preview-batch', [DispatchController::class, 'previewBatch'])->name('admin.dispatch.preview-batch');

    
    // API endpoints cho Phân Tích Nâng Cao
    Route::get('/dispatch/api/priority-orders', [DispatchController::class, 'getPriorityOrders'])->name('admin.dispatch.priority-orders');
    Route::get('/dispatch/api/compare-shippers', [DispatchController::class, 'compareShippers'])->name('admin.dispatch.compare-shippers');
    Route::get('/dispatch/api/shipper-capacity', [DispatchController::class, 'getShipperCapacity'])->name('admin.dispatch.shipper-capacity');

    // === QUẢN LÝ NGƯỜI DÙNG TỔNG HỢP ===
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');
});

// Shipper Module Routes (Prefix: shipper, Middleware: role 2)
Route::middleware(['auth', 'role:2'])->prefix('shipper')->group(function () {
    Route::get('/dashboard', [ShipperController::class, 'dashboard'])->name('shipper.dashboard');
    Route::post('/orders/{id}/delivered', [ShipperController::class, 'updateStatus'])->name('shipper.orders.delivered');
    Route::post('/routes/{id}/accept', [ShipperController::class, 'acceptRoute'])->name('shipper.routes.accept');
    Route::post('/orders/{id}/accept', [ShipperController::class, 'acceptOrder'])->name('shipper.orders.accept');
    Route::post('/orders/{id}/decline', [ShipperController::class, 'declineOrder'])->name('shipper.orders.decline');
    Route::post('/orders/{id}/fail', [ShipperController::class, 'failOrder'])->name('shipper.orders.fail');
    Route::post('/toggle-status', [ShipperController::class, 'toggleStatus'])->name('shipper.toggle-status');
    Route::get('/profile', [ShipperController::class, 'profile'])->name('shipper.profile');
    Route::post('/profile/update', [ShipperController::class, 'updateProfile'])->name('shipper.profile.update');
    Route::get('/earnings', [ShipperController::class, 'earnings'])->name('shipper.earnings');
});

// Customer Module Routes (Prefix: customer, Middleware: role 3)
Route::middleware(['auth', 'role:3'])->prefix('customer')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
    Route::get('/orders/create', [CustomerController::class, 'createOrder'])->name('customer.orders.create');
    Route::post('/orders', [CustomerController::class, 'storeOrder'])->name('customer.orders.store');
    Route::get('/orders/{id}/track', [CustomerController::class, 'trackOrder'])->name('customer.orders.track');
    Route::post('/orders/{id}/cancel', [CustomerController::class, 'cancelOrder'])->name('customer.orders.cancel');
    Route::get('/vouchers', [CustomerController::class, 'vouchers'])->name('customer.vouchers');
    Route::post('/vouchers/apply', [CustomerController::class, 'applyVoucher'])->name('customer.vouchers.apply');
    Route::get('/profile', [CustomerController::class, 'profile'])->name('customer.profile');
    Route::post('/profile/update', [CustomerController::class, 'updateProfile'])->name('customer.profile.update');

    // Payment routes
    Route::get('/payment/{orderId}', [App\Http\Controllers\PaymentController::class, 'checkout'])->name('customer.payment.checkout');
    Route::post('/payment/{orderId}/process', [App\Http\Controllers\PaymentController::class, 'process'])->name('customer.payment.process');
});
