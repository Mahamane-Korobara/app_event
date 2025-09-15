<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    // Autoriser l'assignation en masse pour les champs spécifiés
    protected $fillable = [
        'nom',
        'description',
        'type_evenement',
        'date_debut',
        'date_fin',
        'type_acces',
        'lieu',
        'adresse',
        'lien_en_ligne',
        'capacite',
        'type_tarification',
        'prix',
        'image_principale',
        'lien_billetterie',
        'organisateur_id',
    ];

    public function ambiances()
    {
        return $this->belongsToMany(Ambiance::class, 'evenement_ambiance');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'evenement_tag');
    }

    public function organisateur()
    {
        return $this->belongsTo(User::class, 'organisateur_id');
    }

}
