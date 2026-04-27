<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FichePropose extends Model
{
    protected $fillable = [
        'titre',
        'nom_entreprise',
        'secteur_activite',
        'adresse',
        'resume',
        'n_rc',
        'nif',
        'nis',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pieceJointeUploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'piece_jointe_uploaded_by');
    }

    public function contractUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contract_user_id');
    }

    public function conversionReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conversion_reviewed_by');
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
            'piece_jointe_uploaded_at' => 'datetime',
            'conversion_reviewed_at' => 'datetime',
            'contract_signed_at' => 'date',
            'contract_amount' => 'decimal:2',
        ];
    }

    public function hasPendingClientConversionRequest(): bool
    {
        return $this->client_conversion_status === 'pending';
    }

    public function hasRejectedClientConversionRequest(): bool
    {
        return $this->client_conversion_status === 'rejected';
    }
}
