<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductFillter;

class fillterSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $product = new ProductFillter();
        $product->datas = ["brand" => ["Xiaomi","Samsung"], "Screen Solution" => ["HD","4K"]];
        $product->type = "tv3";
        $product->save();
    }
}
