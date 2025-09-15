<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ambiance extends Model
{
    protected $fillable = ['nom']; // adapte selon les colonnes de ta table

    public function evenements()
    {
        return $this->belongsToMany(Evenement::class, 'evenement_ambiance');
    }
}
