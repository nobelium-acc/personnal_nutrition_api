<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\AdministrateurController;
use App\Http\Controllers\MaladieChroniqueController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\PlanNutritionnelController;
use App\Http\Controllers\ReponseController;
use App\Http\Controllers\NutritionController;
use App\Http\Controllers\AuthController;

use App\Http\Middleware\AdminMiddleware;

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

Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('/user_infos/maladie-chronique', [MaladieChroniqueController::class, 'store']);
    Route::get('/user-infos/get-user-questions', [QuestionController::class, 'index']);
    Route::get('/user-infos/get-user-answers', [QuestionController::class, 'list_answers']);
    Route::post('/user-infos/set-user-answer', [QuestionController::class, 'store']);
    Route::middleware('is_admin')->group(function() {
        Route::get('admin/users/get-list', [AdminController::class, 'get_users_list']);
        Route::post('admin/users/delete/{identifier}', [AdminController::class, 'delete_user']);
    });
});
Route::middleware('checkadmin')->group(function () {
    Route::delete('/utilisateurs/{id}', [UtilisateurController::class, 'destroy']);
});

Route::get('/reponses', [ReponseController::class, 'index'])->middleware('user.responses');
Route::post('/reponses', [ReponseController::class, 'store'])->middleware('validate.reponse');
Route::get('/reponses/{id}', [ReponseController::class, 'show']);
Route::put('/reponses/{id}', [ReponseController::class, 'update']);
Route::delete('/reponses/{id}', [ReponseController::class, 'destroy']);
Route::post('/nutrition/calculate', [NutritionController::class, 'calculate']);
Route::post('/nutrition/recommendation', [NutritionController::class, 'recommendation']);

Route::put('/user/{id}/update-maladie-chronique', [UtilisateurController::class, 'updateMaladieChronique']);

Route::middleware(['validate.maladie.chronique'])->group(function () {
    Route::post('/maladie-chronique', [MaladieChroniqueController::class, 'store']);
});

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::post('register', [AuthController::class, 'register']);


Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('auth/forgot-password/verify', [AuthController::class, 'verifyResetCode']);
Route::post('auth/reset-password', [AuthController::class, 'resetUserPassword']);

Route::post('/register', [UtilisateurController::class, 'store'])->name('utilisateur.store')->middleware('utilisateur.validation');



