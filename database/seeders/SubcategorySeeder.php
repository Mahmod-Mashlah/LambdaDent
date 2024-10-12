<?php

namespace Database\Seeders;

use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($id = 1; $id <= 5; $id++) {
            for ($i = 1; $i <= 3; $i++) {

                Subcategory::create([

                    'name' => "subcategory $i for category $id ",
                    'category_id' => $id

                ]);
            }
        }
    }
}
