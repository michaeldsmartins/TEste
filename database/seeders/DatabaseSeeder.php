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
        // Create a default seller
        \App\Models\User::updateOrCreate(
            ['email' => 'teste@vendas.com'],
            [
                'name' => 'Vendedor Teste',
                'password' => bcrypt('12345678'),
            ]
        );

        // Create some products
        \App\Models\Product::create(['name' => 'Monitor 24"', 'price' => 1200.00]);
        \App\Models\Product::create(['name' => 'Teclado Mecânico', 'price' => 350.00]);
        \App\Models\Product::create(['name' => 'Mouse Gamer', 'price' => 150.00]);
        \App\Models\Product::create(['name' => 'Headset USB', 'price' => 250.00]);

        // Create some clients
        \App\Models\Client::create(['name' => 'João Silva', 'email' => 'joao@email.com']);
        \App\Models\Client::create(['name' => 'Maria Oliveira', 'email' => 'maria@email.com']);
    }
}
