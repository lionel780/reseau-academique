<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/newsfeed', [Postcontroller::class, 'newsfeed']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->get('/profile', [ProfileController::class, 'show']);
Route::middleware('auth:sanctum')->put('/profile', [ProfileController::class, 'update']);

// Routes du dashboard administrateur (protégées)
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    // Statistiques
    Route::get('/stats', [AdminController::class, 'stats']);
    
    // Gestion des utilisateurs
    Route::get('/users', [AdminController::class, 'users']);
    Route::post('/users', [AdminController::class, 'createUser']);
    Route::put('/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
    
    // Gestion des départements
    Route::get('/departements', [AdminController::class, 'departements']);
    Route::post('/departements', [AdminController::class, 'createDepartement']);
    
    // Gestion des filières
    Route::get('/filieres', [AdminController::class, 'filieres']);
    Route::post('/filieres', [AdminController::class, 'createFiliere']);
    
    // Gestion des groupes
    Route::get('/groupes', [AdminController::class, 'groupes']);
    Route::post('/groupes', [AdminController::class, 'createGroupe']);
    Route::delete('/groupes/{id}', [AdminController::class, 'deleteGroupe']);
    Route::post('/groupes/{id}/affecter-etudiants', [AdminController::class, 'affecterEtudiants']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/messages', [MessageController::class, 'store']);
    Route::get('/messages/conversation/{userId}', [MessageController::class, 'conversation']);
    Route::get('/messages/group/{groupId}', [MessageController::class, 'groupConversation']);
    Route::get('/messages/recents', [MessageController::class, 'recentContacts']);
    // Lister tous les utilisateurs (étudiants et enseignants)
    Route::get('/users/all', [AdminController::class, 'allUsers']);
    // Lister tous les groupes
    Route::get('/groupes/all', [AdminController::class, 'allGroupes']);
    // Newsfeed
    Route::get('/newsfeed', [PostController::class, 'index']);
    Route::post('/newsfeed', [PostController::class, 'store']);
    Route::get('/newsfeed/{id}', [PostController::class, 'show']);
    Route::delete('/newsfeed/{id}', [PostController::class, 'destroy']);
});

?>