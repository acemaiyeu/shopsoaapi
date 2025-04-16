<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\PromoCodeController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductVarianController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ThemeController;
use App\Http\Controllers\Api\DiscountController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
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

});


Route::group([
    'prefix' => 'v0'
], function($router){
            //Theme
        Route::get('/themes', [ThemeController::class, 'getThemes']);
        Route::get('/theme/{code}', [ThemeController::class, 'getThemeDetail']);
        //Category
        Route::get('/categories', [CategoryController::class, 'getCategory']);
        Route::get('/category/{id}', [CategoryController::class, 'getDetail']);
        //session
        Route::get('/sessions', [UserController::class, 'getSession']);
        Route::post('/sessions', [UserController::class, 'addSession']);
        //cart
        Route::get('/cart', [CartController::class, 'getCart']);
        Route::post('/addToCart', [CartController::class, 'addToCart']);
        Route::put('/updateCartInfo', [CartController::class, 'updateCartInfo']);
        //products
        Route::get('/fillter/products', [ProductController::class, 'getFillterForProductType']);
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/product-detail/{id}', [ProductController::class, 'detail']);
        Route::get('/product-detail', [ProductController::class, 'detail']);
        Route::get('/promocodes', [PromoCodeController::class, 'getPromoCodeClient']);
        Route::post('/add-discount', [DiscountController::class, 'addDiscountCart']);
        Route::get('/discounts', [DiscountController::class, 'getDiscounts']);

        //Order
        Route::post('/confirm-order', [OrderController::class, 'confirmOrder']);
        Route::get('/order-detail/{phone}/{code}', [OrderController::class, 'detail']);
        Route::get('/my-order/{code}', [OrderController::class, 'detailByCode']);

        //PRomotion Product Page Home
        Route::get('/promotion_show', [PromotionController::class, 'getPromotionsForWeb']);

        //Warehouse
        Route::get('/get-location', [WarehouseController::class, 'getLocation']);

});


Route::group([
    'prefix' => 'v1',
    'middleware' => 'api.admin'
], function($router){
    //Theme 
    Route::get('/themes', [ThemeController::class, 'getThemesAdmin']);
    Route::post('/theme', [ThemeController::class, 'save']);
    //Category
    Route::post('/category', [CategoryController::class, 'create']);
    Route::put('/category', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'deleteCategory']);
    //Promotion
    Route::get('/promotions', [PromotionController::class, 'getPromotions']);
    Route::get('/promotion-products', [PromotionController::class, 'getProductPromotions']);
    Route::get('/promotion/{id}', [PromotionController::class, 'getDetail']);
    Route::post('/promotion', [PromotionController::class, 'create']);

    Route::get('/statistics-orders', [OrderController::class, 'statisticsOrders']);
    
    Route::get('/statistics-orders-fits', [OrderController::class, 'statisticsOrdersRevenue']);
    Route::get('/orders', [OrderController::class, 'getAllOrders']);
    Route::get('/my-orders', [OrderController::class, 'getAllOrdersByClient']);

    //product
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/product-detail/{id}', [ProductController::class, 'detailAdmin']);
    Route::post('/product', [ProductController::class, 'create']);

    //Product varian
    Route::get('/product-varian/{id}', [ProductVarianController::class, 'getAllByProduct']);
    Route::post('/product-varian', [ProductVarianController::class, 'update']);
    Route::get('/profile', [UserController::class, 'profile']);

    //Order
    Route::get('/order-detail/{id}', [OrderController::class, 'detailAdmin']);
    Route::put('/order-update', [OrderController::class, 'updateOrder']);
    Route::get('/order-status/{id}', [OrderController::class, 'getStatus']);
    // Router::get('/orders', [OrderController::class, 'getAllOrders']);

    //Coupon (promocode)
    Route::get('/promocodes', [PromoCodeController::class, 'getPromoCode']);
    Route::get('/promo-detail/{id}', [PromoCodeController::class, 'detail']);
    Route::put('/promo-update', [PromoCodeController::class, 'update']);

    //Warehouse
    Route::get('/warehouses', [WarehouseController::class, 'getAllWarehouse']);
    Route::get('/warehouse-detail/{id}', [WarehouseController::class, 'getWarehouseDetail']);
    Route::get('/warehouse-detail-by-warehouse-id/{id}', [WarehouseController::class, 'getWarehouseDetail']);
    Route::get('/warehouse-product-detail/{id}', [WarehouseController::class, 'getWarehouseProductDetail']);
    Route::get('/warehouse-product-detail-by-warehouse-id/{warehouse_id}', [WarehouseController::class, 'getWarehouseProductDetailByWarehouse']);
    Route::post('/warehouse-product-detail', [WarehouseController::class, 'saveWarehouseProductDetail']);


    Route::post('/warehouse', [WarehouseController::class, 'saveWarehouse']);
    
});
// Route::group(['prefix' => 'v0'])->get('/products', [ProductController::class, 'index']);