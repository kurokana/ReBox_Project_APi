<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BoxController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PengepulController;
use App\Http\Controllers\Api\WasteSaleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'getProfile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile/avatar', [AuthController::class, 'updateAvatar']);
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    });
    
    // Resources
    Route::apiResource('boxes', BoxController::class);
    Route::apiResource('items', ItemController::class);
    Route::apiResource('categories', CategoryController::class);

    // ============= WASTE SALES ROUTES =============
    Route::prefix('waste-sales')->group(function () {
        Route::get('/', [WasteSaleController::class, 'index']);
        Route::post('/', [WasteSaleController::class, 'store']);
        Route::get('/{id}', [WasteSaleController::class, 'show']);
        Route::delete('/{id}', [WasteSaleController::class, 'destroy']);
        
        // Admin only routes
        Route::middleware('role:admin')->group(function () {
            Route::put('/{id}/status', [WasteSaleController::class, 'updateStatus']);
            Route::get('/statistics/summary', [WasteSaleController::class, 'statistics']);
        });
    });

    // ============= ADMIN ROUTES =============
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Mengelola Kategori
        Route::get('/categories', [AdminController::class, 'getCategories']);
        Route::post('/categories', [AdminController::class, 'createCategory']);
        Route::put('/categories/{id}', [AdminController::class, 'updateCategory']);
        Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory']);
        
        // Melihat Notifikasi (semua)
        Route::get('/notifications', [AdminController::class, 'getAllNotifications']);
        Route::post('/notifications', [AdminController::class, 'createNotification']);
        
        // Melihat Riwayat Pesanan (semua transaksi)
        Route::get('/transactions', [AdminController::class, 'getAllTransactions']);
        Route::get('/transactions/stats', [AdminController::class, 'getTransactionStats']);
        
        // Menerima Pembayaran/Pencairan
        Route::get('/payments/pengepul', [AdminController::class, 'getPengepulPayments']);
        Route::post('/payments/process', [AdminController::class, 'processPayment']);
    });

    // ============= PENGEPUL ROUTES =============
    Route::middleware('role:pengepul')->prefix('pengepul')->group(function () {
        // Mengakses Beranda (Dashboard)
        Route::get('/dashboard', [PengepulController::class, 'getDashboard']);
        
        // Melihat Daftar Kotak (Pesanan Available)
        Route::get('/boxes/available', [PengepulController::class, 'getAvailableBoxes']);
        Route::get('/boxes/{id}', [PengepulController::class, 'getBoxDetails']);
        
        // Melakukan Transaksi (Jual Sampah)
        Route::post('/transactions', [PengepulController::class, 'createTransaction']);
        Route::get('/transactions', [PengepulController::class, 'getMyTransactions']);
        Route::put('/transactions/{id}/status', [PengepulController::class, 'updateTransactionStatus']);
    });

    // ============= PENGGUNA ROUTES (Transactions) =============
    Route::prefix('pengguna')->group(function () {
        // Get my transactions as pengguna (seller)
        Route::get('/transactions', [AuthController::class, 'getPenggunaTransactions']);
        Route::put('/transactions/{id}/accept', [AuthController::class, 'acceptTransaction']);
        Route::put('/transactions/{id}/reject', [AuthController::class, 'rejectTransaction']);
        
        // Notifications
        Route::get('/notifications', [AuthController::class, 'getMyNotifications']);
        Route::put('/notifications/{id}/read', [AuthController::class, 'markNotificationAsRead']);
    });
});
