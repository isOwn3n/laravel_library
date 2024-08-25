<?php

use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\BookController;
use App\Http\Controllers\API\v1\BorrowingController;
use App\Http\Controllers\API\v1\CategoryController;
use App\Http\Controllers\API\v1\MembershipPlanController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::prefix('book')->controller(BookController::class)->group(function () {
            Route::get('', 'index')->name('book-list');
            Route::get('/{slug}', 'show')->name('book-list');
            Route::post('', 'store')->name('book-create');
            Route::put('/{slug}', 'update')->name('book-update');
            Route::delete('/{slug}', 'destroy')->name('book-delete');
        });

        Route::prefix('borrowing')->controller(BorrowingController::class)->group(function () {
            Route::get('', 'index')->name('borrowing-list');
            Route::post('', 'borrow')->name('borrowing-create');
            Route::put('return/{id}', 'returned');
            Route::get('category/{id}', 'getBorrowingsByCategory')
                ->name('borrowing-get-by-category');

            Route::get('user/{id}', 'getBorrowingsByUser')->name('borrowing-get-by-user');
            Route::get('book/{id}', 'getBorrowingsByBook')->name('borrowing-get-by-book');
        })->middleware('borrowing.check_admin');

        Route::prefix('category')->controller(CategoryController::class)->group(function () {
            Route::get('', 'index');
            Route::get('/{id}', 'show');
            Route::post('', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        Route::prefix('membership-plan')->controller(MembershipPlanController::class)->group(function () {
            Route::get('', 'index');
            Route::put('/{id}', 'index');
            Route::post('', 'store');
            Route::delete('/{id}', 'destroy');
            Route::get('/users/{id}', 'getUsersById');
        });

        Route::prefix('auth')->controller(AuthController::class)->group(function () {
            Route::post('/logout', 'logout')->name('logout');
            Route::post('/refresh', 'refresh')->name('refresh');
            Route::get('/me', 'me')->name('me');
        });
    });

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });
});
