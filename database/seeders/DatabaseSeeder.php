<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Roles
        $adminRole = \Spatie\Permission\Models\Role::create(['name' => 'Admin']);
        $teacherRole = \Spatie\Permission\Models\Role::create(['name' => 'Teacher']);

        // Create Admin User
        $admin = User::firstOrCreate([
            'email' => 'admin@sgms.com',
        ], [
            'name' => 'System administrator',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'phone_number' => '09123456789',
        ]);
        $admin->assignRole($adminRole);

        // Create a Sample Teacher
        $teacher = User::firstOrCreate([
            'email' => 'teacher@sgms.com',
        ], [
            'name' => 'Sample Teacher',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'phone_number' => '09987654321',
        ]);
        $teacher->assignRole($teacherRole);

        // Create a Sample Grading Period
        \App\Models\GradingPeriod::create([
            'name' => '1st Quarter 2025',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->addMonths(2)->endOfMonth(),
            'is_active' => true,
        ]);
    }
}
