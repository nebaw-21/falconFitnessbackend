<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ChapaController;
use App\Http\Controllers\SubCategoryController;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

 Route::middleware('auth:sanctum')->group(function () {
     
    Route::POST('/user', [UserController::class, 'user']);
    Route::POST('/logout', [UserController::class, 'logout']);
 
});

 // registration and login 
 Route::POST('/register', [UserController::class, 'registration']);
 Route::POST('/login', [UserController::class,'login']);
 Route::PUT('/updateUser/{id}', [UserController::class,'updateUser']);


// category and color
Route::POST('/category', [CategoryController::class, 'addCategory']);
Route::POST('/color', [ColorController::class, 'addColor']);

Route::POST('/displayColor', [ColorController::class, 'displayColor']);
Route::POST('/displayCategory', [CategoryController::class, 'displayCategory']);
Route::POST('/showCategoriesWithProductsForAll/{id}', [CategoryController::class, 'showCategoriesWithProductsForAll']);
Route::POST('/showCategoriesWithProductsForMen', [CategoryController::class, 'showCategoriesWithProductsForMen']);
Route::POST('/showMenCategoryWithSpecificSubCategory/{id}', [ProductController::class, 'showMenCategoryWithSpecificSubCategory']);
Route::POST('/showCategoriesWithProductsForWomen', [CategoryController::class, 'showCategoriesWithProductsForWomen']);
Route::POST('/showWomenCategoryWithSpecificSubCategory/{id}', [ProductController::class, 'showWomenCategoryWithSpecificSubCategory']);
Route::POST('/showCategoriesWithProductsForNutrition', [CategoryController::class, 'showCategoriesWithProductsForNutrition']);
Route::POST('/showNutritionCategoryWithSpecificSubCategory/{id}', [ProductController::class, 'showNutritionCategoryWithSpecificSubCategory']);
Route::POST('/showCategoriesWithProductsForAccessory', [CategoryController::class, 'showCategoriesWithProductsForAccessory']);
Route::POST('/showAccessoryCategoryWithSpecificSubCategory/{id}', [ProductController::class, 'showAccessoryCategoryWithSpecificSubCategory']);
Route::POST('/displaySpecificCategory/{id}', [CategoryController::class, 'displaySpecificCategory']);
Route::POST('/displaySpecificColor/{id}', [ColorController::class, 'displaySpecificColor']);
Route::POST('/updateCategory/{id}', [CategoryController::class, 'updateCategory']);
Route::POST('/updateColor/{id}', [ColorController::class, 'updateColor']);

Route::POST('/displaySubCategory', [SubCategoryController::class, 'displaySubCategory']);
Route::POST('/displaySpecificSubCategory/{id}', [SubCategoryController::class, 'displaySpecificSubCategory']);


//product 
Route::POST('/addProducts', [ProductController::class, 'addProducts']);
Route::POST('/displayProduct', [ProductController::class, 'displayProduct']);
Route::POST('/displayMenProduct', [ProductController::class, 'displayMenProduct']);
Route::POST('/displayWomenProduct', [ProductController::class, 'displayWomenProduct']);
Route::POST('/displayAccessoryProduct', [ProductController::class, 'displayAccessoryProduct']);
Route::POST('/displayNutritionProduct', [ProductController::class, 'displayNutritionProduct']);
Route::POST('/displayRecentProduct', [ProductController::class, 'displayRecentProduct']);
Route::POST('/displaySpecificProduct/{id}', [ProductController::class, 'displaySpecificProduct']);
Route::POST('/displayAllProduct', [ProductController::class, 'displayAllProduct']);
Route::POST('/displayCategory/{id}', [ProductController::class, 'displaySpecificCategoryProducts']);
Route::POST('/updateProduct1/{id}', [ProductController::class, 'updateProduct1']);
Route::POST('/updateProduct2/{id}', [ProductController::class, 'updateProduct2']);
Route::POST('/searchProduct/{key}', [ProductController::class, 'searchProduct']);
Route::POST('/SearchProductForProductTable/{key}', [ProductController::class, 'SearchProductForProductTable']);

//ordered Products

Route::POST('/addOrders', [OrderController::class,'addOrders']);
Route::POST('/userOrderHistory/{id}', [OrderController::class,'userOrderHistory']);
Route::POST('/displayAllOrder', [OrderController::class,'displayAllOrder']);
Route::POST('/orderDetailForAdmin/{id}', [OrderController::class,'orderDetailForAdmin']);
Route::POST('/SearchOrder/{key}', [OrderController::class,'SearchOrder']);
Route::POST('/updateOrderStatus', [OrderController::class,'updateOrderStatus']);


//Forgot and Reset Password 
Route::POST('/forgotPassword', [ForgotPasswordController::class,'forgotPassword']);

//Reset Password 
Route::POST('/resetPassword', [ResetPasswordController::class, 'resetPassword']);

//User
Route::POST('/displayAllUser', [UserController::class, 'displayAllUser']);
Route::delete('/deleteUser/{id}', [UserController::class, 'deleteUser']);
Route::POST('/displaySpecificUser/{id}', [UserController::class, 'displaySpecificUser']);
Route::POST('/addUser', [UserController::class, 'addUser']);
Route::POST('/SearchUser/{key}', [UserController::class, 'SearchUser']);

//chapa controller

Route::POST('/pay', 'App\Http\Controllers\ChapaController@initialize');
