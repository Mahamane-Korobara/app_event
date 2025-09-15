<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemandeCreationEvent extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'type_evenement' => 'required|string|max:100',
            'date_debut' => 'required|date|before:date_fin',
            'date_fin' => 'required|date|after:date_debut',
            'type_acces' => 'required|in:presentiel,en_ligne,hybride',
            'lieu' => 'required_if:type_acces,presentiel,hybride|string|max:255',
            'adresse' => 'required_if:type_acces,presentiel,hybride|string',
            'lien_en_ligne' => 'required_if:type_acces,en_ligne,hybride|url|max:255',
            'capacite' => 'nullable|integer|min:1',
            'type_tarification' => 'nullable|string|max:100',
            'prix' => 'nullable|numeric|min:0',
            'image_principale' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
            'lien_billetterie' => 'nullable|url|max:255',
            //Relations pivot
            'ambiances' => 'required|array|min:1',
            'ambiances.*' => 'integer|exists:ambiances,id',
            'tags' => 'required|array|min:1',
            'tags.*' => 'integer|exists:tags,id'
        ];
    }

    public function messages(): array 
    {
        return [
            'nom.required' => 'Le nom de l\'événement est obligatoire.',
            'nom.string' => 'Le nom de l\'événement doit être une chaîne de caractères.',
            'nom.max' => 'Le nom de l\'événement ne peut pas dépasser 255 caractères.',

            'description.required' => 'La description de l\'événement est obligatoire.',
            'description.string' => 'La description doit être une chaîne de caractères.',

            'type_evenement.required' => 'Le type d\'événement est obligatoire.',
            'type_evenement.string' => 'Le type d\'événement doit être une chaîne de caractères.',
            'type_evenement.max' => 'Le type d\'événement ne peut pas dépasser 100 caractères.',

            'date_debut.required' => 'La date de début est obligatoire.',
            'date_debut.before' => 'La date de début doit être avant la date de fin.',
            'date_fin.required' => 'La date de fin est obligatoire.',
            'date_fin.after' => 'La date de fin doit être après la date de début.',

            'type_acces.required' => 'Le type d\'accès est obligatoire.',
            'type_acces.in' => 'Le type d\'accès doit être "presentiel", "en_ligne" ou "hybride".',

            'lieu.required_if' => 'Le lieu est obligatoire pour les événements en présentiel ou hybrides.',
            'lieu.max' => 'Le lieu ne peut pas dépasser 255 caractères.',

            'adresse.required_if' => 'L\'adresse est obligatoire pour les événements en présentiel ou hybrides.',

            'lien_en_ligne.required_if' => 'Le lien en ligne est obligatoire pour les événements en ligne ou hybrides.',
            'lien_en_ligne.url' => 'Le lien en ligne doit être une URL valide.',
            'lien_en_ligne.max' => 'Le lien en ligne ne peut pas dépasser 255 caractères.',

            'capacite.integer' => 'La capacité doit être un nombre entier.',
            'capacite.min' => 'La capacité doit être au moins de 1.',

            'type_tarification.string' => 'Le type de tarification doit être une chaîne de caractères.',
            'type_tarification.max' => 'Le type de tarification ne peut pas dépasser 100 caractères.',

            'prix.numeric' => 'Le prix doit être un nombre valide.',
            'prix.min' => 'Le prix ne peut pas être négatif.',

            'image_principale.required' => 'L\'image principale est obligatoire.',
            'image_principale.image' => 'L\'image principale doit être un fichier image.',
            'image_principale.mimes' => 'L\'image principale doit être un fichier de type :values.',
            'image_principale.max' => 'L\'image principale ne peut pas dépasser 2MB.',

            'lien_billetterie.url' => 'Le lien de billetterie doit être une URL valide.',
            'lien_billetterie.max' => 'Le lien de billetterie ne peut pas dépasser 255 caractères.',

            'ambiances.required' => 'Au moins une ambiance doit être sélectionnée.',
            'ambiances.array' => 'Les ambiances doivent être un tableau.',
            'ambiances.*.integer' => 'Chaque ambiance doit être un identifiant entier.',
            'ambiances.*.exists' => 'L\'ambiance sélectionnée est invalide.',

            'tags.required' => 'Au moins un tag doit être sélectionné.',
            'tags.array' => 'Les tags doivent être un tableau.',
            'tags.*.integer' => 'Chaque tag doit être un identifiant entier.',
            'tags.*.exists' => 'Le tag sélectionné est invalide.',
        ];
    }

}
