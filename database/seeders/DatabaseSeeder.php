<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        

        // Seeder for category_products
        DB::table('category_products')->insert([
            ['name' => 'Electronics'],
            ['name' => 'Furniture'],
        ]);

        // Seeder for products
        DB::table('products')->insert([
            [
                'product_category_id' => 1,
                'name' => 'Smartphone',
                'price' => 699.99,
                'image' => 'smartphone.jpg',
            ],
            [
                'product_category_id' => 2,
                'name' => 'Sofa',
                'price' => 899.99,
                'image' => 'sofa.jpg',
            ],
        ]);
    }
}
