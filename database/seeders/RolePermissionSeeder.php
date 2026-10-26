<?php

namespace Database\Seeders;

use Filament\Facades\Filament;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Process;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    private const GUARD = 'web';
    private const ROLES = ['super_admin', 'admin', 'owner'];

    public function run(): void
    {
        $registrar = app(PermissionRegistrar::class);

        $registrar->setPermissionsTeamId(null);
        $registrar->forgetCachedPermissions();

        $this->generatePermissions();
        $this->createRoles();

        $registrar->forgetCachedPermissions();
    }

    private function generatePermissions(): void
    {
        foreach (array_keys(Filament::getPanels()) as $panel) {
            Process::path(base_path())
                ->timeout(120)
                ->run([
                    PHP_BINARY,
                    'artisan',
                    'shield:generate',
                    '--all',
                    "--panel={$panel}",
                    '--no-interaction',
                ])
                ->throw();
        }
    }

    private function createRoles(): void
    {
        foreach (self::ROLES as $name) {
            Role::findOrCreate($name, self::GUARD);
        }
    }
}
