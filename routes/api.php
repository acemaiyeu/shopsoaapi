<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\GiftController;
use App\Http\Controllers\Api\ImageProxyController;
use App\Http\Controllers\Api\ImgurController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductVarianController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\ThemeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageUploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
 * |--------------------------------------------------------------------------
 * | API Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register API routes for your application. These
 * | routes are loaded by the RouteServiceProvider and all of them will
 * | be assigned to the "api" middleware group. Make something great!
 * |
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', 'AuthController@refresh');
    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('profile', [AuthController::class, 'updateProfile']);
    Route::post('forgot', [AuthController::class, 'forGotPassword']);
    Route::get('forgot/active/{password}', [AuthController::class, 'activePassword']);
});

Route::group([
    'prefix' => 'v0'
], function ($router) {
    // Theme
    Route::get('/themes', [ThemeController::class, 'getThemes']);
    Route::get('/theme/{code}', [ThemeController::class, 'getThemeDetail']);
    // Category
    Route::get('/categories', [CategoryController::class, 'getCategory']);
    // Route::get('/category/{id}', [CategoryController::class, 'getDetail']);
    // session
    Route::get('/sessions', [UserController::class, 'getSession']);
    Route::post('/sessions', [UserController::class, 'addSession']);
    // cart
    Route::get('/cart', [CartController::class, 'getCart']);
    Route::post('/addToCart', [CartController::class, 'addToCart']);
    Route::put('/updateCartInfo', [CartController::class, 'updateCartInfo']);
    // products
    Route::get('/fillter/products', [ProductController::class, 'getFillterForProductType']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/product-detail/{id}', [ProductController::class, 'detail']);
    Route::get('/product-detail', [ProductController::class, 'detail']);
    Route::get('/Coupons', [CouponController::class, 'getCouponClient']);
    Route::post('/add-discount', [DiscountController::class, 'addDiscountCart']);
    Route::get('/discounts', [DiscountController::class, 'getDiscounts']);

    // Order
    Route::post('/confirm-order', [OrderController::class, 'confirmOrder']);
    Route::get('/order-detail/{phone}/{code}', [OrderController::class, 'detail']);
    Route::get('/my-order/{code}', [OrderController::class, 'detailByCode']);

    // PRomotion Product Page Home
    Route::get('/promotion_show', [PromotionController::class, 'getPromotionsForWeb']);

    // Warehouse
    Route::get('/get-location', [WarehouseController::class, 'getLocation']);
});

Route::group([
    'prefix' => 'v1',
    'middleware' => 'api.admin'
], function ($router) {
    // Theme
    Route::get('/themes', [ThemeController::class, 'getThemesAdmin']);
    Route::get('/theme-promotion', [ThemeController::class, 'getThemeForPromotion']);
    Route::post('/theme', [ThemeController::class, 'save']);
    Route::delete('/theme/{id}', [ThemeController::class, 'deleteById']);
    // Category
    Route::post('/category', [CategoryController::class, 'create']);
    Route::put('/category', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'deleteCategory']);
    Route::get('/category/{code}', [CategoryController::class, 'getDetailByCode']);
    // Promotion
    Route::get('/promotions', [PromotionController::class, 'getPromotions']);
    Route::get('/promotion-products', [PromotionController::class, 'getProductPromotions']);
    Route::get('/promotion/{id}', [PromotionController::class, 'getDetail']);
    Route::post('/promotion', [PromotionController::class, 'create']);

    Route::get('/statistics-orders', [OrderController::class, 'statisticsOrders']);

    Route::get('/statistics-orders-fits', [OrderController::class, 'statisticsOrdersRevenue']);
    Route::get('/orders', [OrderController::class, 'getAllOrders']);
    Route::get('/my-orders', [OrderController::class, 'getAllOrdersByClient']);

    // Profile
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::put('/change-password', [UserController::class, 'changePassword']);
    // Order
    Route::get('/order-detail/{id}', [OrderController::class, 'detailAdmin']);
    Route::put('/order-update', [OrderController::class, 'updateOrder']);
    Route::get('/order-status/{id}', [OrderController::class, 'getStatus']);
    // Router::get('/orders', [OrderController::class, 'getAllOrders']);

    // Coupon
    Route::get('/coupons', [CouponController::class, 'getCoupons']);
    Route::get('/coupon/{code}', [CouponController::class, 'detail']);
    Route::put('/coupon', [CouponController::class, 'update']);
    Route::post('/create-coupon', [CouponController::class, 'createCoupon']);
    Route::put('/update-coupon', [CouponController::class, 'updateCoupon']);
    Route::delete('/delete-coupon/{code}', [CouponController::class, 'deleteCoupon']);

    // Warehouse
    Route::get('/warehouses', [WarehouseController::class, 'getAllWarehouse']);
    Route::get('/warehouse-detail/{id}', [WarehouseController::class, 'getWarehouseDetail']);
    Route::get('/warehouse-detail-by-warehouse-id/{id}', [WarehouseController::class, 'getWarehouseDetail']);
    Route::get('/warehouse-product-detail/{id}', [WarehouseController::class, 'getWarehouseProductDetail']);
    Route::get('/warehouse-product-detail-by-warehouse-id/{warehouse_id}', [WarehouseController::class, 'getWarehouseProductDetailByWarehouse']);
    Route::post('/warehouse-product-detail', [WarehouseController::class, 'saveWarehouseProductDetail']);

    Route::post('/warehouse', [WarehouseController::class, 'saveWarehouse']);

    // Route::post('/upload-image', [ImgurController::class, 'uploadImage']);
    Route::get('/proxy-image', [ImageProxyController::class, 'fetchImage']);
    Route::post('/upload-image', [ImageUploadController::class, 'upload']);

    // Gift
    Route::get('/gifts', [GiftController::class, 'getGift']);
    Route::get('/gift/{id}', [GiftController::class, 'detail']);
    Route::post('/gift', [GiftController::class, 'create']);
    Route::put('/gift', [GiftController::class, 'update']);
});
// Route::group(['prefix' => 'v0'])->get('/products', [ProductController::class, 'index']);
