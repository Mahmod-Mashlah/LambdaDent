<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $default_categories = [
            "Zirconia Blanks",
            "Creramic Powders",
            "NP Metals",
            "Gypsum",
            "Acrylic Powders"
        ];
        foreach ($default_categories as $category) {

            Category::create([

                'name' => $category

            ]);
        }
    }
}
