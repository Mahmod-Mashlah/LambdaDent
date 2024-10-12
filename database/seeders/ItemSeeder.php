<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Subcategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subcategories_ids =  Subcategory::pluck("id");
        foreach ($subcategories_ids as $id) {

            for ($i = 1; $i < 4; $i++) {

                Item::create([

                    'name' => "item $i for subcategory $id",
                    'subcategory_id' => $id,
                    'quantity' => rand(0, 50),
                    'unit_price' => rand(0, 50) * 10000

                ]);
            }
        }
    }
}
