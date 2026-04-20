<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Membuat Role sesuai struktur CV. New Citra Indonesia
        $roleSuperadmin = Role::firstOrCreate(['name' => 'Superadmin']);
        $roleAdmin      = Role::firstOrCreate(['name' => 'Admin']);
        $roleProduser   = Role::firstOrCreate(['name' => 'Produser']);
        $roleSales      = Role::firstOrCreate(['name' => 'Sales']);

        // 2. Membuat User Default untuk Superadmin
        $superadmin = User::firstOrCreate(['email' => 'superadmin@newcitra.com'],[
                'name' => 'Superadmin New Citra',
                'password' => Hash::make('password123'),
            ]
        );

        // 3. Memberikan (Assign) role Superadmin ke user tersebut
        $superadmin->assignRole($roleSuperadmin);
    }
}