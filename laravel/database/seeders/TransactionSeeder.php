<?php

namespace Database\Seeders;

use App\Models\SalesDetailModel;
use App\Models\SalesModel;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        SalesModel::factory()
            ->count(500)
            ->create()
            ->each(function ($sales) {
                SalesDetailModel::factory()->count(2)->create([
                    't_sales_id' => $sales->id
                ]);
            });
    }
}