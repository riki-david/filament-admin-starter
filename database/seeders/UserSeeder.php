<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    private const PASSWORD = 'password';

    private const USERS = [
        [
            'username' => 'superadmin',
            'name'     => 'Super Admin',
            'role'     => 'super_admin',
        ],
        [
            'username' => 'admin',
            'name'     => 'Admin',
            'role'     => 'admin',
        ],
        [
            'username' => 'owner',
            'name'     => 'Owner',
            'role'     => 'owner',
        ],
    ];

    public function run(): void
    {
        foreach (self::USERS as $item) {
            $user = User::updateOrCreate(
                ['username' => $item['username']],
                [
                    'name'     => $item['name'],
                    'email'    => $item['username'] . '@email.com',
                    'password' => self::PASSWORD
                ],
            );

            $user->syncRoles($item['role']);
        }
    }
}
