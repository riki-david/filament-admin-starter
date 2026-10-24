<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    private const PASSWORD = 'password';

    private const USERS = [
        ['username' => 'superadmin', 'name' => 'Super Admin', 'role' => 'super_admin', 'tenant' => false],
        ['username' => 'admin',      'name' => 'Admin',       'role' => 'admin',       'tenant' => false],
        ['username' => 'owner',      'name' => 'Owner',       'role' => 'owner',       'tenant' => true],
    ];

    public function run(): void
    {
        $tenantId = Tenant::orderBy('id')->value('id');

        foreach (self::USERS as $u) {
            $user = User::updateOrCreate(
                ['username' => $u['username']],
                [
                    'name'     => $u['name'],
                    'email'    => $u['username'] . '@email.com',
                    'password' => self::PASSWORD
                ],
            );

            $user->syncRoles($u['role']);

            if ($u['tenant']) {
                $user->tenants()->syncWithoutDetaching([$tenantId]);
            }
        }
    }
}
