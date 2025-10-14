<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\CategoryMenusController;
use App\Http\Controllers\Api\CustomerAddressesController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerUserController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EmployeeAddressesController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\EmployeeUserController;
use App\Http\Controllers\Api\MenuCategoryController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\PermissionRolesController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RolePermissionsController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\TableOrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserCustomerController;
use App\Http\Controllers\Api\UserEmployeeController;
use App\Http\Controllers\Api\UserRoleController;
use App\Http\Middleware\ValidateJsonApiDocument;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware(ValidateJsonApiDocument::class)->name('api.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
});

Route::withoutMiddleware(ValidateJsonApiDocument::class)->name('api.auth.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('me', [AuthController::class, 'meShow'])->name('me.show');
    Route::patch('me', [AuthController::class, 'meUpdate'])->name('me.update');
    Route::post('register', [AuthController::class, 'register'])->name('register');
});

Route::apiResource('states', StateController::class)->names('api.states');

Route::apiResource('roles', RoleController::class)->names('api.roles');
Route::prefix('roles/{role}')->name('api.roles.')->group(function () {
    Route::get('permissions', [RolePermissionsController::class, 'index'])->name('permissions');
    Route::prefix('relationships/permissions')->name('relationships.permissions.')->group(function () {
        Route::get('', [RolePermissionsController::class, 'showRelationship'])->name('show');
        Route::patch('', [RolePermissionsController::class, 'updateRelationship'])->name('update');
        Route::post('', [RolePermissionsController::class, 'attachRelationship'])->name('attach');
        Route::delete('', [RolePermissionsController::class, 'detachRelationship'])->name('detach');
    });
});

Route::apiResource('permissions', PermissionController::class)->only(['index', 'show'])->names('api.permissions');
Route::get('permissions/{permission}/roles', [PermissionRolesController::class, 'index'])
    ->name('api.permissions.roles');
Route::get('permissions/{permission}/relationships/roles', [PermissionRolesController::class, 'showRelationship'])
    ->name('api.permissions.relationships.roles.show');

Route::apiResource('addresses', AddressController::class)->only('show')->names('api.addresses');

Route::apiResource('customers', CustomerController::class)->names('api.customers');
Route::prefix('customers/{customer}')->name('api.customers.')->group(function () {
    Route::get('user', [CustomerUserController::class, 'index'])->name('user');
    Route::get('relationships/user', [CustomerUserController::class, 'showRelationship'])->name('relationships.user.show');

    Route::get('addresses', [CustomerAddressesController::class, 'index'])->name('addresses');
    Route::post('addresses', [CustomerAddressesController::class, 'store'])->name('addresses.store');
    Route::get('addresses/{address}', [CustomerAddressesController::class, 'show'])->name('addresses.show');
    Route::patch('addresses/{address}', [CustomerAddressesController::class, 'update'])->name('addresses.update');
    Route::delete('addresses/{address}', [CustomerAddressesController::class, 'destroy'])->name('addresses.destroy');

    Route::prefix('relationships/addresses')->name('relationships.addresses.')->group(function () {
        Route::get('', [CustomerAddressesController::class, 'showRelationship'])->name('show');
    });
});

Route::apiResource('employees', EmployeeController::class)->names('api.employees');
Route::prefix('employees/{employee}')->name('api.employees.')->group(function () {
    Route::get('user', [EmployeeUserController::class, 'index'])->name('user');
    Route::get('relationships/user', [EmployeeUserController::class, 'showRelationship'])->name('relationships.user.show');

    Route::get('addresses', [EmployeeAddressesController::class, 'index'])->name('addresses');
    Route::post('addresses', [EmployeeAddressesController::class, 'store'])->name('addresses.store');
    Route::get('addresses/{address}', [EmployeeAddressesController::class, 'show'])->name('addresses.show');
    Route::patch('addresses/{address}', [EmployeeAddressesController::class, 'update'])->name('addresses.update');
    Route::delete('addresses/{address}', [EmployeeAddressesController::class, 'destroy'])->name('addresses.destroy');

    Route::prefix('relationships/addresses')->name('relationships.addresses.')->group(function () {
        Route::get('', [EmployeeAddressesController::class, 'showRelationship'])->name('show');
    });
});

Route::apiResource('users', UserController::class)->names('api.users');
Route::patch('users/{user}/password-reset', [UserController::class, 'resetPassword'])
    ->name('api.users.resetPassword')
    ->withoutMiddleware(ValidateJsonApiDocument::class);

Route::prefix('users/{user}')->name('api.users.')->group(function () {

    Route::get('role', [UserRoleController::class, 'index'])->name('role');
    Route::get('relationships/role', [UserRoleController::class, 'showRelationship'])->name('relationships.role.show');
    Route::patch('relationships/role', [UserRoleController::class, 'updateRelationship'])->name('relationships.role.update');

    Route::get('employee', [UserEmployeeController::class, 'index'])->name('employee');
    Route::get('relationships/employee', [UserEmployeeController::class, 'showRelationship'])->name('relationships.employee.show');

    Route::get('customer', [UserCustomerController::class, 'index'])->name('customer');
    Route::get('relationships/customer', [UserCustomerController::class, 'showRelationship'])->name('relationships.customer.show');
});

Route::apiResource('categories', CategoryController::class)->names('api.categories');
Route::get('categories/{category}/menus', [CategoryMenusController::class, 'index'])
    ->name('api.categories.menus');
Route::get('categories/{category}/relationships/menus', [CategoryMenusController::class, 'showRelationship'])
    ->name('api.categories.relationships.menus.show');

Route::apiResource('tables', TableController::class)->names('api.tables');
Route::get('tables/{table}/orders', [TableOrderController::class, 'index'])
    ->name('api.tables.orders');
Route::get('tables/{table}/relationships/orders', [TableOrderController::class, 'showRelationship'])
    ->name('api.tables.relationships.orders.show');

Route::apiResource('menus', MenuController::class)->names('api.menus');
Route::post('menus/{menu}/photo', [MenuController::class, 'updatePhoto'])->name('api.menus.update.photo');
Route::get('menus/{menu}/category', [MenuCategoryController::class, 'index'])
    ->name('api.menus.category');
Route::get('menus/{menu}/relationships/category', [MenuCategoryController::class, 'showRelationship'])
    ->name('api.menus.relationships.category.show');
Route::patch('menus/{menu}/relationships/category', [MenuCategoryController::class, 'updateRelationship'])
    ->name('api.menus.relationships.category.update');
