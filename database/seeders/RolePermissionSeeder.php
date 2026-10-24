<?php

namespace Database\Seeders;

use Filament\Facades\Filament;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    private const GUARD            = 'web';
    private const SUPER_ADMIN_ROLE = 'super_admin';
    private const SEPARATOR        = ':';

    private const READ_ONLY     = ['ViewAny', 'View'];
    private const STANDARD_CRUD = ['ViewAny', 'View', 'Create', 'Update', 'Delete'];

    private const ROLE_PERMISSIONS = [
        'admin' => [
            ['resources' => ['User'],   'actions' => self::STANDARD_CRUD],
            ['resources' => ['Tenant'], 'actions' => self::READ_ONLY],
        ],
        'owner' => [
            [
                'resources' => ['Tenant'],
                'actions'   => ['Create', 'Update', 'Delete'],
            ],
            [
                'resources' => [],
                'actions'   => self::STANDARD_CRUD,
            ],
        ],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->generatePermissions();
        $this->setupRoles();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function generatePermissions(): void
    {
        foreach (array_keys(Filament::getPanels()) as $panel) {
            Artisan::call('shield:generate', [
                '--all'            => true,
                '--panel'          => $panel,
                '--no-interaction' => true,
            ]);
        }
    }

    private function setupRoles(): void
    {
        Role::firstOrCreate(['name' => self::SUPER_ADMIN_ROLE, 'guard_name' => self::GUARD]);

        foreach (self::ROLE_PERMISSIONS as $roleName => $rules) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => self::GUARD]);

            $permissions = [];
            foreach ($rules as $rule) {
                $permissions = array_merge(
                    $permissions,
                    $this->permissionsFor($rule['resources'], $rule['actions']),
                );
            }

            $role->syncPermissions(array_unique($permissions));
        }
    }

    private function permissionsFor(array $resources, array $actions): array
    {
        return Permission::where('guard_name', self::GUARD)
            ->pluck('name')
            ->filter(function (string $name) use ($resources, $actions) {
                [$action, $resource] = array_pad(explode(self::SEPARATOR, $name, 2), 2, null);

                return in_array($resource, $resources, true)
                    && in_array($action, $actions, true);
            })
            ->values()
            ->all();
    }
}
