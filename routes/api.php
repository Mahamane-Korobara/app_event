<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1'); // Limite à 5 tentatives par minute
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


// Fake route pour la confirmation d'email (patch rapide)
Route::get('/login', function () {
    return response()->json(['message' => 'Email vérifié avec succès. Vous pouvez maintenant vous connecter depuis le frontend React.']);
})->name('login');

// Protection des routes
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Vérification email (sans Sanctum)
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // Marque l'email comme vérifié
    return response()->json(['message' => 'Email vérifié avec succès !']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

// Renvoi du lien de vérification
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Lien de vérification envoyé !']);
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');
