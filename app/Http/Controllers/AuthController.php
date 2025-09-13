<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;



class AuthController extends Controller
{
    public function register(RegisterRequest $request){

        // Récupérer les données validées depuis RegisterRequest
        $validateData = $request->validated();

        // Créer un nouvel utilisateur
        $utilisateur = User::create([
            'name' => $validateData['name'],
            'email' => $validateData['email'],
            'password' => Hash::make($validateData['password'])
        ]);

        // Envoyer un email de confirmation d'email
        $utilisateur->sendEmailVerificationNotification();

        // Retourner une réponse JSON avec les details de l'utilisateur créé
        return response()->json([
            'message' => "Utilisateur {$utilisateur->name} créé avec succès vérifier votre email pour la confirmation."
        ]);
    }

    public function login(LoginRequest $request) 
    {
        $infoIdentification = $request->only('email', 'password');

        if (!Auth::attempt($infoIdentification)) {
             Log::warning('Tentative de connexion échouée', [
                'email' => $infoIdentification['email'],
                'ip' => $request->ip(),
            ]);
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        $utilisateur = Auth::user();
        $utilisateur->tokens()->delete(); // Supprimer les anciens tokens avant d'en créer un nouveau
        $token = $utilisateur->createToken('auth_token')->plainTextToken; // Créer un token d'authentification avec Sanctum

        // Journaliser le succès
        Log::info('Connexion réussie', [
            'user_id' => $utilisateur->id,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Connexion réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);

    }

    public function logout(Request $request) 
    {
        $utilisateur = $request->user();
        $utilisateur->currentAccessToken()->delete(); // Supprimer le token actuel

        return response()->json(['message' => 'Déconnexion réussie']);
    }
}
