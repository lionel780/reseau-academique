<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;

Route::get('/', function () {
    return view('welcome');
});

// Route de test pour vérifier si le serveur fonctionne
Route::get('/test', function () {
    return response()->json(['message' => 'Le serveur fonctionne correctement!']);
});

// Routes d'authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées
Route::middleware('auth:api')->group(function () {
    // Profile
    Route::get('/profile', [AuthController::class, 'profile']);
    
    // Messagerie
    Route::get('/conversations', [MessageController::class, 'getConversations']);
    Route::get('/messages/{userId}', [MessageController::class, 'getMessages']);
    Route::post('/messages', [MessageController::class, 'sendMessage']);
    Route::patch('/messages/{messageId}/read', [MessageController::class, 'markAsRead']);
});
