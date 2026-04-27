<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FicheProposeContact extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'tel',
        'email',
        'poste',
    ];

    public function fichePropose(): BelongsTo
    {
        return $this->belongsTo(FichePropose::class);
    }
}
