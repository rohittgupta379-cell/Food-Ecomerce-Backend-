<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\admin\AdminAuthController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\ProductController;



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('user/register',[UserController::class,'register']);
Route::post('/user/login', [UserController::class, 'authenticate']);
Route::post('/admin/login', [AdminAuthController::class, 'authenticate']);


Route::prefix('admin')->group(function(){
    Route::group(['middleware' => 'auth:sanctum'],function(){
        
        // category
        Route::post('categories', [CategoryController::class, 'store']);
        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('categories/{id}', [CategoryController::class, 'show']);
        Route::put('categories/{id}', [CategoryController::class, 'update']);
        Route::delete('categories/{id}', [CategoryController::class, 'destroy']);

        // dish
        Route::post('products', [ProductController::class, 'store']);
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{id}', [ProductController::class, 'show']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
        
    });
});