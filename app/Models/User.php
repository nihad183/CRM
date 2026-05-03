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
        return in_array(strtolower((string) $this->role), ['admin', 'dg'], true);
    }

    public function isEmployee(): bool
    {
        return strtolower((string) $this->role) === 'employee';
    }

    public function canAccessCommercialFeatures(): bool
    {
        return $this->isAdmin() || $this->isEmployee();
    }

    public function roleLabel(): string
    {
        $role = strtolower((string) $this->role);

        return match ($role) {
            'admin' => 'Admin',
            'dg' => 'DG',
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
