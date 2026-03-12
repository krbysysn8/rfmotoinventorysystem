<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['username' => 'admin'],
            [
                'full_name'     => 'Administrator',
                'username'      => 'admin',
                'password_hash' => Hash::make('admin123'),
                'role_id'       => 1,
                'status'        => 'active',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['username' => 'staff'],
            [
                'full_name'     => 'Staff User',
                'username'      => 'staff',
                'password_hash' => Hash::make('staff123'),
                'role_id'       => 2,
                'status'        => 'active',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]
        );

        $this->call(RFMotoProductSeeder::class);
    }
}