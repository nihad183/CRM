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
        'client_conversion_status',
        'piece_jointe_uploaded_by',
        'piece_jointe_uploaded_at',
        'conversion_reviewed_by',
        'conversion_reviewed_at',
        'contract_amount',
        'contract_signed_at',
        'contract_user_id',
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
