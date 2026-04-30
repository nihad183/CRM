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
        return $this->role === 'employee';
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
}
