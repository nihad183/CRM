<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\LoginHistory;
use App\Models\FichePropose;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'position',
        'role',
        'company',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function ficheProposes(): HasMany
    {
        return $this->hasMany(FichePropose::class);
    }

    public function signedContracts(): HasMany
    {
        return $this->hasMany(FichePropose::class, 'contract_user_id');
    }

    public function isAdmin(): bool
    {
        return in_array($this->normalizedRole(), ['admin', 'dg'], true);
    }

    public function isEmployee(): bool
    {
        return $this->normalizedRole() === 'employee';
    }

    public function isCompliance(): bool
    {
        return in_array($this->normalizedRole(), [
            'responsable_conformite',
            'charge_conformite',
            'analyse_conformite',
        ], true);
    }

    public function canAccessCommercialFeatures(): bool
    {
        return $this->isAdmin() || $this->isEmployee();
    }

    public function canAccessComplianceFeatures(): bool
    {
        return $this->normalizedRole() === 'admin' || $this->isCompliance();
    }

    public function roleLabel(): string
    {
        $role = $this->normalizedRole();

        return match ($role) {
            'admin' => 'Admin',
            'dg' => 'DG',
            'responsable_conformite' => 'Responsable conformite',
            'charge_conformite' => 'Charge conformite',
            'analyse_conformite' => 'Analyse conformite',
            default => 'Employee',
        };
    }

    public function normalizedCompany(): string
    {
        $company = strtolower(trim((string) $this->company));

        return match ($company) {
            'rmgc' => 'rmgc',
            'invest market', 'invest_market', 'invest-market' => 'invest_market',
            default => 'invest_market',
        };
    }

    public function companyLabel(): string
    {
        return $this->normalizedCompany() === 'rmgc' ? 'RMGC' : 'Invest Market';
    }

    public function belongsToCompany(string $company): bool
    {
        return $this->normalizedCompany() === self::normalizeCompanyValue($company);
    }

    public function normalizedRole(): string
    {
        return str_replace([' ', '-'], '_', strtolower(trim((string) $this->role)));
    }

    public function canModifyFiche(FichePropose $fichePropose): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (! $this->isEmployee()) {
            return false;
        }

        return $this->belongsToCompany((string) $fichePropose->user?->company);
    }

    public static function normalizeCompanyValue(?string $company): string
    {
        $value = strtolower(trim((string) $company));

        return match ($value) {
            'rmgc' => 'rmgc',
            'invest market', 'invest_market', 'invest-market' => 'invest_market',
            default => 'invest_market',
        };
    }
}
