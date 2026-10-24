<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Panel;
use App\Models\Concerns\HasPublicUuid;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Laravel\Sanctum\HasApiTokens;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Illuminate\Support\Facades\Storage;
use App\Observers\UserObserver;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

#[Fillable(['name', 'email', 'password', 'avatar_url', 'uuid', 'username'])]
#[Hidden(['password', 'remember_token'])]
#[ObservedBy(UserObserver::class)]
class User extends Authenticatable implements FilamentUser, HasAvatar, HasTenants
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasPublicUuid, HasApiTokens, TwoFactorAuthenticatable;

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

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->hasAnyRole(['super_admin', 'admin']),
            'app'   => $this->tenants()->where('status', 'active')->exists(),
            default => false,
        };
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->tenants()->where('status', 'active')->get();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->tenants->contains($tenant);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::disk('public')->url($this->avatar_url) : null;
    }

    public function canImpersonate(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function canBeImpersonated(): bool
    {
        return ! $this->hasRole('super_admin');
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class)->withTimestamps();
    }
}
