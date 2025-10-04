<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),  // random product name
            'description' => $this->faker->sentence(10),
            'price' => $this->faker->randomFloat(2, 50, 5000), // 50–5000 range
            'stock' => $this->faker->numberBetween(0, 500),
            'sku' => strtoupper($this->faker->unique()->bothify('SKU-####')),
            'thmnal' => $this->faker->imageUrl(200, 200, 'products', true), // fake thumbnail
            'status' => $this->faker->boolean(80), // 80% chance true
        ];
    }
}
