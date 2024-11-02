<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PaymentMethod;
use App\Models\Permission;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@hotmail.com',
            'password' => 'admin',
            'active' => true,
        ]);

        PaymentMethod::factory()->create([
            'name' => 'Carteira',
            'active' => true,
        ]);

    }
}
