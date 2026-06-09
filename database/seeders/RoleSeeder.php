<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Rôles prévus dès la V1, exploités pleinement en V3 (multi-relais).
        foreach (['super_admin', 'admin_relais', 'preparateur', 'lecture_seule'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }
}
