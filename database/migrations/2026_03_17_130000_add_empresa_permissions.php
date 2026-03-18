<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'empresa-list',
            'empresa-create',
            'empresa-edit',
            'empresa-solicitud-list',
            'empresa-solicitud-aprobar',
            'empresa-solicitud-rechazar',
            'empresa-solicitud-history',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $adminRole = Role::where('name', 'admin')->first();
        $vendedorRole = Role::where('name', 'vendedor')->first();

        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        if ($vendedorRole) {
            $vendedorRole->givePermissionTo([
                'empresa-list',
                'empresa-create',
                'empresa-edit',
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'empresa-list',
            'empresa-create',
            'empresa-edit',
            'empresa-solicitud-list',
            'empresa-solicitud-aprobar',
            'empresa-solicitud-rechazar',
            'empresa-solicitud-history',
        ];

        $adminRole = Role::where('name', 'admin')->first();
        $vendedorRole = Role::where('name', 'vendedor')->first();

        if ($adminRole) {
            $adminRole->revokePermissionTo($permissions);
        }

        if ($vendedorRole) {
            $vendedorRole->revokePermissionTo([
                'empresa-list',
                'empresa-create',
                'empresa-edit',
            ]);
        }

        Permission::whereIn('name', $permissions)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
