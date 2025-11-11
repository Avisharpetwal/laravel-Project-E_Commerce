<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\CouponController;

Route::get('/', function () {
    return view('welcome');
});

// ---------------------- Authenticated Dashboard Redirect ----------------------
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    $user = auth()->user();
    return $user->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('user.dashboard');
})->name('dashboard');


// Public 
Route::get('/products', [ProductController::class, 'Userdashboard'])->name('products.list');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{id}/products', [ProductController::class, 'categoryProducts'])->name('products.category');

// ---------------------- User Routes ----------------------
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');

    // User Dashboard (Products)
    Route::get('/user/dashboard', [ProductController::class, 'Userdashboard'])->name('user.dashboard');
    // Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.applyCoupon');
    Route::post('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.removeCoupon');

    // Wishlist (Database-based)
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add/{id}', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove/{id}', [WishlistController::class, 'remove'])->name('wishlist.remove');


     Route::get('/checkout', [OrderController::class, 'checkoutForm'])->name('checkout.form');
    Route::post('/checkout', [OrderController::class, 'placeOrder'])->name('checkout.place');
    Route::get('/orders/success', [OrderController::class, 'orderSuccess'])->name('orders.success');
    Route::get('/orders', [OrderController::class, 'myOrders'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/products/{product}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/products/{product}/reviews/video-upload', [\App\Http\Controllers\ReviewController::class, 'uploadVideo'])->name('reviews.uploadVideo');
    Route::post('/products/{id}/reviews', [ReviewController::class, 'store'])->name('reviews.store');


}); 

// ---------------------- Admin Routes ----------------------
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users/{user}/toggle', [AdminController::class, 'toggleBlock'])->name('admin.users.toggle');
    
    Route::post('/notifications/read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('admin.notifications.read');

    // Admin Products
    Route::resource('products', ProductController::class, ['as' => 'admin']);
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    
    // Admin Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::post('/categories/{category}/update', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::get('/admin/manage-orders', [App\Http\Controllers\Admin\AdminController::class, 'manageOrders'])->name('admin.manage.orders');
    Route::put('/admin/orders/{id}/update', [App\Http\Controllers\Admin\AdminController::class, 'updateOrderStatus'])->name('admin.update.order.status');
    // Route::get('/manage-orders', [OrderController::class, 'adminIndex'])->name('admin.manage.orders');
    Route::get('/manage-orders/{id}', [OrderController::class, 'adminShow'])->name('admin.order.show');
    // Route::put('/manage-orders/{id}/update-status', [OrderController::class, 'adminUpdateStatus'])->name('admin.update.order.status');
    Route::get('/coupons', [CouponController::class, 'index'])->name('admin.coupons.index');
    Route::get('/coupons/create', [CouponController::class, 'create'])->name('admin.coupons.create');
    Route::post('/coupons', [CouponController::class, 'store'])->name('admin.coupons.store');
    Route::get('/coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('admin.coupons.edit');
    Route::put('/coupons/{coupon}', [CouponController::class, 'update'])->name('admin.coupons.update');
    Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy'])->name('admin.coupons.destroy');
    Route::patch('/admin/orders/{order}/confirm', [OrderController::class, 'confirmOrder'])->name('admin.orders.confirm');

   //Subtest
    Route::get('/admin/categories/{id}/subcategories', [CategoryController::class, 'getSubcategories'])
     ->name('admin.categories.subcategories');

});

// ---------------------- Cart Routes (Session-based) ----------------------    
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');


// Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

require __DIR__.'/auth.php';

