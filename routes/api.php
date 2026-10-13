<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\admin\AdminAuthController;



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('user/register',[UserController::class,'register']);
Route::post('/user/login', [UserController::class, 'authenticate']);
Route::post('/admin/login', [AdminAuthController::class, 'authenticate']);