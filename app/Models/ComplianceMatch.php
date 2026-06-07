<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplianceMatch extends Model
{
    protected $fillable = [
        'compliance_upload_id',
        'compliance_entry_id',
        'matched_entry_id',
        'fiche_propose_id',
        'matched_fiche_propose_id',
        'ref_dossier',
        'nom_dossier',
        'matched_name',
        'match_ratio',
        'matched_information',
        'decision_status',
        'decided_by',
        'decided_at',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'match_ratio' => 'decimal:2',
            'matched_information' => 'array',
            'decided_at' => 'datetime',
        ];
    }

    public function upload(): BelongsTo
    {
        return $this->belongsTo(ComplianceUpload::class, 'compliance_upload_id');
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(ComplianceEntry::class, 'compliance_entry_id');
    }

    public function matchedEntry(): BelongsTo
    {
        return $this->belongsTo(ComplianceEntry::class, 'matched_entry_id');
    }

    public function fichePropose(): BelongsTo
    {
        return $this->belongsTo(FichePropose::class);
    }

    public function matchedFichePropose(): BelongsTo
    {
        return $this->belongsTo(FichePropose::class, 'matched_fiche_propose_id');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
