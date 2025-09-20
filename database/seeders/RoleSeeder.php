<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 1. Buat daftar permission ---
        $permissions = [
            // Users
            'manage users',

            // Employees
            'manage employees',
            'view employees',

            // Attendance
            'view attendance',
            'edit attendance',

            'manage inventory',
            'manage procurement',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // --- 2. Buat roles ---
        $adminRole   = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $staffRole   = Role::firstOrCreate(['name' => 'staff']);

        // --- 3. Assign permission ke roles ---
        $adminRole->syncPermissions(Permission::all());

        $managerRole->syncPermissions([
            'manage employees',
            'view employees',
            'view attendance',
            'manage inventory',
        ]);

        $staffRole->syncPermissions([
            'view attendance',
        ]);

        // --- 4. Buat 1 user default admin ---
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'), // ganti di production!
                'is_active' => true,
            ]
        );

        $adminUser->assignRole('admin');

        // --- 5. Buat Employee record untuk admin ---
        Employee::firstOrCreate(
            ['user_id' => $adminUser->id],
            [
                'nik' => 'ADM001',
                'phone' => '08123456789',
                'address' => 'Head Office',
                'hire_date' => now(),
                'salary_monthly' => 4500000,
                'role' => 'Administrator',
            ]
        );
    }
}