<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get or create admin role
        $adminRole = Role::firstOrCreate(['nama_role' => 'admin']);
        
        // Create admin user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nama' => 'Administrator',
                'password' => Hash::make('admin123'), // Change this in production!
                'role_id' => $adminRole->id,
                'api_token' => Str::random(60),
                'token_expires_at' => now()->addDays(30),
            ]
        );
    }
}