<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['nom']; // adapte selon ta migration

    public function evenements()
    {
        return $this->belongsToMany(Evenement::class, 'evenement_tag');
    }
}
