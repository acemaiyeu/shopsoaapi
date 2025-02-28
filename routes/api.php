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
use App\Http\Controllers\Api\WarrantyController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ImgurController;
use App\Http\Controllers\Api\ImageProxyController;
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
    
    Route::post('login', [AuthController::class, 'login']);
    Route::post('login-admin', [AuthController::class, 'loginAdmin']);
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('profile', [AuthController::class, 'update']);
});

Route::group([
    'prefix' => 'v0'
], function($router){
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
        Route::post('/add-promo-codes', [PromoCodeController::class, 'addPromoCode']);

        //Order
        Route::post('/confirm-order', [OrderController::class, 'confirmOrder']);
        Route::get('/order-detail/{phone}/{code}', [OrderController::class, 'detail']);
        Route::get('/my-order', [OrderController::class, 'myOrder']);
        Route::post('/order/rating', [OrderController::class, 'ratingOrder']);
        //PRomotion Product Page Home
        Route::get('/promotion_show', [PromotionController::class, 'getPromotionsForWeb']);

        //Warehouse
        Route::get('/get-location', [WarehouseController::class, 'getLocation']);
        
        // Route::post('/upload', [UserController::class, 'uploadImage']);
        Route::post('/upload', [UserController::class, 'uploadImage']);
        
        Route::get('/image/{file_name}', [UserController::class, 'getImage']);
        Route::get('/list-file', [UserController::class, 'listFiles']);
        
        
        //Warranty  
        Route::get('/warranty-detail/{code}', [WarrantyController::class, 'getWarrantyByCode']);

        Route::post('/register', [UserController::class, 'register']);
        Route::get('/warranty/{warhouse_id}/{order_id}', [WarrantyController::class, 'getWarrantyByWarhouseAndOrder']);

        //Post
        Route::get('/posts', [PostController::class, 'getAllPost']);
        Route::get('/post/{id}', [PostController::class, 'getDetailPost']);
        Route::post('/post/comment', [PostController::class, 'comment']);
        Route::post('/post/comment-reply', [PostController::class, 'commentReply']);
        

        Route::post('/upload-image', [ImgurController::class, 'uploadImage']);
        Route::get('/proxy-image', [ImageProxyController::class, 'fetchImage']);
    });


