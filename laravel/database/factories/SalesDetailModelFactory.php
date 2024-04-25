<?php

namespace Database\Factories;

use App\Models\ProductModel;
use App\Models\SalesDetailModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesDetailModelFactory extends Factory
{
    protected $model = SalesDetailModel::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $products = $this->getProductsId();

        return [
            'm_product_id' => $this->faker->randomElement($products),
            'price' => $this->faker->numberBetween(5000, 20000),
            'total_item' => $this->faker->numberBetween(1, 3),
        ];
    }

    public function getProductsId()
    {
        $model = new ProductModel();
        $products = $model->get();

        return array_map(function ($product) {
            return $product['id'];
        }, $products->toArray());
    }
}
