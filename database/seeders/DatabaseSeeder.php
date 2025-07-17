<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Superadmin',
            'email' => 'superadmin@superadmin.com',
        ]);

        DB::table('roles')->insert([
            ['name' => 'admin'],
            ['name' => 'manager'],
            ['name' => 'staff'],
        ]);

        DB::table('role_user')->insert([
            [
                'role_id' => 1,
                'user_id' => 1,
            ],
        ]);
    }
}
