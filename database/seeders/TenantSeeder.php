<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    private const DEFAULTS = ['timezone' => 'Asia/Jakarta', 'status' => 'active'];

    private const TENANTS = [
        [
            'slug'          => 'tenant-1',
            'name'          => 'Tenant 1',
            'address'       => 'Jl. Merdeka No. 1, Jambi',
            'phone_number'  => '6281200000001',
            'email'         => 'tenant1@example.com',
        ],
        [
            'slug'          => 'tenant-2',
            'name'          => 'Tenant 2',
            'address'       => 'Jl. Sudirman No. 2, Jambi',
            'phone_number'  => '6281200000002',
            'email'         => 'tenant2@example.com',
        ],
        [
            'slug'          => 'tenant-3',
            'name'          => 'Tenant 3',
            'address'       => 'Jl. Gatot Subroto No. 3, Jambi',
            'phone_number'  => '6281200000003',
            'email'         => 'tenant3@example.com',
        ],
    ];

    public function run(): void
    {
        foreach (self::TENANTS as $tenant) {
            Tenant::updateOrCreate(
                ['slug' => $tenant['slug']],
                $tenant + self::DEFAULTS
            );
        }
    }
}
