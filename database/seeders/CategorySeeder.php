<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        if (Category::count() < 3) {
            Category::create(['name' => 'Dedicated Desk']);
            Category::create(['name' => 'Closed Office']);
            Category::create(['name' => 'Virtual Office']);
        }
    }
}
