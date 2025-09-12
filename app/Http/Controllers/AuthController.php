<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

        // Retourner une réponse JSON avec les details de l'utilisateur créé
        return response()->json([
            'message' => "Utilisateur {$utilisateur->name} créé avec succès"
        ]);
    }
}
