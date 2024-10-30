<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\AdministrateurController;
use App\Http\Controllers\MaladieChroniqueController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\PlanNutritionnelController;
use App\Http\Controllers\ReponseController;
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
/*Route::middleware(['cors'])->group(function () {
    Route::get('/api/resource', 'ResourceController@index');
});*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
   
// });
Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('/user_infos/maladie-chronique', [MaladieChroniqueController::class, 'store']);
});
Route::middleware('checkadmin')->group(function () {
    Route::delete('/utilisateurs/{id}', [UtilisateurController::class, 'destroy']);
});
/*Route::middleware(['validate.maladie.chronique'])->group(function () {
    Route::post('/maladie-chronique/update', [MaladieChroniqueController::class, 'update']);
});*/
// Route::post('/logout', function () {
//     return app(\App\Http\Middleware\LogoutMiddleware::class)->handle(request(), function () {
//         return response()->json([
//             'success' => true,
//             'message' => 'Déconnexion réussie',
//         ], 200);
//     });
// })->middleware('auth:sanctum'); // Assurez-vous que l'utilisateur est authentifié
// Route::middleware('identify.comment.author')->get('/commentaire/{id}', [CommentaireController::class, 'show']);
// Route::middleware('check.admin')->group(function () {
//     Route::get('/commentaires', [CommentaireController::class, 'index']);
// });
Route::get('/user/responses', [ReponseController::class, 'index'])->middleware('user.responses');
Route::post('/reponses', [ReponseController::class, 'store'])->middleware('validate.reponse');

Route::put('/user/{id}/update-maladie-chronique', [UtilisateurController::class, 'updateMaladieChronique']);
/*Route::post('/obesity', function (Request $request) {
    // Traitement de la requête ici
    return response()->json([
        'success' => true,
        'message' => 'Type d\'obésité valide',
    ]);
})->middleware('validate.maladie.chronique');*/


/*Route::prefix('maladies-chroniques')->group(function () {
    Route::get('/', [MaladieChroniqueController::class, 'index']); // Liste des maladies chroniques
    Route::get('/{id}', [MaladieChroniqueController::class, 'show']); // Détails d'une maladie chronique
    Route::post('/', [MaladieChroniqueController::class, 'store']); // Enregistrer une nouvelle maladie chronique
    Route::put('/{id}', [MaladieChroniqueController::class, 'update']); // Mettre à jour une maladie chronique
    // Optionnel : Route pour supprimer une maladie chronique
    Route::delete('/{id}', [MaladieChroniqueController::class, 'destroy']); // Supprimer une maladie chronique
});*/
Route::middleware(['validate.maladie.chronique'])->group(function () {
    Route::post('/maladie-chronique', [MaladieChroniqueController::class, 'store']);
});

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::post('register', [AuthController::class, 'register']);


// Route pour l'inscription
Route::post('/register', [UtilisateurController::class, 'store'])->name('utilisateur.store')->middleware('utilisateur.validation');

// Route pour la connexion
// Route::post('/login', [UtilisateurController::class, 'login'])->name('utilisateur.login')->middleware('utilisateur.validation');

// Route::post('login', [AuthController::class, 'login']);
// Route::post('register', [AuthController::class, 'register']);
