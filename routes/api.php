<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductDealsController;
use App\Http\Controllers\ProductOrdersController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductShoppingCartController;
use App\Http\Controllers\ProductWishlistController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\NewsLetterController;
use App\Http\Controllers\UserOrderController;
use App\Http\Controllers\UserOrderDetailController;

Route::middleware('auth:api')->get('/user', 
    function(Request $request) {
        return $request->user();
    }   
);

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index']);

// JWT Authenficiation
// Route::get('/auth', 'App\Http\Controllers\UserController@getAuthenticatedUser');
Route::get('/auth', [UserController::class, 'me']);
Route::get('/users', [UserController::class, 'index']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);

// Address
Route::get('/user/default-address', [UserAddressController::class, 'show']);
Route::post('/user/create-user-address', [UserAddressController::class, 'createUser']);
Route::post('/user/address', [UserAddressController::class, 'store']);

// Product
Route::get('/products/newest', [ProductController::class, 'newestProduct']);
Route::get('/products/top-selling', [ProductController::class, 'topSelling']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/search/{name}', [ProductController::class, 'search']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::get('/products/categories/{id}', [ProductController::class, 'productByCategory']);

// Product Deal
Route::get('/product/hot-deal', [ProductDealsController::class,'hotDeals']);

// Product Orders
// Route::post('/stripe', [ProductOrdersController::class,'stripePost']);
Route::post('/product/orders', [ProductOrdersController::class,'store']);
Route::get('/product/orders', [ProductOrdersController::class,'index']);

// Order
Route::post('/order', [UserOrderController::class,'store']);
Route::get('/orders', [UserOrderController::class,'index']);
Route::get('/orders/{id}', [UserOrderController::class,'show']);
Route::put('/orders/{id}', [UserOrderController::class,'update']);

// Order Detail
Route::post('/orderdetail', [UserOrderDetailController::class,'store']);
Route::get('/orderdetail/{id}', [UserOrderDetailController::class,'index']);

// Categories
Route::get('/categories', [CategoryController::class,'index']);
Route::get('/categories/{id}', [CategoryController::class,'show']);
Route::post('/categories', [CategoryController::class,'store']);
Route::delete('/categories/{id}', [CategoryController::class,'destroy']);
Route::put('/categories/{id}', [CategoryController::class,'update']);


// Product Shopping Cart
Route::get('/product/cart-list/count', [ProductShoppingCartController::class,'cartCount']);
Route::get('/product/cart-list', [ProductShoppingCartController::class,'index']);
Route::post('/product/cart-list', [ProductShoppingCartController::class,'store']);
Route::post('/product/cart-list/guest', [ProductShoppingCartController::class,'guestCart']);
Route::put('/product/cart-list/{id}', [ProductShoppingCartController::class,'update']);
Route::get('/product/cart-list/{id}', [ProductShoppingCartController::class,'show']);
Route::delete('/product/cart-list/{id}', [ProductShoppingCartController::class,'destroy']);

//Product Wishlist
Route::get('/product/wishlist/count', [ProductWishlistController::class,'count']);
Route::get('/product/wishlist', [ProductWishlistController::class,'index']);
Route::post('/product/wishlist', [ProductWishlistController::class,'store']);
Route::delete('/product/wishlist/{id}', [ProductWishlistController::class,'destroy']);

// Product Stocks
Route::get('/stocks/{id}', [StockController::class,'show']);
Route::put('/stocks/{id}', [StockController::class,'update']);

// Newsletter
Route::post('/newsletter', [NewsLetterController::class,'store']);