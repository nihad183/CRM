<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FichePropose extends Model
{
    protected $fillable = [
        'user_id',
        'titre',
        'nom_entreprise',
        'secteur_activite',
        'adresse',
        'resume',
        'is_fiche_client',
        'converted_to_client_at',
        'piece_jointe_path',
        'piece_jointe_original_name',
        'n_rc',
        'n_rc_piece_path',
        'n_rc_piece_original_name',
        'nif',
        'nif_piece_path',
        'nif_piece_original_name',
        'nis',
        'nis_piece_path',
        'nis_piece_original_name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(FicheProposeContact::class);
    }

    public function resumes(): HasMany
    {
        return $this->hasMany(FicheProposeResume::class)->latest();
    }

    public function hasCompleteClientDocuments(): bool
    {
        return filled($this->n_rc)
            && filled($this->n_rc_piece_path)
            && filled($this->nif)
            && filled($this->nif_piece_path)
            && filled($this->nis)
            && filled($this->nis_piece_path);
    }

    protected function casts(): array
    {
        return [
            'converted_to_client_at' => 'datetime',
        ];
    }
}
