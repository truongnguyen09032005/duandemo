<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\VoucherController; // Thêm import VoucherController
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/', function () {
        return view('admins.layouts.master');
    });

    // Nhóm route sản phẩm
    Route::get('/products', [ProductController::class, 'index'])->name('product.index');

    //  Nhóm route danh mục
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index'); // admin.categories.index
        Route::get('/create', [CategoryController::class, 'create'])->name('create'); // admin.categories.create
        Route::post('/', [CategoryController::class, 'store'])->name('store'); // admin.categories.store
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit'); // admin.categories.edit
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update'); // admin.categories.update
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy'); // admin.categories.destroy
    });

    // Nhóm route voucher
    Route::prefix('vouchers')->name('vouchers.')->group(function () {
        Route::get('/', [VoucherController::class, 'index'])->name('index'); // admin.vouchers.index
        Route::get('/create', [VoucherController::class, 'create'])->name('create'); // admin.vouchers.create
        Route::post('/', [VoucherController::class, 'store'])->name('store'); // admin.vouchers.store
        // Route generateCode phải được đặt TRƯỚC các route có parameter
        Route::get('/generate-code', [VoucherController::class, 'generateCode'])->name('generateCode'); // admin.vouchers.generateCode
        Route::post('/bulk-action', [VoucherController::class, 'bulkAction'])->name('bulk-action'); // admin.vouchers.bulk-action
        
        // Các route có parameter phải đặt sau
        Route::get('/{voucher}', [VoucherController::class, 'show'])->name('show'); // admin.vouchers.show
        Route::get('/{voucher}/edit', [VoucherController::class, 'edit'])->name('edit'); // admin.vouchers.edit
        Route::put('/{voucher}', [VoucherController::class, 'update'])->name('update'); // admin.vouchers.update
        Route::delete('/{voucher}', [VoucherController::class, 'destroy'])->name('destroy'); // admin.vouchers.destroy
    });

    // Nhóm route đơn hàng
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index'); // admin.orders.index
        // Route export phải được đặt TRƯỚC route có parameter
        Route::get('/export/excel', [OrderController::class, 'export'])->name('export'); // admin.orders.export
        
        Route::get('/{order}', [OrderController::class, 'show'])->name('show'); // admin.orders.show
        Route::put('/{order}/status', [OrderController::class, 'updateStatus'])->name('updateStatus'); // admin.orders.updateStatus
        Route::put('/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('update-payment-status'); // admin.orders.update-payment-status
        Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy'); // admin.orders.destroy
    });

    // Nhóm route bình luận (Admin)
    Route::prefix('comments')->name('comments.')->group(function () {
        Route::get('/', [AdminCommentController::class, 'index'])->name('index'); // admin.comments.index
        // Route bulk-action phải được đặt TRƯỚC route có parameter
        Route::post('/bulk-action', [AdminCommentController::class, 'bulkAction'])->name('bulk-action'); // admin.comments.bulk-action
        
        Route::get('/{comment}', [AdminCommentController::class, 'show'])->name('show'); // admin.comments.show
        Route::put('/{comment}/approve', [AdminCommentController::class, 'approve'])->name('approve'); // admin.comments.approve
        Route::put('/{comment}/reject', [AdminCommentController::class, 'reject'])->name('reject'); // admin.comments.reject
        Route::delete('/{comment}', [AdminCommentController::class, 'destroy'])->name('destroy'); // admin.comments.destroy
        Route::post('/{comment}/reply', [AdminCommentController::class, 'reply'])->name('reply'); // admin.comments.reply
    });

});

// Routes cho phần frontend
Route::get('/', [HomeController::class, 'index'])->name('home.index'); // home.index

// Routes bình luận cho người dùng
Route::prefix('comments')->name('comments.')->middleware('auth')->group(function () {
    Route::post('/products/{product}', [CommentController::class, 'store'])->name('store'); // comments.store
    Route::put('/{comment}', [CommentController::class, 'update'])->name('update'); // comments.update
    Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('destroy'); // comments.destroy
    Route::post('/{comment}/reply', [CommentController::class, 'reply'])->name('reply'); // comments.reply
    Route::post('/{comment}/like', [CommentController::class, 'like'])->name('like'); // comments.like
    Route::delete('/{comment}/unlike', [CommentController::class, 'unlike'])->name('unlike'); // comments.unlike
});

// Route để hiển thị bình luận (không cần auth)
Route::get('/products/{product}/comments', [CommentController::class, 'index'])->name('comments.index'); // comments.index