<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin role if it doesn't exist
        Role::firstOrCreate(['nama_role' => 'admin']);
        
        // Create user role if it doesn't exist
        Role::firstOrCreate(['nama_role' => 'user']);

        // Create user role if it doesn't exist
        Role::firstOrCreate(['nama_role' => 'superadmin']);
    }
}