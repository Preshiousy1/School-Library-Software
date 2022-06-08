<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User::truncate();
        Admin::create([
            'username' => env('ADMIN_USERNAME', 'admin'),
            'password' => bcrypt(env('ADMIN_PASSWORD', 'password')),
        ]);
    }
}
