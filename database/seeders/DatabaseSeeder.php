<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles first
        $this->createRoles();
        
        // Create admin user
        $this->createAdminUser();
        
        // Create super admin user
        $this->createSuperAdminUser();
        
        // Create test user
        $this->createTestUser();
    }
    
    /**
     * Create basic roles
     */
    private function createRoles(): void
    {
        // Create admin role if it doesn't exist
        Role::firstOrCreate(['nama_role' => 'admin']);
        
        // Create user role if it doesn't exist
        Role::firstOrCreate(['nama_role' => 'user']);
        
        // Create super_admin role if it doesn't exist
        Role::firstOrCreate(['nama_role' => 'super_admin']);
    }
    
    /**
     * Create admin user
     */
    private function createAdminUser(): void
    {
        // Get admin role
        $adminRole = Role::where('nama_role', 'admin')->first();
        
        if ($adminRole) {
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
    
    /**
     * Create super admin user
     */
    private function createSuperAdminUser(): void
    {
        // Get super_admin role
        $superAdminRole = Role::where('nama_role', 'super_admin')->first();
        
        if ($superAdminRole) {
            User::firstOrCreate(
                ['email' => 'superadmin@example.com'],
                [
                    'nama' => 'Super Administrator',
                    'password' => Hash::make('superadmin123'), // Change this in production!
                    'role_id' => $superAdminRole->id,
                    'api_token' => Str::random(60),
                    'token_expires_at' => now()->addDays(30),
                ]
            );
        }
    }
    
    /**
     * Create test user
     */
    private function createTestUser(): void
    {
        // Get user role
        $userRole = Role::where('nama_role', 'user')->first();
        
        if ($userRole) {
            User::firstOrCreate(
                ['email' => 'test@example.com'],
                [
                    'nama' => 'Test User',
                    'password' => Hash::make('password'),
                    'role_id' => $userRole->id,
                    'api_token' => Str::random(60),
                    'token_expires_at' => now()->addDays(30),
                ]
            );
        }
    }
}