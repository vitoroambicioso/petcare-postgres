<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DenunciaController;
use App\Models\User;
use App\Http\Controllers\TokenController;
/*use App\Http\Controllers\AuthController;*/


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * rotas do usuario
 */
Route::post('/usuario', [UserController::class, 'create'])->name('user.create');
Route::post('/usuario/{id}', [UserController::class, 'getUser'])->name('user.get');
Route::put('/usuario/{id}', [UserController::class, 'edit'])->name('user.edit');
Route::delete('/usuario/{id}', [UserController::class, 'delete'])->name('user.delete');
Route::get('/usuarios', [UserController::class, 'getAllUsers']);
Route::post('/login', [UserController::class, 'login'])->name('user.login');

/**
 * rota de login google
 */
Route::post('/loginGoogle', [UserController::class, 'loginGoogle'])->name('user.loginGoogle');

/**
 * rotas da denuncia
 */
Route::post('/denuncia', [DenunciaController::class, 'create'])->name('denuncia.create');
Route::post('/denuncias', [DenunciaController::class, 'getDenuncia'])->name('denuncia.get');
Route::put('/denuncia/{id}', [DenunciaController::class, 'edit'])->name('denuncia.edit');
Route::delete('/denuncia/{id}', [DenunciaController::class, 'delete'])->name('denuncia.delete');
Route::get('/denuncias', [DenunciaController::class, 'getAllDenuncias']);