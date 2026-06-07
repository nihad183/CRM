<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComplianceEntry extends Model
{
    protected $fillable = [
        'compliance_upload_id',
        'fiche_propose_id',
        'source_type',
        'person_role',
        'full_name',
        'father_name',
        'mother_name',
        'nationality',
        'birth_date',
        'birth_place',
        'document_number',
        'raw_payload',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'raw_payload' => 'array',
        ];
    }

    public function upload(): BelongsTo
    {
        return $this->belongsTo(ComplianceUpload::class, 'compliance_upload_id');
    }

    public function fichePropose(): BelongsTo
    {
        return $this->belongsTo(FichePropose::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(ComplianceMatch::class);
    }
}
