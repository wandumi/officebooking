<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\Category;
use App\Models\Location;
use Faker\Factory as Faker;
use App\Models\OfficePricing;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $commonOfficeCounter = 1;
        $dedicatedDeskCounter = 1;

        foreach (range(1, 10) as $index) {
            $officeType = $index % 2 == 0 ? 'Dedicated desk' : 'Closed office';  

            if ($officeType == 'Closed office') {
                $name = 'Closed office ' . $commonOfficeCounter++;
            } else {
                $name = 'Dedicated desk ' . $dedicatedDeskCounter++;
            }
            
            Office::create([
                'location_id' => Location::inRandomOrder()->first()->id, 
                'category_id' => Category::inRandomOrder()->first()->id, 
                'seats' => $faker->optional()->numberBetween(1, 10), 
                'monthly_rate' => $faker->numberBetween(1000, 3000), 
                'daily_rate' => $faker->optional()->numberBetween(100, 500), 
                'office_name' => $name,
            ]);
        }
    }
}
