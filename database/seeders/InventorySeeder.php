<?php
namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $cat1 = Category::create(['name' => 'Spareparts']);
        $cat2 = Category::create(['name' => 'Consumables']);
        $cat3 = Category::create(['name' => 'Lubricants']);

        Product::create([
            'category_id' => $cat1->id,
            'sku' => 'SPR-001',
            'name' => 'Brake Pad Set',
            'stock' => 50,
            'min_stock' => 10,
            'price' => 250000,
        ]);

        Product::create([
            'category_id' => $cat2->id,
            'sku' => 'CON-001',
            'name' => 'Industrial Rag 1kg',
            'stock' => 5, 
            'min_stock' => 10,
            'price' => 15000,
        ]);

        Product::create([
            'category_id' => $cat3->id,
            'sku' => 'LUB-001',
            'name' => 'Synthetic Oil 5W-30',
            'stock' => 100,
            'min_stock' => 20,
            'price' => 120000,
        ]);
    }
}
