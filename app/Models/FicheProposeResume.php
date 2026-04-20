<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FicheProposeResume extends Model
{
    protected $fillable = [
        'fiche_propose_id',
        'user_id',
        'titre',
        'resume',
    ];

    public function fichePropose(): BelongsTo
    {
        return $this->belongsTo(FichePropose::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
