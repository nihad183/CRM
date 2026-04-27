<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FicheProposeResume extends Model
{
    protected $fillable = [
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
