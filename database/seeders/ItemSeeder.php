<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Item::create([
            'name' => 'Sample Item_1',
            'measurement_unit' => 'KG',
            'quantity' => 100,
            'image_path'=> 'sample-item.jpg',
        ]);
          Item::create([
            'name' => 'Sample Item_2',
            'measurement_unit' => 'M',
            'quantity' => 500,
            'image_path'=> '',
        ]);
          Item::create([
            'name' => 'Sample Item_3',
            'measurement_unit' => 'packs',
            'quantity' => 100,
            'image_path'=> 'sample-item3.jpg',
        ]);
  Item::create([
            'name' => 'Sample Item_4',
            'measurement_unit' => 'Ton',
            'quantity' => 1000,
            'image_path'=> 'sample-item4.jpg',
        ]);
    }
}