Route::group([
    'prefix' => 'v1',
    'middleware' => 'api.admin'
], function($router){
    //Promotion
    Route::middleware(['permission:VIEW-ALL-PROMOTIONS'])->get('/promotions', [PromotionController::class, 'getPromotions']);
    Route::middleware(['permission:CREATE-UPDATE-PROMOTION'])->get('/promotion-products', [PromotionController::class, 'getProductPromotions']);
    Route::middleware(['permission:VIEW-DETAIL-PROMOTION'])->get('/promotion/{id}', [PromotionController::class, 'getDetail']);
    Route::middleware(['permission:CREATE-UPDATE-PROMOTION'])->post('/promotion', [PromotionController::class, 'create']);
    Route::middleware(['permission:DELETE-PROMOTION'])->delete('/promotion/deleted/{id}', [PromotionController::class, 'deleteById']);

    Route::middleware(['permission:STATISTIC-ORDERS'])->get('/statistics-orders', [OrderController::class, 'statisticsOrders']);
    Route::middleware(['permission:VIEW-ALL-ORDERS'])->get('/orders', [OrderController::class, 'getAllOrders']);

    //product
    Route::middleware(['permission:VIEW-ALL-PRODUCTS'])->get('/products', [ProductController::class, 'indexByAdmin']);
    Route::middleware(['permission:VIEW-DETAIL-PRODUCT'])->get('/product-detail/{id}', [ProductController::class, 'detailAdmin']);
    Route::middleware(['permission:CREATE-UPDATE-PRODUCT'])->post('/product', [ProductController::class, 'create']);
    Route::middleware(['permission:DELETE-PRODUCT'])->delete('/product/{id}', [ProductController::class, 'destroy']);
    

    //Product varian
    Route::middleware(['permission:VIEW-ALL-PRODUCT-VARIAN'])->get('/product-varian/{id}', [ProductVarianController::class, 'getAllByProductVarians']);
    // Route::middleware(['permission:CREATE-UPDATE-PRODUCT-VARIAN'])->post('/product-varian', [ProductVarianController::class, 'update']);
    Route::get('/profile', [UserController::class, 'profile']);

    //Order
    Route::middleware(['permission:VIEW-DETAIL-ORDER'])->get('/order-detail/{id}', [OrderController::class, 'detailAdmin']);
    Route::middleware(['permission:UPDATE-DETAIL-ORDER'])->put('/order-update', [OrderController::class, 'updateOrder']);
    Route::middleware(['permission:VIEW-STATUS-ORDER'])->get('/order-status/{id}', [OrderController::class, 'getStatus']);

    //Rating order
    
  
    //Coupon (promocode)
    Route::middleware(['permission:VIEW-ALL-COUPONS'])->get('/promocodes', [PromoCodeController::class, 'getPromoCode']);
    Route::middleware(['permission:VIEW-DETAIL-COUPON'])->get('/promo-detail/{id}', [PromoCodeController::class, 'detail']);
    Route::middleware(['permission:UPDATE-DETAIL-COUPON'])->put('/promo-update', [PromoCodeController::class, 'update']);
    Route::middleware(['permission:CREATE-DETAIL-COUPON'])->post('/coupon', [PromoCodeController::class, 'create']);
    Route::middleware(['permission:DELETE-COUPON'])->delete('/coupon/{id}', [PromoCodeController::class, 'deleteById']);

    //Warehouse
    Route::middleware(['permission:VIEW-ALL-WAREHOUSE'])->get('/warehouses', [WarehouseController::class, 'getAllWarehouse']);
    Route::middleware(['permission:VIEW-DETAIL-WAREHOUSE'])->get('/warehouse-detail/{id}', [WarehouseController::class, 'getWarehouseDetail']);
    Route::middleware(['permission:VIEW-DETAIL-WAREHOUSE'])->get('/warehouse-detail-by-warehouse-id/{id}', [WarehouseController::class, 'getWarehouseDetail']);
    Route::middleware(['permission:VIEW-PRODUCT-ALL-WAREHOUSE'])->get('/warehouse-product-detail/{id}', [WarehouseController::class, 'getWarehouseProductDetail']);
    Route::middleware(['permission:VIEW-PRODUCT-ALL-WAREHOUSE'])->get('/warehouse-product-detail-by-warehouse-id/{warehouse_id}', [WarehouseController::class, 'getWarehouseProductDetailByWarehouse']);
    Route::middleware(['permission:CREATE-UPDATE-PRODUCT-ALL-WAREHOUSE'])->post('/warehouse-product-detail', [WarehouseController::class, 'saveWarehouseProductDetail']);


    Route::middleware(['permission:CREATE-UPDATE-WAREHOUSE'])->post('/warehouse', [WarehouseController::class, 'saveWarehouse']);




    //Warranty
    Route::middleware(['permission:VIEW-DETAIL-WARRANTY'])->get('/warranty/{warhouse_id}/{order_id}', [WarrantyController::class, 'getWarrantyByWarhouseAndOrder']);
    Route::middleware(['permission:CREATE-UPDATE-WARRANTY'])->post('/order-warranty', [WarrantyController::class, 'updateOrderWarranty']);
    Route::middleware(['permission:CREATE-UPDATE-WARRANTY'])->post('/warranty-detail', [WarrantyController::class, 'updateWarrantyDetail']);
    

    //Post
    Route::middleware(['permission:VIEW-ALL-POSTS'])->get('/posts', [PostController::class, 'getAllPostForAdmin']);
    Route::middleware(['permission:VIEW-DETAIL-POST'])->get('/post/{id}', [PostController::class, 'getDetailPostForAdmin']);
    Route::middleware(['permission:CREATE-UPDATE-POST'])->post('/post', [PostController::class, 'savePost']);
    Route::middleware(['permission:DELETE-POST'])->delete('/post/{id}', [PostController::class, 'deleteById']);


    //Category
    Route::middleware(['permission:VIEW-ALL-CATEGORYS'])->get('/categories', [CategoryController::class, 'getAllCategoryForAdmin']);
    Route::middleware(['permission:VIEW-DETAIL-CATEGORY'])->get('/category/{id}', [CategoryController::class, 'getDetailCategoryForAdmin']);
    Route::middleware(['permission:DELETE-CATEGORY'])->delete('/category/{id}', [CategoryController::class, 'deleteByCode']);
    Route::middleware(['permission:CREATE-UPDATE-CATEGORY'])->post('/category', [CategoryController::class, 'saveCategory']);


    //
    //Permision
    
    Route::middleware(['permission:VIEW-ALL-PERMISSION'])->get('/permissions', [PermissionController::class, 'getPermission']);
    Route::middleware(['permission:CREATE-UPDATE-PERMISSION'])->post('/permission', [PermissionController::class, 'savePermission']);
    Route::middleware(['permission:CREATE-UPDATE-USER-PERMISSION'])->post('/permission-detail', [PermissionController::class, 'savePermissionDetail']);
    Route::middleware(['permission:VIEW-USER-PERMISSION'])->get('/users-permission', [PermissionController::class, 'getUserPermission']);
    Route::middleware(['permission:VIEW-ALL-ROLES'])->get('/roles', [PermissionController::class, 'getAllRole']);
    
    
    //Role
    Route::middleware(['permission:CREATE-UPDATE-ROLE'])->post('/role', [RoleController::class, 'saveRole']);
    Route::middleware(['permission:DELETE-ROLE'])->delete('/role/{code}', [RoleController::class, 'deleteByCode']);
    


    // User
    Route::middleware(['permission:CHANGE-USER-PASSWORD'])->post('/user/changepassword', [UserController::class, 'changePassword']);

    
});
// Route::group(['prefix' => 'v0'])->get('/products', [ProductController::class, 'index']);


