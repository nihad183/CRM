<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComplianceUpload extends Model
{
    protected $fillable = [
        'original_name',
        'path',
        'uploaded_by',
        'entries_count',
        'filtering_result',
        'best_match_ratio',
        'filtered_at',
    ];

    protected function casts(): array
    {
        return [
            'best_match_ratio' => 'decimal:2',
            'filtered_at' => 'datetime',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ComplianceEntry::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(ComplianceMatch::class);
    }
}
