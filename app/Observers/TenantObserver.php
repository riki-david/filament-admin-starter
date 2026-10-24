<?php

namespace App\Observers;

use App\Models\Tenant;
use Illuminate\Support\Str;

class TenantObserver
{
    public function creating(Tenant $tenant): void
    {
        if (blank($tenant->slug)) {
            $tenant->slug = $this->generateUniqueSlug($tenant->name);
        }
    }

    private function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'tenant';
        $slug = $base;
        $i = 2;

        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
