<?php

namespace App\Http\Controllers;

use App\Http\Requests\DemandeCreationEvent;
use Illuminate\Support\Facades\Auth;
use App\Models\Evenement;

class EventController extends Controller
{
    // Création d'un événement
    public function create(DemandeCreationEvent $request) 
    {
        // Validation des données
        $validateData = $request->validated();

        // Gestion de l'image principale
        if ($request->hasFile('image_principale')) {
            $path = $request->file('image_principale')->store('images', 'public');
            $validateData['image_principale'] = $path;
        }

        // Récupération de l'utilisateur connecté
        $validateData['organisateur_id'] = Auth::id();

        // Création de l'événement
        $evenement = Evenement::create($validateData);

        // Lier les ambiances et les tags fournis
        $evenement->ambiances()->sync($request->input('ambiances', []));
        $evenement->tags()->sync($request->input('tags', []));

        // Retourner une réponse en JSON
        return response()->json([
            'message' => 'Evenement créé avec succès',
        ], 201);
    }
}
