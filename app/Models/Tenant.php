<?php

namespace App\Models;

use App\Enums\TenantStatus;
use App\Models\Concerns\HasPublicUuid;
use App\Observers\TenantObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'address', 'phone_number', 'email', 'timezone', 'status'])]
#[ObservedBy([TenantObserver::class])]
class Tenant extends Model
{
    use HasPublicUuid;

    protected function casts(): array
    {
        return [
            'status' => TenantStatus::class,
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function domains(): HasMany
    {
        return $this->hasMany(TenantDomain::class);
    }
}
